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

namespace libasynCurl\thread;

use Closure;
use InvalidArgumentException;
use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\InternetRequestResult;
use pocketmine\utils\Utils;
use function igbinary_serialize;
use function igbinary_unserialize;

abstract class CurlTask extends AsyncTask
{

	protected string $page;

	protected int $timeout;

	protected string $headers;

	public function __construct(string $page, int $timeout, array $headers, Closure $closure = null)
	{
		$this->page = $page;
		$this->timeout = $timeout;

		$serialized_headers = igbinary_serialize($headers);
		if ($serialized_headers === null) {
			throw new InvalidArgumentException("Headers cannot be serialized");
		}
		$this->headers = $serialized_headers;

		if ($closure !== null) {
			Utils::validateCallableSignature(function (?InternetRequestResult $result) : void {}, $closure);
			$this->storeLocal('closure', $closure);
		}
	}

	public function getHeaders() : array
	{
		/** @var array $headers */
		$headers = igbinary_unserialize($this->headers);

		return $headers;
	}

	public function onCompletion() : void
	{
		try {
			/** @var Closure $closure */
			$closure = $this->fetchLocal('closure');
		} catch (InvalidArgumentException $exception) {
			return;
		}

		if ($closure !== null) {
			$closure($this->getResult());
		}
	}
}
