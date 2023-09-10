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

use pmmp\thread\ThreadSafe;
use pmmp\thread\ThreadSafeArray;
use function serialize;

class QuerySendQueue extends ThreadSafe{
	/** @var bool */
	private $invalidated = false;
	/** @var ThreadSafeArray */
	private $queries;

	public function __construct(){
		$this->queries = new ThreadSafeArray();
	}

	public function scheduleQuery(int $queryId, array $modes, array $queries, array $params) : void{
		if($this->invalidated){
			throw new QueueShutdownException("You cannot schedule a query on an invalidated queue.");
		}
		$this->synchronized(function() use ($queryId, $modes, $queries, $params) : void{
			$this->queries[] = serialize([$queryId, $modes, $queries, $params]);
			$this->notifyOne();
		});
	}

	public function fetchQuery() : ?string {
		return $this->synchronized(function() : ?string {
			while($this->queries->count() === 0 && !$this->isInvalidated()){
				$this->wait();
			}
			return $this->queries->shift();
		});
	}

	public function invalidate() : void {
		$this->synchronized(function() : void{
			$this->invalidated = true;
			$this->notify();
		});
	}

	public function isInvalidated() : bool {
		return $this->invalidated;
	}

	public function count() : int{
		return $this->queries->count();
	}
}
