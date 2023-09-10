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

namespace SOFe\AwaitGenerator;

use AssertionError;
use Closure;
use Generator;
use function spl_object_id;

/**
 * `Loading` is a class that represents an asynchronously loaded value.
 * Users with an instance of `Loading` can call `get` to wait for the loading process to complete.
 *
 * This is somewhat similar to the `Promise` class in JavaScript.
 *
 * @template T
 */
final class Loading{
	/** @var list<Closure(): void>|null */
	private ?array $onLoaded = [];
	private $value;

	/**
	 * @param Closure(): Generator<mixed, Await::RESOLVE|null|Await::RESOLVE_MULTI|Await::REJECT|Await::ONCE|Await::ALL|Await::RACE|Generator, mixed, T> $loader
	 */
	public function __construct(Closure $loader){
		Await::f2c(function() use($loader) {
			$this->value = yield from $loader();
			$onLoaded = $this->onLoaded;
			$this->onLoaded = null;

			if($onLoaded === null){
				throw new AssertionError("loader is called twice on the same object");
			}

			foreach($onLoaded as $closure){
				$closure();
			}
		});
	}

	/**
	 * @return array{Loading<T>, Closure(T): void}
	 */
	public static function byCallback() : array{
		$callback = null;
		$loading = new self(function() use(&$callback){
			return yield from Await::promise(function($resolve) use(&$callback){
				$callback = $resolve;
			});
		});
		return [$loading, $callback];
	}

	/**
	 * @return Generator<mixed, Await::RESOLVE|null|Await::RESOLVE_MULTI|Await::REJECT|Await::ONCE|Await::ALL|Await::RACE|Generator, mixed, T>
	 */
	public function get() : Generator{
		if($this->onLoaded !== null){
			try {
				// $key holds the object reference directly instead of the key to avoid GC causing spl_object_id duplicate
				$key = null;

				yield from Await::promise(function($resolve) use(&$key) {
					$key = $resolve;
					$this->onLoaded[spl_object_id($key)] = $resolve;
				});
			} finally {
				if($key !== null) {
					unset($this->onLoaded[spl_object_id($key)]);
				}
			}
		}

		return $this->value;
	}

	/**
	 * @template U
	 * @param U $default
	 * @return T|U
	 */
	public function getSync($default) {
		return $this->onLoaded === null ? $this->value : $default;
	}
}
