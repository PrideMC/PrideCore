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

use pocketmine\entity\Entity;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\Position;
use PrideCore\Player\Player;

class EntityManager {

	use SingletonTrait;

	public array $ft = [];

	public array $npc = [];

	public function spawnFloatingText(Player $player, string $text, ?Position $position = null) : void{
		$ft = new FloatingText($text, ($position ?? $player->getPosition()), Entity::nextRuntimeId());
		$ft->spawnTo($player);
		$this->ft[] = $ft;
	}

	public function getAllNPC() : array {
		return $this->npc;
	}

	public function getNPCById(int $id) : ?NPC{
		foreach($this->npc as $entity){
			if($entity->getRuntimeId() === $id){
				return $entity;
			}
		}
		return null;
	}

	public function spawnNPC(Player $player, string $nametag, float $scale = 1.0) : void{
		$nbt = CompoundTag::create()
			->setTag("Pos", new ListTag([
				new DoubleTag($player->getLocation()->x),
				new DoubleTag($player->getLocation()->y),
				new DoubleTag($player->getLocation()->z)
			]))
			->setTag("Motion", new ListTag([
				new DoubleTag(0.0),
				new DoubleTag(0.0),
				new DoubleTag(0.0)
			]))
			->setTag("Rotation", new ListTag([
				new FloatTag($player->getLocation()->getYaw()),
				new FloatTag($player->getLocation()->getPitch())
			]));
		$nbt->setTag("Skin", CompoundTag::create()
		->setString("Name", $player->getPlayerInfo()->getSkin()->getSkinData())
		->setByteArray("CapeData", $player->getPlayerInfo()->getSkin()->getCapeData())
		->setString("GeometryName", $player->getPlayerInfo()->getSkin()->getGeometryName())
		->setByteArray("GeometryData", $player->getPlayerInfo()->getSkin()->getGeometryData())
		);

		$entity = new NPC($player->getLocation(), $player->getPlayerInfo()->getSkin(), $nbt);
		$entity->setNametag($nametag);
		$entity->setScale($scale);
		$entity->setNameTagAlwaysVisible(true);
		$entity->spawnToAll();

		$this->npc[] = $entity;
	}

	public function getAllFloatingText() : array{
		return $this->ft;
	}
}
