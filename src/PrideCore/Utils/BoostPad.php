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

namespace PrideCore\Utils;

use pocketmine\block\BlockTypeIds;
use pocketmine\utils\SingletonTrait;
use PrideCore\Player\Player;

class BoostPad {

	use SingletonTrait;

	public function getBoostValue() : float{
		return Config::getInstance()->getServerConfig()->getNested("boost-pad.power", 0.8);
	}

	public function getBoostValueLimit() : float{
		return Config::getInstance()->getServerConfig()->getNested("boost-pad.limit", 0.8);
	}

	public function checkIfCanBoost(Player $player) : void{
		$x = $player->getLocation()->getX();
		$y = $player->getLocation()->getY();
		$z = $player->getLocation()->getZ();
		$world = $player->getWorld();
		$block = $world->getBlock($player->getLocation()->getSide(0, 0));
		if($block->getTypeId() === BlockTypeIds::WEIGHTED_PRESSURE_PLATE_HEAVY || $block->getTypeId() === BlockTypeIds::WEIGHTED_PRESSURE_PLATE_LIGHT){
			$direction = $player->getDirectionVector();
			$dx = $direction->getX();
			$dz = $direction->getZ();
			$player->knockBack($dx, $dz, $this->getBoostValue(), $this->getBoostValueLimit());
			$player->playSound("mob.enderdragon.flap");
		}
	}
}
