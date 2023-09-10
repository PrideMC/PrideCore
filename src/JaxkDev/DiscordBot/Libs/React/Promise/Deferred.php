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

namespace JaxkDev\DiscordBot\Libs\React\Promise;

use Throwable;

final class Deferred implements PromisorInterface
{
	private $promise;
	private $resolveCallback;
	private $rejectCallback;

	public function __construct(callable $canceller = null)
	{
		$this->promise = new Promise(function ($resolve, $reject) : void {
			$this->resolveCallback = $resolve;
			$this->rejectCallback = $reject;
		}, $canceller);
	}

	public function promise() : PromiseInterface
	{
		return $this->promise;
	}

	public function resolve($value = null) : void
	{
		($this->resolveCallback)($value);
	}

	public function reject(Throwable $reason) : void
	{
		($this->rejectCallback)($reason);
	}
}
