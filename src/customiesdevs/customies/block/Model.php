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

namespace customiesdevs\customies\block;

use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;

final class Model {

	/** @var Material[] */
	private array $materials;
	private ?string $geometry;
	private Vector3 $origin;
	private Vector3 $size;

	/**
	 * @param Material[] $materials
	 */
	public function __construct(array $materials, ?string $geometry = null, ?Vector3 $origin = null, ?Vector3 $size = null) {
		$this->materials = $materials;
		$this->geometry = $geometry;
		$this->origin = $origin ?? Vector3::zero();
		$this->size = $size ?? Vector3::zero();
	}

	/**
	 * Returns the model in the correct NBT format supported by the client.
	 * @return CompoundTag[]
	 */
	public function toNBT() : array {
		$materials = CompoundTag::create();
		foreach($this->materials as $material){
			$materials->setTag($material->getTarget(), $material->toNBT());
		}

		$material = [
			"minecraft:material_instances" => CompoundTag::create()
				->setTag("mappings", CompoundTag::create()) // What is this? The client will crash if it is not sent.
				->setTag("materials", $materials),
		];
		if($this->geometry === null) {
			$material["minecraft:unit_cube"] = CompoundTag::create();
		} else {
			$material["minecraft:geometry"] = CompoundTag::create()
				->setString("identifier", $this->geometry);
			$material["minecraft:collision_box"] = CompoundTag::create()
				->setByte("enabled", 1)
				->setTag("origin", new ListTag([
					new FloatTag($this->origin->getX()),
					new FloatTag($this->origin->getY()),
					new FloatTag($this->origin->getZ())
				]))
				->setTag("size", new ListTag([
					new FloatTag($this->size->getX()),
					new FloatTag($this->size->getY()),
					new FloatTag($this->size->getZ())
				]));
			$material["minecraft:selection_box"] = CompoundTag::create()
				->setByte("enabled", 1)
				->setTag("origin", new ListTag([
					new FloatTag($this->origin->getX()),
					new FloatTag($this->origin->getY()),
					new FloatTag($this->origin->getZ())
				]))
				->setTag("size", new ListTag([
					new FloatTag($this->size->getX()),
					new FloatTag($this->size->getY()),
					new FloatTag($this->size->getZ())
				]));
		}
		return $material;
	}
}
