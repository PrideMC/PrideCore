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
use Generator;
use Throwable;

class GeneratorUtil{
	/**
	 * Returns a generator that yields nothing and returns $ret
	 *
	 * @param mixed $ret
	 *
	 * @template T
	 * @phpstan-param T $ret
	 * @phpstan-return Generator<never, never, never, T>
	 */
	public static function empty($ret = null) : Generator{
		false && yield;
		return $ret;
	}

	/**
	 * Returns a generator that yields nothing and throws $throwable
	 *
	 * @template T of Throwable
	 *
	 * @throws Throwable
	 *
	 * @phpstan-param T $throwable
	 * @phpstan-return Generator<never, never, never, never>
	 * @throws T
	 */
	public static function throw(Throwable $throwable) : Generator{
		false && yield;
		throw $throwable;
	}

	/**
	 * Returns a generator that never returns.
	 *
	 * Since await-generator does not maintain a runtime,
	 * calling `Await::g2c(GeneratorUtil::pending())` does not leak memory.
	 *
	 * @phpstan-return Generator<mixed, Await::RESOLVE|null|Await::RESOLVE_MULTI|Await::REJECT|Await::ONCE|Await::ALL|Await::RACE|Generator, mixed, never>
	 */
	public static function pending() : Generator{
		yield from Await::promise(function() : void{});
		throw new AssertionError("this line is unreachable");
	}
}
