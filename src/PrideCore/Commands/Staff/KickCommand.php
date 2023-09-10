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

/**
 * Kick!!
 */
class KickCommand extends Command implements PluginOwned
{

	public function getOwningPlugin() : Core
	{
		return Core::getInstance();
	}

	public function __construct()
	{
		parent::__construct("kick", "Kick a player.", "/kick <player_name> [reason]");
		$this->setPermission("pride.staff.kick");
	}

	public function execute(CommandSender $sender, string $label, array $args) : void
	{
		if (!$this->testPermission($sender)) {
			return;
		}

		if (!isset($args[0])) {
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "Usage: " . $this->usageMessage);
			return;
		}

		if (($target = Server::getInstance()->getPlayerExact($args[0])) === null) {
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "The player " . TF::DARK_RED . $args[0] . TF::RED . " couldnt find in the server.");
		} else {
			if (!isset($args[1])) {
				$target->kick(Core::PREFIX . TF::GRAY . "\nYou have been kicked from our network." . "\n\n" . Core::ARROW . TF::RESET . TF::GRAY . "Reason: " . TF::YELLOW . "Unspecified" . TF::RESET);
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "Sucessfully kicked " . $args[0] . " from the server.");
			} else {
				$target->kick(Core::PREFIX . TF::GRAY . "\nYou have been kicked from our network." . "\n\n" . Core::ARROW . TF::RESET . TF::GRAY . "Reason: " . TF::YELLOW . $args[1] . TF::RESET);
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "Sucessfully kicked " . $args[0] . " from the server for " . $args[1]);
			}
		}
	}
}
