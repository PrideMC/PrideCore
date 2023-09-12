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

/**
 * @template ParentT
 */
class AwaitChild extends PromiseState{
	/** @var Await<ParentT> */
	protected $await;

	/**
	 * @phpstan-param Await<ParentT> $await
	 */
	public function __construct(Await $await){
		$this->await = $await;
	}

	/**
	 * @param mixed $value
	 */
	public function resolve($value = null) : void{
		if($this->state !== self::STATE_PENDING){
			return; // nothing should happen if resolved/rejected multiple times
		}

		parent::resolve($value);
		if(!$this->cancelled && $this->await->isSleeping()){
			$this->await->recheckPromiseQueue($this);
		}
	}

	public function reject(Throwable $value) : void{
		if($this->state !== self::STATE_PENDING){
			return; // nothing should happen if resolved/rejected multiple times
		}

		parent::reject($value);
		if(!$this->cancelled && $this->await->isSleeping()){
			$this->await->recheckPromiseQueue($this);
		}
	}
}
