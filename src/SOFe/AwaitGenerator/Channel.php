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

use Generator;
use function array_shift;
use function count;
use function spl_object_id;

/**
 * A channel allows coroutines to communicate by sending and polling values in an FIFO stream.
 * @template T
 */
final class Channel{
	private EmptyChannelState|SendingChannelState|ReceivingChannelState $state;

	public function __construct(){
		$this->state = new EmptyChannelState();
	}

	/**
	 * Sends a value to the channel,
	 * and wait until the value is received by the other side.
	 *
	 * @param T $value
	 */
	public function sendAndWait($value) : Generator{
		if($this->state instanceof ReceivingChannelState){
			$receiver = array_shift($this->state->queue);
			if(count($this->state->queue) === 0){
				$this->state = new EmptyChannelState();
			}
			$receiver($value);
			return;
		}

		if($this->state instanceof EmptyChannelState){
			$this->state = new SendingChannelState();
		}

		try {
			// $key holds the object reference directly instead of the key to avoid GC causing spl_object_id duplicate
			$key = null;

			yield from Await::promise(function($resolve) use($value, &$key){
				$key = $resolve;
				$this->state->queue[spl_object_id($key)] = [$value, $resolve];
			});
		} finally {
			if($key !== null) {
				if($this->state instanceof SendingChannelState) {
					// our key may still exist in the channel state

					unset($this->state->queue[spl_object_id($key)]);
					if(count($this->state->queue) === 0) {
						$this->state = new EmptyChannelState();
					}
				}
				// else, state already changed means our key has been shifted already.
			}
		}
	}

	/**
	 * Send a value to the channel
	 * without waiting for a receiver.
	 *
	 * This method always returns immediately.
	 * It is equivalent to calling `Await::g2c($channel->sendAndWait($value))`.
	 *
	 * @param T $value
	 */
	public function sendWithoutWait($value) : void{
		Await::g2c($this->sendAndWait($value));
	}

	/**
	 * Try to send a value to the channel if there is a receive waiting.
	 * Returns whether the value successfully sent.
	 *
	 * @param T $value
	 */
	public function trySend($value) : bool {
		if($this->state instanceof ReceivingChannelState) {
			$receiver = array_shift($this->state->queue);
			if(count($this->state->queue) === 0){
				$this->state = new EmptyChannelState();
			}
			$receiver($value);
			return true;
		}

		return false;
	}

	/**
	 * Receive a value from the channel.
	 * Waits for a sender if there is currently no sender waiting.
	 *
	 * @return Generator<mixed, Await::RESOLVE|null|Await::RESOLVE_MULTI|Await::REJECT|Await::ONCE|Await::ALL|Await::RACE|Generator, mixed, T>
	 */
	public function receive() : Generator{
		if($this->state instanceof SendingChannelState){
			[$value, $sender] = array_shift($this->state->queue);
			if(count($this->state->queue) === 0){
				$this->state = new EmptyChannelState();
			}
			$sender();
			return $value;
		}

		if($this->state instanceof EmptyChannelState){
			$this->state = new ReceivingChannelState();
		}

		try {
			// $key holds the object reference directly instead of the key to avoid GC causing spl_object_id duplicate
			$key = null;

			return yield from Await::promise(function($resolve) use(&$key){
				$key = $resolve;
				$this->state->queue[spl_object_id($key)] = $resolve;
			});
		} finally {
			if($key !== null) {
				if($this->state instanceof ReceivingChannelState) {
					// our key may still exist in the channel state

					unset($this->state->queue[spl_object_id($key)]);
					if(count($this->state->queue) === 0) {
						$this->state = new EmptyChannelState();
					}
				}
				// else, state already changed means our key has been shifted already.
			}
		}
	}

	/**
	 * Try to receive a value from the channel if there is a sender waiting.
	 * Returns `$default` if there is no sender waiting.
	 *
	 * @template U
	 * @param U $default
	 *
	 * @return T|U
	 */
	public function tryReceiveOr($default) {
		if($this->state instanceof SendingChannelState) {
			[$value, $sender] = array_shift($this->state->queue);
			if(count($this->state->queue) === 0){
				$this->state = new EmptyChannelState();
			}
			$sender();
			return $value;
		}

		return $default;
	}

	public function getSendQueueSize() : int {
		if($this->state instanceof SendingChannelState){
			return count($this->state->queue);
		}

		return 0;
	}

	public function getReceiveQueueSize() : int {
		if($this->state instanceof ReceivingChannelState){
			return count($this->state->queue);
		}

		return 0;
	}
}
