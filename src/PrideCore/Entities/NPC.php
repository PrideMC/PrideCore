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

namespace PrideCore\Entities;

use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use PrideCore\Events\Entities\NPCHitEvent;

class NPC extends Human {

	public function attack(EntityDamageEvent $source) : void
	{
		if($source instanceof EntityDamageByEntityEvent){
			(new NPCHitEvent($source->getDamager()))->call(); // call event for checking
			$source->cancel();
		} else {
			$source->cancel(); // prevent unexpected damage of npc
		}
	}

	public function getDrops() : array
	{
		return [];
	}

	public function getHealth() : float
	{
		return 0.0;
	}

	public function kill() : void
	{
		if(!$this->isAlive()) return;

		$this->flagForDespawn();
	}
}
