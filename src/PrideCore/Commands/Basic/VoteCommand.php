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

namespace PrideCore\Commands\Basic;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat as TF;
use PrideCore\Core;
use PrideCore\Player\Player;

/**
 * Vote on our server.
 */
class VoteCommand extends Command implements PluginOwned {

	public function getOwningPlugin() : Core
	{
		return Core::getInstance();
	}

	public function	__construct()
	{
		parent::__construct("vote", "Vote on our server.", "/vote");
		$this->setPermission("pride.basic.command");
	}

	/**
	 * @return [type]
	 */
	public function execute(CommandSender $sender, string $label, array $args)
	{
		if (!$sender instanceof Player) {
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . TF::RED . " Sorry, this can be only executed as a player.");
			return;
		}

		$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::AQUA . "Visit https://mcpride.tk/vote on your browser, and vote in our server and get a chance to get a free cape and lobby tag on our server.");
	}
}
