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
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\Packet;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\player\GameMode;
use PrideCore\Anticheat\Anticheat;
use PrideCore\Core;
use PrideCore\Player\Player;
use PrideCore\Utils\Rank;
use function microtime;
use function spl_object_hash;

class Killaura extends Anticheat implements Listener {

	public function __construct()
	{
		parent::__construct(Anticheat::KILLAURA);
		Core::getInstance()->getServer()->getPluginManager()->registerEvents($this, Core::getInstance());
	}

	private array $lastEntity = [];
	private array $entities = [];
	private array $timer = [];

	public function handleEvent(EntityDamageByEntityEvent $event) : void{ // bruh worst anticheat
		if(($player = $event->getEntity()) instanceof Player && ($damager = $event->getDamager()) instanceof Player){
			if($damager->getRankId() === Rank::OWNER) return;
			if($damager->getGamemode()->equals(GameMode::CREATIVE())) return;
			if($damager->getGamemode()->equals(GameMode::SPECTATOR())) return;
			if(!isset($this->lastEntity[$damager->getUniqueId()->__toString()])) $this->lastEntity[$damager->getUniqueId()->__toString()] = spl_object_hash($player);
			if(!isset($this->entities[$damager->getUniqueId()->__toString()])) $this->lastEntity[$damager->getUniqueId()->__toString()] = 0;
			if(!isset($this->timer[$damager->getUniqueId()->__toString()])) $this->timer[$damager->getUniqueId()->__toString()] = microtime(true);

			if($this->lastEntity[$damager->getUniqueId()->__toString()] !== spl_object_hash($player)){
				if($this->lastEntity[$damager->getUniqueId()->__toString()]->distance($damager) > 2){
					if($this->timer[$damager->getUniqueId()->__toString()] - microtime(true) > 0.5){
						$event->cancel();
						$this->fail($damager);
					}
				}
				$this->entities[$damager->getUniqueId()->__toString()]++;
				$this->lastEntity[$damager->getUniqueId()->__toString()] = spl_object_hash($player);
				$this->timer[$damager->getUniqueId()->__toString()] = microtime(true);
			}
		}
	}

	// Commonly in Toolbox
	public function handlePackets(Packet $packet, Player $player) : void{
		if($player->getRankId() === Rank::OWNER) return;
		if($player->getGamemode()->equals(GameMode::CREATIVE())) return;
		if($player->getGamemode()->equals(GameMode::SPECTATOR())) return;
		if(!$packet instanceof DataPacket) return;
		$swing = null;
		if($packet instanceof AnimatePacket){
			if($packet->action === AnimatePacket::ACTION_SWING_ARM){
				$swing = true;
			} else {
				$swing = false; // player detected killaura. :>
			}
		}

		if($packet instanceof InventoryTransactionPacket && $packet->trData->getTypeId() === InventoryTransactionPacket::TYPE_USE_ITEM_ON_ENTITY && $packet->trData->getActionType() === UseItemOnEntityTransactionData::ACTION_ATTACK){
			if(!$swing && $swing !== null){
				$this->fail($player);
			}
		}
	}

	public function processEvent(DataPacketReceiveEvent $event) : void{
		if($event->getPacket() instanceof AnimatePacket || $event->getPacket() instanceof InventoryTransactionPacket){
			$this->handlePackets($event->getPacket(), $event->getOrigin()->getPlayer());
		}
	}

	public function destroyQuit(PlayerQuitEvent $event) : void{
		$player = $event->getPlayer();
		if(isset($this->lastEntity[$player->getUniqueId()->__toString()])) unset($this->lastEntity[$player->getUniqueId()->__toString()]);
		if(isset($this->entities[$player->getUniqueId()->__toString()])) unset($this->entities[$player->getUniqueId()->__toString()]);
		if(isset($this->timer[$player->getUniqueId()->__toString()])) unset($this->timer[$player->getUniqueId()->__toString()]);
	}
}
