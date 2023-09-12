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

namespace PrideCore\Anticheat\Modules;

use pocketmine\block\BlockTypeIds;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\player\GameMode;
use PrideCore\Anticheat\Anticheat;
use PrideCore\Core;

class NoClip extends Anticheat implements Listener
{

	public function __construct()
	{
		parent::__construct(Anticheat::NOCLIP);
		Core::getInstance()->getServer()->getPluginManager()->registerEvents($this, Core::getInstance());
	}

	public function onMove(PlayerMoveEvent $event) {
		$id = $event->getPlayer()->getWorld()->getBlock($event->getPlayer()->getLocation())->getTypeId();
		if ($event->getPlayer()->getWorld()->getBlock($event->getPlayer()->getLocation()->add(0, 1, 0))->isSolid() && $id !== BlockTypeIds::SAND && $id !== BlockTypeIds::GRAVEL && $id !== BlockTypeIds::ACACIA_FENCE && $id !== BlockTypeIds::OAK_FENCE && $id !== BlockTypeIds::BIRCH_FENCE && $id !== BlockTypeIds::DARK_OAK_FENCE && $id !== BlockTypeIds::JUNGLE_FENCE && $id !== BlockTypeIds::NETHER_BRICK_FENCE && $id !== BlockTypeIds::SPRUCE_FENCE && $id !== BlockTypeIds::WARPED_FENCE && $id !== BlockTypeIds::MANGROVE_FENCE && $id !== BlockTypeIds::CRIMSON_FENCE && $id !== BlockTypeIds::CHERRY_FENCE  && $id !== BlockTypeIds::ACACIA_FENCE_GATE && $id !== BlockTypeIds::OAK_FENCE_GATE && $id !== BlockTypeIds::BIRCH_FENCE_GATE && $id !== BlockTypeIds::DARK_OAK_FENCE_GATE && $id !== BlockTypeIds::JUNGLE_FENCE_GATE && $id !== BlockTypeIds::SPRUCE_FENCE_GATE && $id !== BlockTypeIds::WARPED_FENCE_GATE && $id !== BlockTypeIds::MANGROVE_FENCE_GATE && $id !== BlockTypeIds::CRIMSON_FENCE_GATE && $id !== BlockTypeIds::CHERRY_FENCE_GATE && $event->getPlayer()->getGamemode() !== GameMode::SPECTATOR()) {
			$event->cancel();
			$this->fail($event->getPlayer());
			return;
		}
	}

}
