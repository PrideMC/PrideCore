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
use PrideCore\Utils\Rank;

use function count;

/**
 * Maintenance the server!
 */
class MaintenanceCommand extends Command implements PluginOwned
{

	public function getOwningPlugin() : Core
	{
		return Core::getInstance();
	}

	public function __construct()
	{
		parent::__construct("maintenance", "Change Server Status to Maintenance Mode.", "/maintenance <on/off>");
		$this->setPermission("pride.staff.maintenance");
	}

	public function execute(CommandSender $sender, string $label, array $args) : void
	{
		if (!$this->testPermission($sender)) {
			return;
		}
		if (count($args) === 0) {
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "Usage: " . $this->usageMessage);
			return;
		}

		if ($args[0] === "on") {
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "Successfully set status to on the maintenance mode.");
			Core::$maintenance = true;
			foreach(Core::getInstance()->getServer()->getOnlinePlayers() as $player){
				if(!Core::getInstance()->getServer()->isOp($player->getName()) || !$player->getRankId() === Rank::OWNER || !$player->getRankId() === Rank::STAFF){
					$player->kick(Core::PREFIX . TF::GRAY . "\nSorry, We're going on the maintenance.\n\nCheck back later if available soon to join.");
				}
			}
			return;
		} elseif ($args[0] === "off") {
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "Successfully set status to off the maintenance mode.");
			Core::$maintenance = false;
			return;
		} else {
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "Usage: " . $this->usageMessage);
			return;
		}
	}
}
