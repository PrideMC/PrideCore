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

namespace PrideCore\Tasks;

use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;
use PrideCore\Core;

use function count;
use function str_replace;

/**
 * Broadcast to players.
 */
class BroadcastTask extends Task
{

	private int $count = 0;

	public function onRun() : void
	{
		$msg = Core::getInstance()->getConfigs()->getServerConfig()->get("broadcastMessages");
		$msg = str_replace("{PREFIX}", Core::PREFIX, $msg);
		$msg = str_replace("{ARROW}", Core::ARROW, $msg);
		Core::getInstance()->getServer()->broadcastMessage(TextFormat::colorize($msg[$this->count]));
		++$this->count;

		if ($this->count === count($msg)) {
			$this->count = 0;
		}
	}
}
