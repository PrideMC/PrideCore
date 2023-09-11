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
		if ($event->getPlayer()->getWorld()->getBlock($event->getPlayer()->getLocation()->add(0, 1, 0))->isSolid() && $id !== BlockTypeIds::SAND && $id !== BlockTypeIds::GRAVEL && $event->getPlayer()->getGamemode() !== GameMode::SPECTATOR()) {
			$event->cancel();
			$this->fail($event->getPlayer());
			return;
		}
	}

}
