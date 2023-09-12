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

use pocketmine\data\bedrock\block\convert\BlockStateReader;
use pocketmine\data\bedrock\block\convert\BlockStateWriter;

interface Permutable {

	/**
	 * Returns an array of the different block properties the block has. When the block is registered, it is registered
	 * with all the possible combinations of all the block properties returned.
	 * @return BlockProperty[]
	 */
	public function getBlockProperties() : array;

	/**
	 * Returns an array of the permutations the block has. They contain molang queries that can use the block properties
	 * to control the components based on different states server-side.
	 * @return Permutation[]
	 */
	public function getPermutations() : array;

	/**
	 * Returns an array of the current block property values in the same order as those in getBlockProperties(). It is
	 * used to convert the current properties in to a meta value that can be stored on disk in the world.
	 * @return mixed[]
	 */
	public function getCurrentBlockProperties() : array;

	/**
	 * Serializes the block state to the given BlockStateWriter.
	 */
	public function serializeState(BlockStateWriter $blockStateOut) : void;

	/**
	 * Deserializes the block state from the given BlockStateReader.
	 */
	public function deserializeState(BlockStateReader $blockStateIn) : void;
}
