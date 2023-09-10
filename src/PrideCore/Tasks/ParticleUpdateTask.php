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

namespace PrideCore\Tasks;

use pocketmine\scheduler\Task;
use PrideCore\Core;
use PrideCore\Player\Particles;

/**
 * Update particles of player.
 */
class ParticleUpdateTask extends Task {

	public function onRun() : void {
		foreach(Core::getInstance()->getServer()->getOnlinePlayers() as $player){
			if(!$player->getActiveParticle() === Particles::NONE){
				switch($player->getParticleType()){
					case Particles::TRAIL:
						Particles::getInstance()->displayTrailParticle($player, $player->getActiveParticle());
						break;
					case Particles::SPIRAL:
						Particles::getInstance()->displaySpiralParticle($player, $player->getActiveParticle());
						break;
					case Particles::WING:
						Particles::getInstance()->displayWingParticle($player, $player->getActiveParticle());
						break;
				}
			}
		}
	}
}
