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
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\math\Vector3;
use pocketmine\player\GameMode;
use PrideCore\Anticheat\Anticheat;
use PrideCore\Core;
use PrideCore\Player\Player;

class NoClip extends Anticheat implements Listener
{

	public function __construct()
	{
		parent::__construct(Anticheat::NOCLIP);
		Core::getInstance()->getServer()->getPluginManager()->registerEvents($this, Core::getInstance());
	}

	private array $lastMoveUpdates = [];

	public function onMove(PlayerMoveEvent $event) {
		$id = $event->getPlayer()->getWorld()->getBlock($event->getPlayer()->getLocation())->getTypeId();
		if ($event->getPlayer()->getWorld()->getBlock($event->getPlayer()->getLocation()->add(0, 1, 0))->isSolid() && $id !== BlockTypeIds::SAND && $id !== BlockTypeIds::GRAVEL && $event->getPlayer()->getGamemode() !== GameMode::SPECTATOR()) {
			$event->cancel();
			$this->fail($event->getPlayer());
			$event->getPlayer()->teleport(new Vector3($event->getPlayer()->getLocation()->getX(), ($event->getPlayer()->getWorld()->getHighestBlockAt($event->getPlayer()->getLocation()->getX(), $event->getPlayer()->getLocation()->getZ()) + 1), $event->getPlayer()->getLocation()->getZ()));
			return;
		}
		$this->lastMoveUpdates[$event->getPlayer()->getName()] = $event->getTo();
	}

	public function onTeleport(EntityTeleportEvent $event) {
		if (!$event->getEntity() instanceof Player) return;
		$this->lastMoveUpdates[$event->getEntity()->getName()] = $event->getTo();
	}

}
