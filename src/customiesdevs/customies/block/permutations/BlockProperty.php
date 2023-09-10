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

namespace customiesdevs\customies\block\permutations;

use customiesdevs\customies\util\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use function array_map;

final class BlockProperty {

	public function __construct(private readonly string $name, private readonly array $values) { }

	/**
	 * Returns the name of the block property provided in the constructor.
	 */
	public function getName() : string {
		return $this->name;
	}

	/**
	 * Returns the array of possible values of the block property provided in the constructor.
	 */
	public function getValues() : array {
		return $this->values;
	}

	/**
	 * Returns the block property in the correct NBT format supported by the client.
	 */
	public function toNBT() : CompoundTag {
		$values = array_map(static fn($value) => NBT::getTagType($value), $this->values);
		return CompoundTag::create()
			->setString("name", $this->name)
			->setTag("enum", new ListTag($values));
	}
}
