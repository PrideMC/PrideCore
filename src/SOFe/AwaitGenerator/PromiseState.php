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

use Throwable;
use function assert;

abstract class PromiseState{
	public const STATE_PENDING = 0;
	public const STATE_RESOLVED = 1;
	public const STATE_REJECTED = 2;

	/** @var int */
	protected $state = self::STATE_PENDING;
	/** @var mixed */
	protected $resolved;
	/** @var Throwable */
	protected $rejected;

	/** @var bool  */
	protected $cancelled = false;

	/**
	 * @param mixed $value
	 */
	public function resolve($value) : void{
		assert($this->state === self::STATE_PENDING);

		$this->state = self::STATE_RESOLVED;
		$this->resolved = $value;
	}

	public function reject(Throwable $value) : void{
		assert($this->state === self::STATE_PENDING);

		$this->state = self::STATE_REJECTED;
		$this->rejected = $value;
	}
}
