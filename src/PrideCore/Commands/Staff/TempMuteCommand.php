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
use pocketmine\utils\TextFormat as TF;
use PrideCore\Core;
use PrideCore\Utils\TimeUtils;

class TempMuteCommand extends Command implements PluginOwned {

	public function getOwningPlugin() : Core
	{
		return Core::getInstance();
	}

	public function __construct()
	{
		parent::__construct("tempmute", "Temp mute a player.", "/tempmute <player_name> <time> [reason]", ["tm"]);
		$this->setPermission("pride.staff.tempmute");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : void
	{
		if(!$this->testPermission($sender)){
			return;
		}

		if (!isset($args[0])) {
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "Usage: " . $this->usageMessage);
			return;
		}

		if(($target = Core::getInstance()->getServer()->getPlayerExact($args[0])) !== null){
			if(!isset($args[1])){
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "Usage: " . $this->usageMessage);
				return;
			}

			if(!isset($args[2])){
				if($target->isMuted()){
					$target->setMuted(false);
					$target->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "You have been unmuted by our staff: " . TF::YELLOW . $sender->getName() . TF::RED . " , You're now able to talk in the chat or interact with other people.");
					$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "The player " . TF::YELLOW . $target->getName() . TF::RED . " is now unmuted.");
				} else {
					$target->setTempMute(TimeUtils::stringTimeToInt($args[2]));
					$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "The player " . TF::YELLOW . $target->getName() . TF::RED . " is temp now muted.");
					$target->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "You have been temp muted by our staff: " . TF::YELLOW . $sender->getName() . TF::RED . " , If you believe this is a mistake. Appeal us on our discord.");
				}
			} else {
				if($target->isMuted()){
					$target->setMuted(false);
					$target->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "You have been unmuted by our staff: " . TF::YELLOW . $sender->getName() . TF::RED . " , You're now able to talk in the chat or interact with other people.");
					$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "The player " . TF::YELLOW . $target->getName() . TF::RED . " is now unmuted.");
				} else {
					$target->setTempMute(TimeUtils::stringTimeToInt($args[2]));
					$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "The player " . TF::YELLOW . $target->getName() . TF::RED . " is temp now muted for " . TF::AQUA . $args[2]);
					$target->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "You have been temp muted by our staff: " . TF::YELLOW . $sender->getName() . TF::RED . " for " . TF::AQUA . $args[2] . ", If you believe this is a mistake. Appeal us on our discord.");
				}
			}
		}
	}
}
