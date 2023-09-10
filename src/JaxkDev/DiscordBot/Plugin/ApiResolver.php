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

/*
 * DiscordBot, PocketMine-MP Plugin.
 *
 * Licensed under the Open Software License version 3.0 (OSL-3.0)
 * Copyright (C) 2020-present JaxkDev
 *
 * Discord :: JaxkDev
 * Email   :: JaxkDev@gmail.com
 */

namespace JaxkDev\DiscordBot\Plugin;

use AssertionError;
use AttachableLogger;
use JaxkDev\DiscordBot\Communication\Packets\Resolution;
use JaxkDev\DiscordBot\Libs\React\Promise\Deferred;
use JaxkDev\DiscordBot\Libs\React\Promise\PromiseInterface;
use pocketmine\Server;
use RuntimeException;

/** @internal */
final class ApiResolver{

	static private ?AttachableLogger $logger;

	/** @var Array<int, Deferred> */
	static private array $map = [];

	static public function create(int $uid) : PromiseInterface{
		if(isset(self::$map[$uid])){
			throw new AssertionError("Packet {$uid} already linked to a promise resolver.");
		}
		$d = new Deferred();
		self::$map[$uid] = $d;
		return $d->promise();
	}

	static public function getPromise(int $uid) : ?PromiseInterface{
		return isset(self::$map[$uid]) ? self::$map[$uid]->promise() : null;
	}

	static public function handleResolution(Resolution $packet) : void{
		if(isset(self::$map[$packet->getPid()])){
			$d = self::$map[$packet->getPid()];
			if($packet->wasSuccessful()){
				$d->resolve(new ApiResolution([$packet->getResponse(), ...$packet->getData()]));
			}else{
				$d->reject(new ApiRejection($packet->getResponse(), $packet->getData()));
			}
			unset(self::$map[$packet->getPid()]);
		}else{
			if((self::$logger ?? null) === null){
				$pl = null;
				try{
					$pl = Server::getInstance()->getPluginManager()->getPlugin("DiscordBot");
				}catch(RuntimeException){}
				if($pl instanceof Main){
					self::$logger = $pl->getLogger();
				}else{
					throw new RuntimeException("Failed to fetch DiscordBot logger.");
				}
			}
			self::$logger->debug("An unidentified resolution has been received, ID: {$packet->getPid()}, Successful: {$packet->wasSuccessful()}, Message: {$packet->getResponse()}");
		}
	}
}
