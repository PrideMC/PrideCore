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
use PrideCore\Player\Forms;
use PrideCore\Player\Player;
use function count;

/**
 * Region transfer UI
 */
class RegionCommand extends Command implements PluginOwned {

	public function getOwningPlugin() : Core {
		return Core::getInstance();
	}

	public function __construct(){
		parent::__construct("region", "Change your current server region to another.", "/region");
		$this->setPermission("pride.basic.command");
	}

	public function execute(CommandSender $sender, string $label, array $args) : void{
		if(!$sender instanceof Player){
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . TF::RED . " Sorry, this can be only executed as a player.");
			return;
		}

		if(count($args) === 0){
			Forms::getInstance()->regionForm($sender); // region form :<
		} else {
			switch($args[0]){
				case "as":
				case "asia":
				case "sg":
				case "singapore":
					Forms::getInstance()->asiaServerTransferForm($sender);
					break;
				case "us":
				case "america":
				case "unitedstates":
				case "northamerica":
				case "na":
					Forms::getInstance()->northAmericaServerTransferForm($sender);
					break;
				case "eu":
				case "europe":
				case "uk":
				case "unitedkindom":
				case "euro":
					Forms::getInstance()->europeServerTransferForm($sender);
					break;
				default:
					Forms::getInstance()->regionForm($sender); // region form :<
					break;
			}
		}
	}
}
