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

use Throwable;
use function array_push;
use function is_object;
use function key;
use function method_exists;

/**
 * @internal
 */
final class CancellationQueue
{
	private $started = false;
	private $queue = [];

	public function __invoke() : void
	{
		if ($this->started) {
			return;
		}

		$this->started = true;
		$this->drain();
	}

	public function enqueue($cancellable) : void
	{
		if (!is_object($cancellable) || !method_exists($cancellable, 'then') || !method_exists($cancellable, 'cancel')) {
			return;
		}

		$length = array_push($this->queue, $cancellable);

		if ($this->started && 1 === $length) {
			$this->drain();
		}
	}

	private function drain() : void
	{
		for ($i = key($this->queue); isset($this->queue[$i]); $i++) {
			$cancellable = $this->queue[$i];

			$exception = null;

			try {
				$cancellable->cancel();
			} catch (Throwable $exception) {
			}

			unset($this->queue[$i]);

			if ($exception) {
				throw $exception;
			}
		}

		$this->queue = [];
	}
}
