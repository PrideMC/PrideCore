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

/**
 * Mute a player.
 */
class MuteCommand extends Command implements PluginOwned
{

	public function getOwningPlugin() : Core
	{
		return Core::getInstance();
	}

	public function __construct()
	{
		parent::__construct("mute", "Mute a player.", "/mute <player_name> [reason] [time]");
		$this->setPermission("pride.staff.mute");
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

		if(($target = Core::getInstance()->getServer()->getPlayerExact($args[0])) !== null){
			if(!isset($args[1])){
				if($target->isMuted()){
					$target->setMuted(false);
					$target->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "You have been unmuted by our staff: " . TF::YELLOW . $sender->getName() . TF::RED . " , You're now able to talk in the chat or interact with other people.");
					$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "The player " . TF::YELLOW . $target->getName() . TF::RED . " is now unmuted.");
				} else {
					$target->setMuted(true);
					$target->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "You have been muted by our staff: " . TF::YELLOW . $sender->getName() . TF::RED . " , If you believe this is a mistake. Appeal us on our discord.");
					$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "The player " . TF::YELLOW . $target->getName() . TF::RED . " is now muted.");
				}
				return;
			} else {
				if($target->isMuted()){
					$target->setMuted(false);
					$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "You have been unmuted by our staff: " . TF::YELLOW . $sender->getName() . TF::RED . " for " . TF::AQUA . $args[1] . ", You're now able to talk in the chat or interact with other people.");
					$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "The player " . TF::YELLOW . $target->getName() . TF::RED . " is now unmuted.");
				} else {
					$target->setMuted(true);
					$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "You have been unmuted by our staff: " . TF::YELLOW . $sender->getName() . TF::RED . " for " . TF::AQUA . $args[1] . ", You're now able to talk in the chat or interact with other people.");
					$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "The player " . TF::YELLOW . $target->getName() . TF::RED . " is now muted.");
				}
				return;
			}
		} else {
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "Unable to find player: " . $args[0]);
		}
	}
}
