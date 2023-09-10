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

namespace PrideCore\Commands\Staff;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;
use PrideCore\Core;
use PrideCore\Player\Player;

/**
 * Can build command.
 */
class BuildCommand extends Command implements PluginOwned
{

	public function getOwningPlugin() : Core
	{
		return Core::getInstance();
	}

	public function __construct()
	{
		parent::__construct("buildermode", "Change can build status.", "/buildermode [on|off] [player_name]", ["bm"]);
		$this->setPermission("pride.builder.build");
	}

	public function execute(CommandSender $sender, string $label, array $args) : void
	{
		if (!$this->testPermission($sender)) {
			return;
		}

		if (!$sender instanceof Player) {
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . TF::RED . " Sorry, this can be only executed as a player.");
			return;
		}

		if (!isset($args[0])) {
			if ($sender->isBuilder()) {
				$sender->setBuilder(false);
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "You cant now build in this server.");
			} else {
				$sender->setBuilder(true);
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "You can now build in this server.");
			}
			return;
		}

		if ($args[0] === "on" || $args[0] === true) {
			if (!isset($args[1])) {
				$sender->setBuilder(true);
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "You can now build in this server.");
			} else {
				if (($target = Server::getInstance()->getPlayerExact($args[1])) === null) {
					$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "The player " . TF::DARK_RED . $args[0] . TF::RED . " couldnt find in the server.");
				} else {
					$target->setBuilder(true);
					$target->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "You can now build in this server.");
					$sender->sendMessage(Core::PREFIX . Core::ARROW . " " . TF::GREEN . "The player " . TF::DARK_GREEN . $args[1] . TF::GREEN . " can now build in the server.");
				}
			}
		} else {
			if (!isset($args[1])) {
				$sender->setBuilder(false);
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "You cant now build in this server.");
			} else {
				if (($target = Server::getInstance()->getPlayerExact($args[1])) === null) {
					$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "The player " . TF::DARK_RED . $args[0] . TF::RED . " couldnt find in the server.");
				} else {
					$target->setBuilder(false);
					$target->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "You cant now build in this server.");
					$sender->sendMessage(Core::PREFIX . Core::ARROW . " " . TF::GREEN . "The player " . TF::DARK_GREEN . $args[1] . TF::GREEN . " can now build in the server.");
				}
			}
		}
	}
}
