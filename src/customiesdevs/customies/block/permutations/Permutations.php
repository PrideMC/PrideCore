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

namespace customiesdevs\customies\block\permutations;

use Exception;
use function array_map;
use function array_product;
use function count;
use function current;
use function next;
use function reset;

class Permutations {

	/**
	 * Attempts to return an array of block properties from the provided meta value based on the possible permutations
	 * of the block. An exception is thrown if the meta value does not match any combinations of all the block
	 * properties.
	 */
	public static function fromMeta(Permutable $block, int $meta) : array {
		$properties = self::getCartesianProduct(
			array_map(static fn(BlockProperty $blockProperty) => $blockProperty->getValues(), $block->getBlockProperties())
		)[$meta] ?? null;
		if($properties === null) {
			throw new Exception("Unable to calculate permutations from block meta: " . $meta);
		}
		return $properties;
	}

	/**
	 * Attempts to convert the block in to a meta value based on the possible permutations of the block. An exception is
	 * thrown if the state of the block is not a possible combination of all the block properties.
	 */
	public static function toMeta(Permutable $block) : int {
		$properties = self::getCartesianProduct(
			array_map(static fn(BlockProperty $blockProperty) => $blockProperty->getValues(), $block->getBlockProperties())
		);
		foreach($properties as $meta => $permutations){
			if($permutations === $block->getCurrentBlockProperties()) {
				return $meta;
			}
		}
		throw new Exception("Unable to calculate block meta from current permutations");
	}

	/**
	 * Returns the number of bits required to represent all the possible permutations of the block.
	 */
	public static function getStateBitmask(Permutable $block) : int {
		$possibleValues = array_map(static fn(BlockProperty $blockProperty) => $blockProperty->getValues(), $block->getBlockProperties());
		return count(self::getCartesianProduct($possibleValues)) - 1;
	}

	/**
	 * Returns an 2-dimensional array containing all possible combinations of the provided arrays using the cartesian
	 * product (https://en.wikipedia.org/wiki/Cartesian_product).
	 */
	public static function getCartesianProduct(array $arrays) : array {
		$result = [];
		$count = count($arrays) - 1;
		$combinations = array_product(array_map(static fn(array $array) => count($array), $arrays));
		for($i = 0; $i < $combinations; $i++){
			$result[] = array_map(static fn(array $array) => current($array), $arrays);
			for($j = $count; $j >= 0; $j--){
				if(next($arrays[$j])) {
					break;
				}
				reset($arrays[$j]);
			}
		}
		return $result;
	}
}
