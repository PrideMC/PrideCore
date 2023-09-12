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

namespace PrideCore\Commands\Staff;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat as TF;
use PrideCore\Core;

use function strtolower;

/**
 * Global mute the server.
 */
class GlobalMuteCommand extends Command implements PluginOwned
{

	public function getOwningPlugin() : Core
	{
		return Core::getInstance();
	}

	public function __construct()
	{
		parent::__construct("globalmute", "Global Mute for the Server.", "/globalmute [on|off]", ["gm"]);
		$this->setPermission("pride.staff.globalmute");
	}

	public function execute(CommandSender $sender, string $label, array $args) : void
	{
		if (!$this->testPermission($sender)) {
			return;
		}

		if (!isset($args[0])) {
			if (Core::$mute) {
				Core::$mute = false;
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "The global mute is now disabled.");
			} else {
				Core::$mute = true;
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "The global mute is now enabled.");
			}
		} else {
			if (strtolower($args[0]) === "on") {
				Core::$mute = true;
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "The global mute is now enabled.");
			} elseif (strtolower($args[0]) === "off") {
				Core::$mute = false;
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "The global mute is now disabled.");
			} else {
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "Usage: " . $this->usageMessage);
			}
		}
	}
}
