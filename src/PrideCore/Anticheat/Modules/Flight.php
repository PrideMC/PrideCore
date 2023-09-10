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

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use PrideCore\Anticheat\Anticheat;
use PrideCore\Core;

class Flight extends Anticheat implements Listener{

	public const FLIGHT_MAX_MOVE = 8.0;

	private array $lastLocation = [];

	public function __construct()
	{
		parent::__construct(Anticheat::FLIGHT);
		Core::getInstance()->getServer()->getPluginManager()->registerEvents($this, Core::getInstance());
	}

	public function handleEvent(PlayerMoveEvent $event) : void{
		if(!($player = $event->getPlayer())->getAllowFlight()){ // improve this soon... most likely a bad code.
			$this->lastLocation[$player->getUniqueId()->__toString()] = ["x" => $player->getLocation()->getX(), "y" => $player->getLocation()->getY(), "z" => $player->getLocation()->getY()];
			if(!$player->isFlying() && Anticheat::areAllBlocksAboveAir($player)){
				if($player->getLocation()->getX() !== $this->lastLocation[$player->getUniqueId()->__toString()]["x"] && $player->getLocation()->getZ() !== $this->lastLocation[$player->getUniqueId()->__toString()]["z"] && $player->getLocation()->getY() === $this->lastLocation[$player->getUniqueId()->__toString()]["y"]){
					$this->fail($player);
					$event->cancel();
				}
			}
		}
	}
}
