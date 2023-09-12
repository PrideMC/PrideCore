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

namespace SOFe\AwaitGenerator;

use AssertionError;
use Closure;
use Generator;
use RuntimeException;
use function array_shift;
use function count;

/**
 * A mutex is a lock that can only be acquired by one coroutine at a time.
 * Coroutines can acquire the lock by calling `acquire()`, and release it by calling `release()`.
 * `acquire()` is an asynchronous operation, which suspends until other coroutines release the lock.
 * If multiple coroutines request the same lock,
 * the first coroutine requesting the lock acquires it first.
 * A mutex can be thought as a run queue.
 *
 * To avoid forgetting to release the lock,
 * use the `run`/`runClosure` methods,
 * which executes the given generator only when the lock is acquired,
 * and automatically releases the lock when the generator completes (even if it throws an exception).
 *
 * Since it is impossible to identify which coroutine is running,
 * recursive mutex locking will lead to deadlock.
 */
final class Mutex{
	private bool $acquired = false;

	/** @var list<Closure(): void> */
	private array $queue = [];

	/**
	 * Returns whether the mutex is idle,
	 * i.e. not acquired by any coroutine.
	 */
	public function isIdle() : bool{
		return !$this->acquired;
	}

	public function acquire() : Generator{
		if(!$this->acquired){
			// Mutex is idle, no extra work to do
			$this->acquired = true;
			return;
		}

		$this->queue[] = yield Await::RESOLVE;

		yield Await::ONCE;

		if(!$this->acquired) {
			throw new AssertionError("Mutex->acquired should remain true if queue is nonempty");
		}
	}

	public function release() : void{
		if(!$this->acquired){
			throw new RuntimeException("Attempt to release a released mutex");
		}

		if(count($this->queue) === 0){
			// Mutex is now idle, just clean up.
			$this->acquired = false;
			return;
		}

		$next = array_shift($this->queue);

		// When this call completes, $next may or may not be complete,
		// and $this->queue may or may not be modified.
		// `release()` may also have been called within `$next()`.
		// Therefore, we must not do anything after this call,
		// and leave the changes like setting $this->acquired to false to the other release call.
		$next();
	}

	/**
	 * @template T
	 * @param Closure(): Generator<mixed, Await::RESOLVE|null|Await::RESOLVE_MULTI|Await::REJECT|Await::ONCE|Await::ALL|Await::RACE|Generator, mixed, T> $generatorClosure
	 * @return Generator<mixed, Await::RESOLVE|null|Await::RESOLVE_MULTI|Await::REJECT|Await::ONCE|Await::ALL|Await::RACE|Generator, mixed, T>
	 */
	public function runClosure(Closure $generatorClosure) : Generator{
		return yield from $this->run($generatorClosure());
	}

	/**
	 * @template T
	 * @param Generator<mixed, Await::RESOLVE|null|Await::RESOLVE_MULTI|Await::REJECT|Await::ONCE|Await::ALL|Await::RACE|Generator, mixed, T> $generator
	 * @return Generator<mixed, Await::RESOLVE|null|Await::RESOLVE_MULTI|Await::REJECT|Await::ONCE|Await::ALL|Await::RACE|Generator, mixed, T>
	 */
	public function run(Generator $generator) : Generator{
		yield from $this->acquire();
		try{
			return yield from $generator;
		}finally{
			$this->release();
		}
	}
}
