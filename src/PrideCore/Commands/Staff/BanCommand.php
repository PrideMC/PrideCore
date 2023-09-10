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
use PrideCore\Utils\Utils;

use function implode;

/**
 * Ban!!!
 */
class BanCommand extends Command implements PluginOwned
{

	public function getOwningPlugin() : Core
	{
		return Core::getInstance();
	}

	public function __construct()
	{
		parent::__construct("ban", "Ban a player.", "/ban <player_name> [reason] [time]");
		$this->setPermission("pride.staff.ban");
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
			if (!isset($args[1])) {
				Server::getInstance()->getNameBans()->addBan($args[0], "Unspecified", null, $sender->getName());
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "The player " . TF::YELLOW . $args[0] . TF::RED . " has been banned from the server.");
				return;
			} else {
				if (!isset($args[2])) {
					Server::getInstance()->getNameBans()->addBan($args[0], $args[1], null, $sender->getName());
					$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "The player " . TF::YELLOW . $args[0] . TF::RED . " has been banned from the server for " . TF::YELLOW . $args[1] . TF::RED . ".");
				} else {
					$date = Utils::stringToTimestamp(implode(" ", $args));
					Server::getInstance()->getNameBans()->addBan($args[0], $args[1], Utils::stringtoTimestamp($args[2])[0], $sender->getName());
					$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "The player " . TF::YELLOW . $args[0] . TF::RED . " has been banned from the server for " . TF::YELLOW . $args[1] . TF::RED . ".");
				}
			}
		} else {
			if (!isset($args[1])) {
				Server::getInstance()->getNameBans()->addBan($args[0], "Unspecified", null, $sender->getName());
				$target->kick(Core::PREFIX . T::GRAY . "\nYou have been banned from our network." . "\n\n" . Core::ARROW . TF::RESET . TF::GRAY . "Reason: " . TF::YELLOW . "Unspecified" . TF::RESET . "\n" . Core::ARROW . TF::RESET . TF::GRAY . "Expires: " . TF::YELLOW . "Permanent", "", true);
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "The player " . TF::YELLOW . $args[0] . TF::RED . " has been banned from the server for " . TF::YELLOW . "Unspecified" . TF::RED . ".");
				return;
			} else {
				if (!isset($args[2])) {
					Server::getInstance()->getNameBans()->addBan($args[0], $args[1], null, $sender->getName());
					$target->kick(Core::PREFIX . TF::GRAY . "\nYou have been banned from our network." . "\n\n" . Core::ARROW . TF::RESET . TF::GRAY . "Reason: " . TF::YELLOW . $args[1] . TF::RESET . "\n" . Core::ARROW . TF::RESET . TF::GRAY . "Expires: " . TF::YELLOW . "Permanent", $args[1], "");
					$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "The player " . TF::YELLOW . $args[0] . TF::RED . " has been banned from the server for " . TF::YELLOW . $args[1] . TF::RED . ".");
				} else {
					$date = Utils::stringToTimestamp(implode(" ", $args));
					Server::getInstance()->getNameBans()->addBan($args[0], $args[1], Utils::stringtoTimestamp($args[2])[0], $sender->getName());
					$target->kick(Core::PREFIX . TF::GRAY . "\nYou have been banned from our network." . "\n\n" . Core::ARROW . TF::RESET . TF::GRAY . "Reason: " . TF::YELLOW . $args[1] . TF::RESET . "\n" . Core::ARROW . TF::RESET . TF::GRAY . "Expires: " . TF::YELLOW . $date[0]->format("Y-m-d H:i:s"), $args[1], "");
					$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "The player " . TF::YELLOW . $args[0] . TF::RED . " has been banned from the server for " . TF::YELLOW . $args[1] . TF::RED . ".");
				}
			}
		}
	}
}
