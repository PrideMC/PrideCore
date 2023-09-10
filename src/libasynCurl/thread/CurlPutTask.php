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
use pocketmine\utils\Internet;
use pocketmine\utils\InternetException;
use pocketmine\utils\InternetRequestResult;
use function is_array;
use function json_encode;

class CurlPutTask extends CurlTask
{

	protected string $args;

	public function __construct(string $page, array|string $args, int $timeout, array $headers, Closure $closure = null)
	{
		if (is_array($args)) {
			$this->args = json_encode($args, JSON_THROW_ON_ERROR);
		} else {
			$this->args = $args;
		}

		parent::__construct($page, $timeout, $headers, $closure);
	}

	public function onRun() : void
	{
		$this->setResult(self::putURL($this->page, $this->args, $this->timeout, $this->getHeaders()));
	}

	/**
	 * PUTs data to a URL
	 * NOTE: This is a blocking operation and can take a significant amount of time. It is inadvisable to use this method on the main thread.
	 *
	 * @param string|string[] $args
	 * @param string[] $extraHeaders
	 * @param string|null $err reference parameter, will be set to the output of curl_error(). Use this to retrieve errors that occurred during the operation.
	 * @phpstan-param string|array<string, string> $args
	 * @phpstan-param list<string> $extraHeaders
	 */
	public static function putURL(string $page, array|string $args, int $timeout = 10, array $extraHeaders = [], &$err = null) : ?InternetRequestResult
	{
		try {
			return Internet::simpleCurl($page, $timeout, $extraHeaders, [
				CURLOPT_CUSTOMREQUEST => "PUT",
				CURLOPT_POSTFIELDS => $args
			]);
		} catch (InternetException $ex) {
			$err = $ex->getMessage();
			return null;
		}
	}
}
