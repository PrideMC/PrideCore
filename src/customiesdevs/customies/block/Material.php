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

use pocketmine\nbt\tag\CompoundTag;

final class Material {

	public const TARGET_ALL = "*";
	public const TARGET_SIDES = "sides";
	public const TARGET_UP = "up";
	public const TARGET_DOWN = "down";
	public const TARGET_NORTH = "north";
	public const TARGET_EAST = "east";
	public const TARGET_SOUTH = "south";
	public const TARGET_WEST = "west";

	public const RENDER_METHOD_ALPHA_TEST = "alpha_test";
	public const RENDER_METHOD_BLEND = "blend";
	public const RENDER_METHOD_OPAQUE = "opaque";

	public function __construct(
		private readonly string $target,
		private readonly string $texture,
		private readonly string $renderMethod,
		private readonly bool   $faceDimming = true,
		private readonly bool   $ambientOcclusion = true
	) { }

	/**
	 * Returns the targeted face for the material.
	 */
	public function getTarget() : string {
		return $this->target;
	}

	/**
	 * Returns the material in the correct NBT format supported by the client.
	 */
	public function toNBT() : CompoundTag {
		return CompoundTag::create()
			->setString("texture", $this->texture)
			->setString("render_method", $this->renderMethod)
			->setByte("face_dimming", $this->faceDimming ? 1 : 0)
			->setByte("ambient_occlusion", $this->ambientOcclusion ? 1 : 0);
	}
}
