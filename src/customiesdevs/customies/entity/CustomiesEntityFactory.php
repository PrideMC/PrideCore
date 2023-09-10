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

namespace customiesdevs\customies\entity;

use Closure;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\cache\StaticPacketCache;
use pocketmine\network\mcpe\protocol\AvailableActorIdentifiersPacket;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;
use ReflectionClass;

class CustomiesEntityFactory {
	use SingletonTrait;

	/**
	 * Register an entity to the EntityFactory and all the required mappings. An optional behaviour identifier can be
	 * provided if you want to have your entity behave like a vanilla entity.
	 * @phpstan-param class-string<Entity> $className
	 * @phpstan-param Closure(World $world, CompoundTag $nbt) : Entity $creationFunc
	 */
	public function registerEntity(string $className, string $identifier, ?Closure $creationFunc = null, string $behaviourId = "") : void {
		EntityFactory::getInstance()->register($className, $creationFunc ?? static function (World $world, CompoundTag $nbt) use ($className) : Entity {
			return new $className(EntityDataHelper::parseLocation($nbt, $world), $nbt);
		}, [$identifier]);
		$this->updateStaticPacketCache($identifier, $behaviourId);
	}

	private function updateStaticPacketCache(string $identifier, string $behaviourId) : void {
		$instance = StaticPacketCache::getInstance();
		$property = (new ReflectionClass($instance))->getProperty("availableActorIdentifiers");
		/** @var AvailableActorIdentifiersPacket $packet */
		$packet = $property->getValue($instance);
		/** @var CompoundTag $root */
		$root = $packet->identifiers->getRoot();
		($root->getListTag("idlist") ?? new ListTag())->push(CompoundTag::create()
			->setString("id", $identifier)
			->setString("bid", $behaviourId));
		$packet->identifiers = new CacheableNbt($root);
	}
}
