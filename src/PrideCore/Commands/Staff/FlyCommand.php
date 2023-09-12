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
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;
use PrideCore\Core;
use PrideCore\Player\Player;

use function count;

/**
 * Flying ability command.
 */
class FlyCommand extends Command implements PluginOwned
{

	public function getOwningPlugin() : Core
	{
		return Core::getInstance();
	}

	public function __construct()
	{
		parent::__construct("fly", "Fly around the server.", "/fly [player_name]");
		$this->setPermission("pride.staff.fly");
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

		if (count($args) === 0) {
			if ($sender->getAllowFlight()) {
				$sender->setAllowFlight(false);
				$sender->setFlying(false);
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "You cant now fly in the server.");
			} else {
				$sender->setAllowFlight(true);
				$sender->setFlying(true);
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "You can now fly in the server.");
			}
			return; // tl;dr
		}

		if (($target = Server::getInstance()->getPlayerExact($args[0])) === null) {
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "The player " . TF::DARK_RED . $args[0] . TF::RED . " couldnt find in the server.");
		} else {
			if ($target->getAllowFlight()) {
				$target->setAllowFlight(false);
				$target->setFlying(false);
				$target->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "You cant now fly in the server.");
			} else {
				$target->setAllowFlight(true);
				$target->setFlying(true);
				$target->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "You can now fly in the server.");
			}
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "The player " . TF::YELLOW . $target->getName() . " has been toggled their fly ability.");
		}
	}
}
