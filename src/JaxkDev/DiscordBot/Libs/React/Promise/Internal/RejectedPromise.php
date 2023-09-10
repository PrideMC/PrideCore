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

use JaxkDev\DiscordBot\Libs\React\Promise\Promise;
use JaxkDev\DiscordBot\Libs\React\Promise\PromiseInterface;
use Throwable;
use function JaxkDev\DiscordBot\Libs\React\Promise\_checkTypehint;
use function JaxkDev\DiscordBot\Libs\React\Promise\enqueue;
use function JaxkDev\DiscordBot\Libs\React\Promise\fatalError;
use function JaxkDev\DiscordBot\Libs\React\Promise\resolve;

/**
 * @internal
 */
final class RejectedPromise implements PromiseInterface
{
	private $reason;

	public function __construct(Throwable $reason)
	{
		$this->reason = $reason;
	}

	public function then(callable $onFulfilled = null, callable $onRejected = null) : PromiseInterface
	{
		if (null === $onRejected) {
			return $this;
		}

		return new Promise(function (callable $resolve, callable $reject) use ($onRejected) : void {
			enqueue(function () use ($resolve, $reject, $onRejected) : void {
				try {
					$resolve($onRejected($this->reason));
				} catch (Throwable $exception) {
					$reject($exception);
				}
			});
		});
	}

	public function done(callable $onFulfilled = null, callable $onRejected = null) : void
	{
		enqueue(function () use ($onRejected) {
			if (null === $onRejected) {
				fatalError($this->reason);
			}

			try {
				$result = $onRejected($this->reason);
			} catch (Throwable $exception) {
				fatalError($exception);
			}

			if ($result instanceof self) {
				fatalError($result->reason);
			}

			if ($result instanceof PromiseInterface) {
				$result->done();
			}
		});
	}

	public function otherwise(callable $onRejected) : PromiseInterface
	{
		if (!_checkTypehint($onRejected, $this->reason)) {
			return $this;
		}

		return $this->then(null, $onRejected);
	}

	public function always(callable $onFulfilledOrRejected) : PromiseInterface
	{
		return $this->then(null, function (Throwable $reason) use ($onFulfilledOrRejected) : PromiseInterface {
			return resolve($onFulfilledOrRejected())->then(function () use ($reason) : PromiseInterface {
				return new RejectedPromise($reason);
			});
		});
	}

	public function cancel() : void
	{
	}
}
