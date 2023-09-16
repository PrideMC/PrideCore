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

namespace PrideCore\Anticheat\Modules;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use PrideCore\Anticheat\Anticheat;
use PrideCore\Player\Player;
use PrideGames\LegitHacks\Main as LegitHacks;
use function array_filter;
use function array_pop;
use function array_unshift;
use function count;
use function microtime;
use function round;

class AutoClicker extends Anticheat implements Listener{

	public function __construct()
	{
		parent::__construct(Anticheat::AUTOCLICKER);
	}

	public const MAX_CLICKS = 40.0;

	public function onDataPacketReceive(DataPacketReceiveEvent $ev) : void
	{
		$player = $ev->getOrigin()->getPlayer();
		$packet = $ev->getPacket();
		if ($player !== null && $player->isOnline()) {
			if(LegitHacks::getInstance()->hasKillaura($player)) return;
			switch ($packet->pid()) {
				case AnimatePacket::NETWORK_ID:
					switch ($packet->action) {
						case AnimatePacket::ACTION_SWING_ARM:
							$this->addCps($player);

							if($this->getCps($player) > AutoClicker::MAX_CLICKS){
								$this->fail($player); // this will automatically detect when player is using autoclicker.
							}
							break;
					}
					break;
			}
		}
	}

	public array $clicks = [];

	public function addCps(Player $player) : void
	{
		array_unshift($this->clicks[$player->getUniqueId()->getBytes()], microtime(true));
		if(count($this->clicks[$player->getUniqueId()->getBytes()]) >= 100) array_pop($this->clicks[$player->getUniqueId()->getBytes()]);
	}

	public function getCps(Player $player) : float
	{
		if(empty($this->clicks[$player->getUniqueId()->getBytes()])){
			return 0.0;
		}
		$ct = microtime(true);
		return round(count(array_filter($this->clicks[$player->getUniqueId()->getBytes()], static function(float $t) use ($ct) : bool{
				return ($ct - $t) <= 1.0;
			})) / 1.0, 1);
	}
}
