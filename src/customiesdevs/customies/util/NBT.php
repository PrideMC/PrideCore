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

namespace customiesdevs\customies\util;

use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\Tag;
use function array_keys;
use function array_map;
use function count;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_string;
use function range;

class NBT {

	/**
	 * Attempts to return the correct Tag for the provided type.
	 */
	public static function getTagType($type) : ?Tag {
		return match (true) {
			is_array($type) => self::getArrayTag($type),
			is_bool($type) => new ByteTag($type ? 1 : 0),
			is_float($type) => new FloatTag($type),
			is_int($type) => new IntTag($type),
			is_string($type) => new StringTag($type),
			$type instanceof CompoundTag => $type,
			default => null,
		};
	}

	/**
	 * Creates a Tag that is either a ListTag or CompoundTag based on the data types of the keys in the provided array.
	 */
	private static function getArrayTag(array $array) : Tag {
		if(array_keys($array) === range(0, count($array) - 1)) {
			return new ListTag(array_map(fn($value) => self::getTagType($value), $array));
		}
		$tag = CompoundTag::create();
		foreach($array as $key => $value){
			$tag->setTag($key, self::getTagType($value));
		}
		return $tag;
	}
}
