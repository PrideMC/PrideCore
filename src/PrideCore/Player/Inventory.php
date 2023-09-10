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

namespace PrideCore\Player;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat as TF;

/**
 * Player inventory related...
 */
class Inventory
{
	public const USE = TF::RESET . TF::GRAY . "[" . TF::GREEN . "Use" . TF::GRAY . "]" . TF::RESET;

	public static function lobbyInventory(Player $player) : void
	{
		Inventory::clear($player);
		$inv = $player->getInventory();
		$inv->setItem(0, VanillaItems::COMPASS()->setCustomName(TF::RESET . TF::AQUA . "Game Selector " . TF::RESET . Inventory::USE));
		$inv->setItem(7, VanillaItems::HEART_OF_THE_SEA()->setCustomName(TF::RESET . TF::GREEN . "Settings " . Inventory::USE));
		$inv->setItem(8, VanillaBlocks::ENDER_CHEST()->asItem()->setCustomName(TF::RESET . TF::GOLD . "Your Locker " . Inventory::USE));
	}

	public static function clear(Player $player) : void
	{
		$player->getInventory()->clearAll();
		$player->getEffects()->clear();
		$player->getHungerManager()->setFood($player->getHungerManager()->getMaxFood());
		$player->setAbsorption(0);
		$player->setHealth(20);
		$player->setMaxHealth(20);
	}
}
