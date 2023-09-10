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

namespace libasynCurl;

use Closure;
use InvalidArgumentException;
use libasynCurl\thread\CurlDeleteTask;
use libasynCurl\thread\CurlGetTask;
use libasynCurl\thread\CurlPostTask;
use libasynCurl\thread\CurlPutTask;
use libasynCurl\thread\CurlThreadPool;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;

class Curl
{

	private static bool $registered = false;

	private static CurlThreadPool $threadPool;

	public static function register(PluginBase $plugin) : void
	{
		if (self::isRegistered()) {
			throw new InvalidArgumentException("{$plugin->getName()} attempted to register " . self::class . " twice.");
		}

		$server = $plugin->getServer();
		self::$threadPool = new CurlThreadPool(CurlThreadPool::POOL_SIZE, CurlThreadPool::MEMORY_LIMIT, $server->getLoader(), $server->getLogger(), $server->getTickSleeper());

		$plugin->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () : void {
			self::$threadPool->collectTasks();
		}), CurlThreadPool::COLLECT_INTERVAL);
		$plugin->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () : void {
			self::$threadPool->triggerGarbageCollector();
		}), CurlThreadPool::GARBAGE_COLLECT_INTERVAL);

		self::$registered = true;
	}

	public static function isRegistered() : bool
	{
		return self::$registered;
	}

	public static function postRequest(string $page, array|string $args, int $timeout = 10, array $headers = [], Closure $closure = null) : void
	{
		self::$threadPool->submitTask(new CurlPostTask($page, $args, $timeout, $headers, $closure));
	}

	public static function putRequest(string $page, array|string $args, int $timeout = 10, array $headers = [], Closure $closure = null) : void
	{
		self::$threadPool->submitTask(new CurlPutTask($page, $args, $timeout, $headers, $closure));
	}

	public static function deleteRequest(string $page, array|string $args, int $timeout = 10, array $headers = [], Closure $closure = null) : void
	{
		self::$threadPool->submitTask(new CurlDeleteTask($page, $args, $timeout, $headers, $closure));
	}

	public static function getRequest(string $page, int $timeout = 10, array $headers = [], Closure $closure = null) : void
	{
		self::$threadPool->submitTask(new CurlGetTask($page, $timeout, $headers, $closure));
	}
}
