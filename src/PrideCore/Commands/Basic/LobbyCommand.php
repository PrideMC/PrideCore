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
use PrideCore\Player\Inventory;
use PrideCore\Player\Player;
use PrideCore\Utils\TeleportScreen;

/**
 * Teleport to lobby.
 */
class LobbyCommand extends Command implements PluginOwned
{

	public function getOwningPlugin() : Core
	{
		return Core::getInstance();
	}

	public function __construct()
	{
		parent::__construct("lobby", "Back to Lobby", "/lobby", ["hub","spawn"]);
		$this->setPermission("pride.basic.command");
	}

	public function execute(CommandSender $sender, string $label, array $args) : void
	{
		if (!$sender instanceof Player) {
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . TF::RED . " Sorry, this can be only executed as a player.");
			return;
		}

		if($sender->getWorld()->getFolderName() === $sender->getServer()->getWorldManager()->getDefaultWorld()->getFolderName()){
			$sender->teleport($sender->getServer()->getWorldManager()->getDefaultWorld()->getSpawnLocation());
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . TF::GREEN . " You have been teleport back to the lobby.");
		} else {
			TeleportScreen::getInstance($sender, $sender->getServer()->getWorldManager()->getDefaultWorld());
			$sender->removeOnOtherServer();
		}
		Inventory::lobbyInventory($sender);
	}
}
