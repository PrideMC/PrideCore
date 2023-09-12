<?php

/*
 *
 *       _____      _     _      __  __  _____
 *      |  __ \    (_)   | |    |  \/  |/ ____|
 *      | |__) | __ _  __| | ___| \  / | |
 *      |  ___/ '__| |/ _` |/ _ \ |\/| | |
 *      | |   | |  | | (_| |  __/ |  | | |____
 *      |_|   |_|  |_|\__,_|\___|_|  |_|\_____|
 *            A minecraft bedrock server.
 *
 *      This project and it’s contents within
 *     are copyrighted and trademarked property
 *   of PrideMC Network. No part of this project or
 *    artwork may be reproduced by any means or in
 *   any form whatsoever without written permission.
 *
 *  Copyright © PrideMC Network - All Rights Reserved
 *                     Season #5
 *
 *  www.mcpride.tk                 github.com/PrideMC
 *  twitter.com/PrideMC         youtube.com/c/PrideMC
 *  discord.gg/PrideMC           facebook.com/PrideMC
 *               bit.ly/JoinInPrideMC
 *  #PrideGames                           #PrideMonth
 *
 */

declare(strict_types=1);

namespace poggit\libasynql\base;

use InvalidArgumentException;
use pmmp\thread\Thread as NativeThread;
use pocketmine\snooze\SleeperHandlerEntry;
use pocketmine\thread\Thread;
use poggit\libasynql\SqlError;
use poggit\libasynql\SqlResult;
use poggit\libasynql\SqlThread;
use function is_string;
use function unserialize;

abstract class SqlSlaveThread extends Thread implements SqlThread{
	private SleeperHandlerEntry $sleeperEntry;

	private static $nextSlaveNumber = 0;

	protected $slaveNumber;
	protected $bufferSend;
	protected $bufferRecv;
	protected $connCreated = false;
	protected $connError;
	protected $busy = false;

	protected function __construct(SleeperHandlerEntry $entry, QuerySendQueue $bufferSend = null, QueryRecvQueue $bufferRecv = null){
		$this->sleeperEntry = $entry;

		$this->slaveNumber = self::$nextSlaveNumber++;
		$this->bufferSend = $bufferSend ?? new QuerySendQueue();
		$this->bufferRecv = $bufferRecv ?? new QueryRecvQueue();
		$this->start(NativeThread::INHERIT_INI);
	}

	protected function onRun() : void{
		$error = $this->createConn($resource);
		$this->connCreated = true;
		$this->connError = $error;

		$notifier = $this->sleeperEntry->createNotifier();

		if($error !== null){
			return;
		}

		while(true){
			$row = $this->bufferSend->fetchQuery();
			if(!is_string($row)){
				break;
			}
			$this->busy = true;
			[$queryId, $modes, $queries, $params] = unserialize($row, ["allowed_classes" => true]);

			try{
				$results = [];
				foreach($queries as $index => $query){
					$results[] = $this->executeQuery($resource, $modes[$index], $query, $params[$index]);
				}
				$this->bufferRecv->publishResult($queryId, $results);
			}catch(SqlError $error){
				$this->bufferRecv->publishError($queryId, $error);
			}

			$notifier->wakeupSleeper();
			$this->busy = false;
		}
		$this->close($resource);
	}

	public function isBusy() : bool{
		return $this->busy;
	}

	public function stopRunning() : void{
		$this->bufferSend->invalidate();

		parent::quit();
	}

	public function quit() : void{
		$this->stopRunning();
		parent::quit();
	}

	public function addQuery(int $queryId, array $modes, array $queries, array $params) : void{
		$this->bufferSend->scheduleQuery($queryId, $modes, $queries, $params);
	}

	public function readResults(array &$callbacks, ?int $expectedResults) : void{
		if($expectedResults === null){
			$resultsList = $this->bufferRecv->fetchAllResults();
		}else{
			$resultsList = $this->bufferRecv->waitForResults($expectedResults);
		}
		foreach($resultsList as [$queryId, $results]){
			if(!isset($callbacks[$queryId])){
				throw new InvalidArgumentException("Missing handler for query #$queryId");
			}

			$callbacks[$queryId]($results);
			unset($callbacks[$queryId]);
		}
	}

	public function connCreated() : bool{
		return $this->connCreated;
	}

	public function hasConnError() : bool{
		return $this->connError !== null;
	}

	public function getConnError() : ?string{
		return $this->connError;
	}

	protected abstract function createConn(&$resource) : ?string;

	/**
	 * @param mixed   $resource
	 * @param mixed[] $params
	 *
	 * @throws SqlError
	 */
	protected abstract function executeQuery($resource, int $mode, string $query, array $params) : SqlResult;

	protected abstract function close(&$resource) : void;
}
