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
		if ($event->getPlayer()->getWorld()->getBlock($event->getPlayer()->getLocation()->add(0, 1, 0))->isSolid() && $event->getPlayer()->getGamemode() !== GameMode::SPECTATOR()) {
			switch($id){
				// Anti-false positive on falling blocks
				case BlockTypeIds::SAND:
				case BlockTypeIds::GRAVEL:
				// Prevent false positive on fence & fence gates
				case BlockTypeIds::ACACIA_FENCE:
				case BlockTypeIds::OAK_FENCE:
				case BlockTypeIds::BIRCH_FENCE:
				case BlockTypeIds::DARK_OAK_FENCE:
				case BlockTypeIds::JUNGLE_FENCE:
				case BlockTypeIds::NETHER_BRICK_FENCE:
				case BlockTypeIds::SPRUCE_FENCE:
				case BlockTypeIds::WARPED_FENCE:
				case BlockTypeIds::MANGROVE_FENCE:
				case BlockTypeIds::CRIMSON_FENCE:
				case BlockTypeIds::CHERRY_FENCE:
				case BlockTypeIds::ACACIA_FENCE_GATE:
				case BlockTypeIds::OAK_FENCE_GATE:
				case BlockTypeIds::BIRCH_FENCE_GATE:
				case BlockTypeIds::DARK_OAK_FENCE_GATE:
				case BlockTypeIds::JUNGLE_FENCE_GATE:
				case BlockTypeIds::SPRUCE_FENCE_GATE:
				case BlockTypeIds::WARPED_FENCE_GATE:
				case BlockTypeIds::MANGROVE_FENCE_GATE:
				case BlockTypeIds::CRIMSON_FENCE_GATE:
				case BlockTypeIds::CHERRY_FENCE_GATE:
				// prevent glitching on cobblestone walls
				case BlockTypeIds::COBBLESTONE_WALL:
				// Prevent false positive on glass panes and building blocks.
				case BlockTypeIds::GLASS_PANE:
				case BlockTypeIds::HARDENED_GLASS_PANE:
				case BlockTypeIds::STAINED_GLASS_PANE:
				case BlockTypeIds::STAINED_HARDENED_GLASS_PANE:
					$event->cancel();
					$this->fail($event->getPlayer());
					break;
			}
			return;
		}
	}

}
