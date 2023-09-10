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
use PrideCore\Player\Forms;
use PrideCore\Player\Player;
use PrideCore\Utils\Rank;

use function count;

/**
 * Manage ranks via command.
 */
class RankCommand extends Command implements PluginOwned
{

	public function getOwningPlugin() : Core
	{
		return Core::getInstance();
	}

	public function __construct()
	{
		parent::__construct("rank", "View Ranks", "/rank", ["vip","plus","mvp"]);
		$this->setPermission("pride.staff.rank");
	}

	public function execute(CommandSender $sender, string $label, array $args) : void
	{
		if (!$this->testPermission($sender)) {
			return;
		}

		if(isset($args[0]) && $args[0] === "set"){
			if(!isset($args[1])){
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . TF::RED . " Please enter the username of player.");
				return;
			}

			if(($target = Core::getInstance()->getServer()->getPlayerExact($args[1])) !== null){
				if(!isset($args[2])){
					$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . TF::RED . " Please enter the rank id/name of the rank.");
					return;
				}

				if(($id = Rank::getInstance()->idConverter($args[2])) === false){
					$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . TF::RED . " Invalid rank id or name.");
					return;
				}

				Rank::getInstance()->setRank($target, $id);
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "Successfully changed rank of " . $target->getName() . " to " . Rank::getInstance()->displayTag($id) . TF::RESET . TF::GREEN . " Rank.");
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "Successfully changed your rank to " . Rank::getInstance()->displayTag($id) . TF::RESET . TF::GREEN . " Rank.");
				return;
			} else {
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . TF::RED . " We couldn't find the player in the server.");
			}
			return;
		}

		if (!$sender instanceof Player) {
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . TF::RED . " Sorry, this can be only executed as a player.");
			return;
		}
		if (count($args) === 0) {
			Forms::viewRanks($sender);
			return;
		}

		if ($sender->hasPermission("pride.staff.rank")) {
			if ($args[0] === "manage") {
				Forms::manageRanks($sender);
				return;
			}
		}
		 else {
			Forms::viewRanks($sender);
			return;
		}

		Forms::viewRanks($sender); // loop?
	}
}
