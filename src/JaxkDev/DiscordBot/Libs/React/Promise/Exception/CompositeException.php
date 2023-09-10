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

namespace JaxkDev\DiscordBot\Libs\React\Promise\Exception;

use Exception;
use Throwable;

/**
 * Represents an exception that is a composite of one or more other exceptions.
 *
 * This exception is useful in situations where a promise must be rejected
 * with multiple exceptions. It is used for example to reject the returned
 * promise from `some()` and `any()` when too many input promises reject.
 */
class CompositeException extends Exception
{
	private $throwables;

	public function __construct(array $throwables, $message = '', $code = 0, $previous = null)
	{
		parent::__construct($message, $code, $previous);

		$this->throwables = $throwables;
	}

	/**
	 * @return Throwable[]
	 */
	public function getThrowables() : array
	{
		return $this->throwables;
	}
}
