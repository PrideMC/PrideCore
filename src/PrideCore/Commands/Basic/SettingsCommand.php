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

namespace PrideCore\Commands\Basic;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat as TF;
use PrideCore\Core;
use PrideCore\Player\Forms;
use PrideCore\Player\Player;
use PrideCore\Player\SettingsManager;
use function count;
use function strtolower;

/**
 * Settings personalize command!
 */
class SettingsCommand extends Command implements PluginOwned {

	public function getOwningPlugin() : Core{
		return Core::getInstance();
	}

	public function __construct()
	{
		parent::__construct("settings", "View your personalized settings.", "/settings [help|alwaysSprint|visiblePlayers|reload] [boolean]", ["toggles"]);
		$this->setPermission("pride.basic.command");
	}

	public function execute(CommandSender $sender, string $label, array $args) : void
	{
		if (!$sender instanceof Player) {
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . TF::RED . " Sorry, this can be only executed as a player.");
			return;
		}

		if(count($args) === 0){
		  Forms::getInstance()->viewSettings($sender);
		  return;
		}

		if($args[0] === "help"){
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . TF::GREEN . " /settings help - Help Information");
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . TF::GREEN . " /settings alwaysSprint <true/false> - Set AutoSprint.");
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . TF::GREEN . " /settings visiblePlayers <true/false> - Set Player Visuals.");
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . TF::GREEN . " /settings reload - Reload/Restart Settings.");
			return;
		}

		if(strtolower($args[0]) === "alwayssprint"){
			if(!isset($args[1])){
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . TF::RED . " Usage: /settings alwaysSprint <true/false>");
				return;
			}

			if($args[1] === "true"){
				SettingsManager::getInstance()->setAlwaysSprinting($sender, true);
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . TF::GREEN . " Autosprint has been enabled.");
				return;
			}
			if($args[1] === "false"){
				SettingsManager::getInstance()->setAlwaysSprinting($sender, false);
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . TF::RED . " Autosprint has been disabled.");
			}
		  return;
		}

		if(strtolower($args[0]) === "visibleplayers"){
			if(!isset($args[1])){
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . TF::RED . " Usage: /settings visiblePlayers <true/false>");
				return;
			}

			if($args[1] === "true"){
				SettingsManager::getInstance()->setVisiblityToPlayers($sender, true);
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . TF::GREEN . " The player visibility has been enabled.");
				return;
			}
			if($args[1] === "false"){
				SettingsManager::getInstance()->setVisiblityToPlayers($sender, false);
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . TF::RED . " The player visibility  has been disabled.");
			}
		  return;
		}

		if(strtolower($args[0]) === "reload"){
			SettingsManager::getInstance()->init($sender);
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . TF::GREEN . " Successfully reloaded your personal settings.");
		}
	}
}
