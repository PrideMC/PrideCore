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

namespace PrideCore\Tasks;

use libpmquery\PMQuery as Query;
use libpmquery\PMQueryException as QueryException;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use PrideCore\Utils\Cache;

use function explode;
use function json_decode;
use function json_encode;

/**
 * Update the player count on the server.
 */
class UpdatePlayersTask extends AsyncTask
{
	/** @var string */
	private $serversData;

	public function __construct(array $serversConfig)
	{
		$this->serversData = json_encode($serversConfig, JSON_THROW_ON_ERROR);
	}

	public function onRun() : void
	{
		$res = ['count' => 0, 'maxPlayers' => 0, 'errors' => []];
		$serversConfig = json_decode($this->serversData, true, 512, JSON_THROW_ON_ERROR);
		foreach ($serversConfig as $serverConfigString) {
			$serverData = explode(':', $serverConfigString);
			$ip = $serverData[0];
			$port = (int) $serverData[1];
			try {
				$qData = Query::query($ip, $port);
			} catch (QueryException $e) {
				$res['errors'][] = 'Failed to query ' . $serverConfigString . ': ' . $e->getMessage();
				continue;
			}
			$res['count'] += $qData['Players'];
		}
		$this->setResult($res);
	}

	public function onCompletion() : void
	{
		$server = Server::getInstance();
		$res = $this->getResult();
		if(!isset($res['errors'])){
			Cache::getInstance()->setPlayerCount($res['count']);
		} else {
			foreach($res['errors'] as $e){
				$server->getLogger()->debug($e);
			}
		}
	}
}
