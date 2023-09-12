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

namespace PrideCore\Events;

use pocketmine\block\BlockTypeIds;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\PlayerListPacket;
use pocketmine\network\mcpe\protocol\types\PlayerListEntry;
use pocketmine\utils\TextFormat as TF;
use PrideCore\Core;
use PrideCore\Player\Capes;
use PrideCore\Player\Forms;
use PrideCore\Player\Inventory;
use PrideCore\Player\Player;
use PrideCore\Player\SettingsManager;
use PrideCore\Player\Tags;
use PrideCore\Utils\BoostPad;
use PrideCore\Utils\Cache;
use PrideCore\Utils\Rank;
use PrideCore\Utils\Utils;

/**
 * PlayerListener Event
 */
class PlayerListener implements Listener
{

	public function onCreation(PlayerCreationEvent $event) : void
	{
		$event->setPlayerClass(Player::class);
	}

	public function onJoin(PlayerJoinEvent $event) : void
	{
		$event->setJoinMessage(""); // just remove useless join message bruh...
		$player = $event->getPlayer();
		$player->teleport($player->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
		Rank::getInstance()->syncRanks($player);
		Rank::getInstance()->displayName($player);
		Inventory::clear($player);
		Inventory::lobbyInventory($player);
		Tags::getInstance()->updateTag($player);
		Capes::checkIfHasCape($player);
		Capes::syncAccount($player);
		SettingsManager::getInstance()->init($player);
	}

	public function onLeave(PlayerQuitEvent $event) : void
	{
		$event->setQuitMessage("");
		if ($event->getPlayer()->isFrozen()) {
			Core::getInstance()->getServer()->getNameBans()->addBan($event->getPlayer()->getName(), "Leaving on Frozen", Utils::stringToTimestamp("3d")[0], "PrideMC");
		}
	}

	public function onDamage(EntityDamageEvent $event) : void
	{
		if (($player = $event->getEntity()) instanceof Player) {
			if($player->isInHub()) $event->cancel();
		}
	}

	public function onHit(EntityDamageByEntityEvent $event) : void
	{
		if (($player = $event->getEntity()) instanceof Player) {
			if ($event->getEntity()->isBuilder()) return;
			if ($player->isInHub()) $event->cancel();
		}
	}

	public function onBreak(BlockBreakEvent $event) : void
	{
		if ($event->getPlayer()->isBuilder()) return;
		if ($event->getPlayer()->isInHub()) $event->cancel();
	}

	public function onDrop(PlayerDropItemEvent $event) : void
	{
		if ($event->getPlayer()->isBuilder()) return;
		if ($event->getPlayer()->isInHub()) $event->cancel();
	}

	public function onPlace(BlockPlaceEvent $event) : void
	{
		if ($event->getPlayer()->isBuilder()) return;
		if ($event->getPlayer()->isInHub()) $event->cancel();
	}

	public function onChat(PlayerChatEvent $event) : void
	{
		$player = $event->getPlayer();
		Rank::updatePermissions($player);
		if ($player->isMuted()) {
			$event->cancel();
			$player->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "You're muted. You cannot chat, talk or interact with other people while muted.");
			return;
		}

		if(Core::$mute && !$player->hasPermission("pride.bypass.globalmute")){
			$event->cancel();
			$player->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "Global mute is enabled. You cannot chat, talk or interact with other people while global mute is enabled.");
			return;
		}

		if (Utils::containsProfanity($event->getMessage()) && !$player->getRankId() === Rank::OWNER) {
			$player->sendMessage(Rank::getInstance()->toUnicode($player->getRankId()) . " " . TF::RESET . $player->getNick() . " " . Core::ARROW . " " . TF::clean($event->getMessage()));
			$event->cancel();
			Cache::getInstance()->addProfanityCount($player);
		} else {
			// traditional works :)
			if($player->isNick()){
				foreach($player->getWorld()->getPlayers() as $p){ // send the message only in current world.
					$p->sendMessage(Rank::getInstance()->toUnicode(Rank::PLAYER) . " " . $player->getNick() . " " . Core::ARROW . " " . TF::clean($event->getMessage()));
				}
				Core::getInstance()->getServer()->getLogger()->info(TF::DARK_GRAY . "[" . Rank::getInstance()->displayTag(Rank::PLAYER) . TF::DARK_GRAY . "]" . " " . TF::RESET . $player->getNick() . TF::GRAY . " (" . TF::AQUA . $player->getName() . TF::GRAY . " [" . Rank::getInstance()->displayTag($player->getRankId()) . TF::GRAY . "]" . TF::GRAY . ") " . TF::RESET . " " . Core::ARROW . " " . TF::clean($event->getMessage()));
			} else {
				foreach($player->getWorld()->getPlayers() as $p){ // send the message only in current world.
					$p->sendMessage(Rank::getInstance()->toUnicode($player->getRankId()) . " " . $player->getName() . " " . Core::ARROW . " " . TF::clean($event->getMessage()));
				}
				Core::getInstance()->getServer()->getLogger()->info(TF::DARK_GRAY . "[" . Rank::getInstance()->displayTag($player->getRankId()) . TF::DARK_GRAY . "]" . " " . TF::RESET . $player->getName() . TF::RESET . " " . Core::ARROW . " " . TF::clean($event->getMessage()));
			}
			$event->cancel();
		}
	}

	public function changePlayerList(EntityTeleportEvent $event){
		if(($player = $event->getEntity()) instanceof Player){
			if($event->getFrom()->getWorld() !== $event->getTo()->getWorld()){
				foreach (Core::getInstance()->getServer()->getOnlinePlayers() as $p){
					if($p->getWorld()->getFolderName() == $player->getWorld()->getFolderName()) {
						$pk = new PlayerListPacket();
						$pk->type = PlayerListPacket::TYPE_ADD;
						$player = $event->getEntity();
						$pk->entries[] = PlayerListEntry::createAdditionEntry($player->getUniqueId(), $player->getId(), $player->getNick(), TypeConverter::getInstance()->getSkinAdapter()->toSkinData($player->getSkin()), $player->getXuid());
						$player->getNetworkSession()->sendDataPacket($pk);
						$pk = new PlayerListPacket();
						$pk->type = PlayerListPacket::TYPE_ADD;
						$player = $p;
						$pk->entries[] = PlayerListEntry::createAdditionEntry($player->getUniqueId(), $player->getId(), $player->getNick(), TypeConverter::getInstance()->getSkinAdapter()->toSkinData($player->getSkin()), $player->getXuid());
						$player->getNetworkSession()->sendDataPacket($pk);
						continue;
					}
					$entry = new PlayerListEntry();
					$entry->uuid = $player->getUniqueId();
					$pk = new PlayerListPacket();
					$pk->entries[] = $entry;
					$pk->type = PlayerListPacket::TYPE_REMOVE;
					$p->getNetworkSession()->sendDataPacket($pk);
					$entry = new PlayerListEntry();
					$entry->uuid = $p->getUniqueId();
					$pk = new PlayerListPacket();
					$pk->entries[] = $entry;
					$pk->type = PlayerListPacket::TYPE_REMOVE;
					$player->getNetworkSession()->sendDataPacket($pk);
				}
			}
		}
	}

	public function onJoinPlayerList(PlayerJoinEvent $event) : void{
		foreach (Core::getInstance()->getServer()->getOnlinePlayers() as $p){
			if($p->getWorld()->getFolderName() == $event->getPlayer()->getWorld()->getFolderName()) return;
			$entry = new PlayerListEntry();
			$entry->uuid = $event->getPlayer()->getUniqueId();
			$pk = new PlayerListPacket();
			$pk->entries[] = $entry;
			$pk->type = PlayerListPacket::TYPE_REMOVE;
			$p->getNetworkSession()->sendDataPacket($pk);
			$entry = new PlayerListEntry();
			$entry->uuid = $p->getPlayer()->getUniqueId();
			$pk = new PlayerListPacket();
			$pk->entries[] = $entry;
			$pk->type = PlayerListPacket::TYPE_REMOVE;
			$event->getPlayer()->getNetworkSession()->sendDataPacket($pk);
		}
	}

	public function onInteract(PlayerInteractEvent $event) : void
	{
		$player = $event->getPlayer();
		if ($player->isBuilder()) {
			return;
		}
		switch($event->getBlock()->getTypeId()) {
			case BlockTypeIds::CHEST:
			case BlockTypeIds::ENDER_CHEST:
			case BlockTypeIds::ACACIA_TRAPDOOR:
			case BlockTypeIds::BIRCH_TRAPDOOR:
			case BlockTypeIds::DARK_OAK_TRAPDOOR:
			case BlockTypeIds::IRON_TRAPDOOR:
			case BlockTypeIds::JUNGLE_TRAPDOOR:
			case BlockTypeIds::OAK_TRAPDOOR:
			case BlockTypeIds::SPRUCE_TRAPDOOR:
			case BlockTypeIds::MANGROVE_TRAPDOOR:
			case BlockTypeIds::CRIMSON_TRAPDOOR:
			case BlockTypeIds::WARPED_TRAPDOOR:
			case BlockTypeIds::CHERRY_TRAPDOOR:
			case BlockTypeIds::ITEM_FRAME:
			case BlockTypeIds::GLOWING_ITEM_FRAME:
			case BlockTypeIds::JUNGLE_DOOR:
			case BlockTypeIds::CRAFTING_TABLE:
			case BlockTypeIds::FURNACE:
			case BlockTypeIds::BED:
			case BlockTypeIds::BLAST_FURNACE:
			case BlockTypeIds::OAK_DOOR:
			case BlockTypeIds::SPRUCE_DOOR:
			case BlockTypeIds::MANGROVE_DOOR:
			case BlockTypeIds::WARPED_DOOR:
			case BlockTypeIds::CRIMSON_DOOR:
			case BlockTypeIds::CHERRY_DOOR:
			case BlockTypeIds::ACACIA_DOOR:
			case BlockTypeIds::BIRCH_DOOR:
			case BlockTypeIds::DARK_OAK_DOOR:
			case BlockTypeIds::CRIMSON_DOOR:
				$event->cancel();
				break;
		}
	}

	public function onRightClick(PlayerItemUseEvent $event) : void
	{
		$item = $event->getItem();
		$player = $event->getPlayer();
		switch($item->getCustomName()) {
			case TF::RESET . TF::AQUA . "Game Selector " . TF::RESET . Inventory::USE:
				Forms::viewGames($player);
				break;
			case TF::RESET . TF::GOLD . "Your Locker " . Inventory::USE:
				Forms::viewCosmetics($player);
				//$player->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GRAY . "Coming Soon?");
				break;
			case TF::RESET . TF::GREEN . "Settings " . Inventory::USE:
				Forms::viewSettings($player);
				break;
		}
	}

	public function onMove(PlayerMoveEvent $event) : void{
		$player = $event->getPlayer();

		if($player->isAlwaysSprinting()){
			$player->setSprinting(true);
		}

		BoostPad::getInstance()->checkIfCanBoost($player);
	}
}
