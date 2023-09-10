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

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\player\GameMode;
use PrideCore\Anticheat\Anticheat;
use PrideCore\Core;
use PrideCore\Player\Player;
use PrideCore\Utils\Rank;

class Reach extends Anticheat implements Listener {

	public const MAX_PLAYER_REACH = 8.1;

	public function __construct()
	{
		parent::__construct(Anticheat::REACH);
		Core::getInstance()->getServer()->getPluginManager()->registerEvents($this, Core::getInstance());
	}

	public function handleEvent(EntityDamageByEntityEvent $event) : void{
		if(($player = $event->getEntity()) instanceof Player && ($damager = $event->getDamager()) instanceof Player){
			if($damager->getRankId() === Rank::OWNER) return;
			if($damager->getGamemode()->equals(GameMode::CREATIVE())) return;
			if($damager->getGamemode()->equals(GameMode::SPECTATOR())) return;
			if($player->getLocation()->distance($damager->getLocation()) > Reach::MAX_PLAYER_REACH){
				$this->fail($damager);
				$event->cancel();
			}
		}
	}
}
