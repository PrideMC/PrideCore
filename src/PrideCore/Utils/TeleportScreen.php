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

namespace PrideCore\Utils;

use pocketmine\network\mcpe\protocol\ChangeDimensionPacket;
use pocketmine\network\mcpe\protocol\PlayStatusPacket;
use pocketmine\network\mcpe\protocol\types\DimensionIds;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;
use PrideCore\Core;
use PrideCore\Player\Player;

/**
 * Fancy teleport screen handler.
 */
class TeleportScreen {

	use SingletonTrait;

	public function teleport(Player $player, World $world) : void{
		if($player->isOnOtherServer()){
			$player->removeOnOtherServer();
		}
		foreach($player->getServer()->getOnlinePlayers() as $p){
			$p->hidePlayer($player);
		}
		$player->setNoClientPredictions(true);

		Core::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($player){
			$packet = new ChangeDimensionPacket();
			$packet->dimension = DimensionIds::NETHER;
			$packet->position = $player->getLocation()->asVector3();
			$packet->respawn = true;
			$player->getNetworkSession()->sendDataPacket($packet);
			Core::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($player){
				$pk = new PlayStatusPacket();
				$pk->status = PlayStatusPacket::PLAYER_SPAWN;
				$player->getNetworkSession()->sendDataPacket($pk);
				$packet = new ChangeDimensionPacket(); //I tried doing that with a delay but the same thing happened
				$packet->dimension = DimensionIds::OVERWORLD;
				$packet->position = $player->getLocation()->asVector3();
				$packet->respawn = true;
				$player->getNetworkSession()->sendDataPacket($packet);
				Core::getInstance()->getScheduler()->scheduleDelayedTaskS(new ClosureTask(function() use ($player){
					$pk = new PlayStatusPacket();
					$pk->status = PlayStatusPacket::PLAYER_SPAWN;
					$player->getNetworkSession()->sendDataPacket($pk);
				}), 10); //show loading screen again for 0.5 seconds
			}), 20); //show loading screen for 1 second
		}), 20); // add a magic delay like nethergames...

		foreach($player->getServer()->getOnlinePlayers() as $p){
			$p->showPlayer($player);
		}
		$player->setNoClientPredictions(false);
		$player->teleport($world->getSpawnLocation());
		$player->setOnOtherServer($world->getFolderName());
	}
}
