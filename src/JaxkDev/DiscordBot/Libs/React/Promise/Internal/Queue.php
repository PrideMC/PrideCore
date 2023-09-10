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

use function array_push;
use function key;

/**
 * @internal
 */
final class Queue
{
	private $queue = [];

	public function enqueue(callable $task) : void
	{
		if (1 === array_push($this->queue, $task)) {
			$this->drain();
		}
	}

	private function drain() : void
	{
		for ($i = key($this->queue); isset($this->queue[$i]); $i++) {
			try {
				($this->queue[$i])();
			} finally {
				unset($this->queue[$i]);
			}
		}

		$this->queue = [];
	}
}
