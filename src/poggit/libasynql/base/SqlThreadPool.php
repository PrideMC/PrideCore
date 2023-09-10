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
 *
 *  www.mcpride.tk                 github.com/PrideMC
 *  twitter.com/PrideMC         youtube.com/c/PrideMC
 *  discord.gg/PrideMC           facebook.com/PrideMC
 *               bit.ly/JoinInPrideMC
 *  #StandWithUkraine                     #PrideMonth
 *
 */

declare(strict_types=1);

namespace poggit\libasynql\base;

use InvalidArgumentException;
use pocketmine\Server;
use pocketmine\snooze\SleeperHandlerEntry;
use poggit\libasynql\SqlThread;
use function assert;
use function count;

class SqlThreadPool implements SqlThread{

	private SleeperHandlerEntry $sleeperEntry;
	/** @var callable */
	private $workerFactory;
	/** @var SqlSlaveThread[] */
	private $workers = [];
	/** @var int */
	private $workerLimit;

	/** @var QuerySendQueue */
	private $bufferSend;
	/** @var QueryRecvQueue */
	private $bufferRecv;

	/** @var DataConnectorImpl|null */
	private $dataConnector = null;

	public function setDataConnector(DataConnectorImpl $dataConnector) : void {
		$this->dataConnector = $dataConnector;
	}

	/**
	 * SqlThreadPool constructor.
	 *
	 * @param callable $workerFactory create a child worker: <code>function(?Threaded $bufferSend = null, ?Threaded $bufferRecv = null) : {@link BaseSqlThread}{}</code>
	 * @param int      $workerLimit   the maximum number of workers to create. Workers are created lazily.
	 */
	public function __construct(callable $workerFactory, int $workerLimit){
		$this->sleeperEntry = Server::getInstance()->getTickSleeper()->addNotifier(function() : void{
			assert($this->dataConnector instanceof DataConnectorImpl); // otherwise, wtf
			$this->dataConnector->checkResults();
		});

		$this->workerFactory = $workerFactory;
		$this->workerLimit = $workerLimit;
		$this->bufferSend = new QuerySendQueue();
		$this->bufferRecv = new QueryRecvQueue();

		$this->addWorker();
	}

	private function addWorker() : void{
		$this->workers[] = ($this->workerFactory)($this->sleeperEntry, $this->bufferSend, $this->bufferRecv);
	}

	public function join() : void{
		foreach($this->workers as $worker){
			$worker->join();
		}
	}

	public function stopRunning() : void{
		foreach($this->workers as $worker){
			$worker->stopRunning();
		}
	}

	public function addQuery(int $queryId, array $modes, array $queries, array $params) : void{
		$this->bufferSend->scheduleQuery($queryId, $modes, $queries, $params);

		// check if we need to increase worker size
		foreach($this->workers as $worker){
			if(!$worker->isBusy()){
				return;
			}
		}
		if(count($this->workers) < $this->workerLimit){
			$this->addWorker();
		}
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
		return $this->workers[0]->connCreated();
	}

	public function hasConnError() : bool{
		return $this->workers[0]->hasConnError();
	}

	public function getConnError() : ?string{
		return $this->workers[0]->getConnError();
	}

	public function getLoad() : float{
		return $this->bufferSend->count() / (float) $this->workerLimit;
	}
}
