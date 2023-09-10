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

namespace JaxkDev\DiscordBot\Libs\React\Promise\Internal;

use InvalidArgumentException;
use JaxkDev\DiscordBot\Libs\React\Promise\Promise;
use JaxkDev\DiscordBot\Libs\React\Promise\PromiseInterface;
use Throwable;
use function JaxkDev\DiscordBot\Libs\React\Promise\enqueue;
use function JaxkDev\DiscordBot\Libs\React\Promise\fatalError;
use function JaxkDev\DiscordBot\Libs\React\Promise\resolve;

/**
 * @internal
 */
final class FulfilledPromise implements PromiseInterface
{
	private $value;

	public function __construct($value = null)
	{
		if ($value instanceof PromiseInterface) {
			throw new InvalidArgumentException('You cannot create JaxkDev\DiscordBot\Libs\React\Promise\FulfilledPromise with a promise. Use JaxkDev\DiscordBot\Libs\React\Promise\resolve($promiseOrValue) instead.');
		}

		$this->value = $value;
	}

	public function then(callable $onFulfilled = null, callable $onRejected = null) : PromiseInterface
	{
		if (null === $onFulfilled) {
			return $this;
		}

		return new Promise(function (callable $resolve, callable $reject) use ($onFulfilled) : void {
			enqueue(function () use ($resolve, $reject, $onFulfilled) : void {
				try {
					$resolve($onFulfilled($this->value));
				} catch (Throwable $exception) {
					$reject($exception);
				}
			});
		});
	}

	public function done(callable $onFulfilled = null, callable $onRejected = null) : void
	{
		if (null === $onFulfilled) {
			return;
		}

		enqueue(function () use ($onFulfilled) {
			try {
				$result = $onFulfilled($this->value);
			} catch (Throwable $exception) {
				fatalError($exception);
			}

			if ($result instanceof PromiseInterface) {
				$result->done();
			}
		});
	}

	public function otherwise(callable $onRejected) : PromiseInterface
	{
		return $this;
	}

	public function always(callable $onFulfilledOrRejected) : PromiseInterface
	{
		return $this->then(function ($value) use ($onFulfilledOrRejected) : PromiseInterface {
			return resolve($onFulfilledOrRejected())->then(function () use ($value) {
				return $value;
			});
		});
	}

	public function cancel() : void
	{
	}
}
