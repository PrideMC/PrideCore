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
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat as TF;
use PrideCore\Core;
use PrideCore\Player\Forms;
use PrideCore\Player\Player;
use PrideCore\Utils\Config;
use PrideCore\Utils\Rank;
use function count;
use function strtoupper;

/**
 * Redeem command
 */
class RedeemCommand extends Command implements PluginOwned {

	public function getOwningPlugin() : Core{
		return Core::getInstance();
	}

	public function __construct(){
		parent::__construct("redeem", "Redeem a code.", "/redeem <code>");
		$this->setPermission("pride.basic.command");
	}

	public function execute(CommandSender $sender, string $label, array $args) : void{
		if(count($args) === 0){
			if($sender instanceof Player){
				Forms::getInstance()->redeemForm($sender);
			} else {
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "/redeem <create|remove> <code> <item>");
			}
			return;
		}

		if($args[0] === "create"){
			if(!$sender->hasPermission("pride.staff.create_redeem_code")){
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "You do not have permission to use this action.");
			} else {
				if(!isset($args[1])){
					$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "/redeem create <code> <item>");
					return;
				}

				if(!isset($args[2])){
					$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "/redeem create <code> <item>");
					return;
				}

				$this->createRedeemCode(strtoupper($args[2]), strtoupper($args[1]));
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "Successfully created a redeem code!");
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "Code: " . strtoupper($args[1]));
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "Item: " . strtoupper($args[2]));
			}
			return;
		}

		if($args[0] === "remove"){
			if(!$sender->hasPermission("pride.staff.remove_redeem_code")){
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "You do not have permission to use this action.");
			} else {
				if(!isset($args[1])){
					$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "/redeem remove <code>");
					return;
				}

				if(!Config::getInstance()->getRedeemConfig()->get("redeem_codes." . strtoupper($args[1])) === false){
					$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "Successfully removed a redeem code!");
					$this->removeRedeemCode(strtoupper($args[1]));
				} else {
					$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "Unable to remove the code: The code specified is not created yet.");
				}
			}
			return;
		}

		if(!$sender instanceof Player){
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . TF::RED . " Sorry, this can be only executed as a player.");
			return;
		}

		if(($status = Config::getInstance()->getRedeemConfig()->getNested("redeem_codes." . strtoupper($args[0]) . ".status")) !== null){
			if($status === "available"){
				$this->claim(Config::getInstance()->getRedeemConfig()->getNested("redeem_codes." . strtoupper($args[0]) . ".item"), $sender, $args[0]);
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "The code is successfully redeemed!");
			} else {
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "The code is already used.");
			}
		} else {
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "The code is expired or invalid.");
		}
	}

	public function claim(string $type, Player $player, string $code) : void {
		switch(strtoupper($type)){
			case "PLUS_RANK":
				Core::getInstance()->getRanks()->setRank($player, Rank::PLUS);
				$player->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "You have been redeemed a " . Rank::getInstance()->displayTag(Rank::PLUS) . "!");
				break;
			case "MVP_RANK":
				Core::getInstance()->getRanks()->setRank($player, Rank::MVP);
				$player->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "You have been redeemed a " . Rank::getInstance()->displayTag(Rank::MVP) . "!");
				break;
			case "VIP_RANK":
				Core::getInstance()->getRanks()->setRank($player, Rank::VIP);
				$player->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "You have been redeemed a " . Rank::getInstance()->displayTag(Rank::VIP) . "!");
				break;
			case "PRIDE_RANK":
				Core::getInstance()->getRanks()->setRank($player, Rank::PRIDE);
				$player->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "You have been redeemed a " . Rank::getInstance()->displayTag(Rank::PRIDE) . "!");
				break;
			case "MEDIA_RANK":
				Core::getInstance()->getRanks()->setRank($player, Rank::MEDIA);
				$player->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "You have been redeemed a " . Rank::getInstance()->displayTag(Rank::MEDIA) . "!");
		}

		Config::getInstance()->getRedeemConfig()->setNested("redeem_codes." . $code . ".status", "claimed");
		Config::getInstance()->getRedeemConfig()->save();
		Core::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use($code){
			$this->removeRedeemCode($code);
		}), 20 * 60); // secret method to expired the code immediately after 1 minutes of claim ;)
	}

	public function createRedeemCode(string $type, string $code) : void{
		Config::getInstance()->getRedeemConfig()->setNested("redeem_codes." . $code . ".status", "available");
		Config::getInstance()->getRedeemConfig()->setNested("redeem_codes." . $code . ".item", $type);
		Config::getInstance()->getRedeemConfig()->save();
		Config::getInstance()->getRedeemConfig()->reload();
	}

	public function removeRedeemCode(string $code) : void{
		Config::getInstance()->getRedeemConfig()->removeNested("redeem_codes." . $code);
		Config::getInstance()->getRedeemConfig()->save();
		Config::getInstance()->getRedeemConfig()->reload();
	}
}
