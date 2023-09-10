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

namespace libasynCurl\thread;

use pocketmine\scheduler\AsyncPool;
use pocketmine\scheduler\DumpWorkerMemoryTask;
use pocketmine\scheduler\GarbageCollectionTask;
use function gc_collect_cycles;

class CurlThreadPool extends AsyncPool
{
	public const MEMORY_LIMIT = 256; // 256MB Limit
	public const POOL_SIZE = 2; // 2 workers
	public const COLLECT_INTERVAL = 1; // 1 tick
	public const GARBAGE_COLLECT_INTERVAL = 15 * 60 * 20; // 15 minutes

	/**
	 * Dumps the server memory into the specified output folder.
	 */
	public function dumpMemory(string $outputFolder, int $maxNesting, int $maxStringSize) : void
	{
		foreach ($this->getRunningWorkers() as $i) {
			$this->submitTaskToWorker(new DumpWorkerMemoryTask($outputFolder, $maxNesting, $maxStringSize), $i);
		}
	}

	public function triggerGarbageCollector() : int
	{
		$this->shutdownUnusedWorkers();

		foreach ($this->getRunningWorkers() as $i) {
			$this->submitTaskToWorker(new GarbageCollectionTask(), $i);
		}

		return gc_collect_cycles();
	}
}
