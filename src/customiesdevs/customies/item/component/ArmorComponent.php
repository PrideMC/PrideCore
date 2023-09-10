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

namespace customiesdevs\customies\item\component;

final class ArmorComponent implements ItemComponent {

	public const TEXTURE_TYPE_CHAIN = "chain";
	public const TEXTURE_TYPE_DIAMOND = "diamond";
	public const TEXTURE_TYPE_ELYTRA = "elytra";
	public const TEXTURE_TYPE_GOLD = "gold";
	public const TEXTURE_TYPE_IRON = "iron";
	public const TEXTURE_TYPE_LEATHER = "leather";
	public const TEXTURE_TYPE_NETHERITE = "netherite";
	public const TEXTURE_TYPE_NONE = "none";
	public const TEXTURE_TYPE_TURTLE = "turtle";

	private int $protection;
	private string $textureType;

	public function __construct(int $protection, string $textureType = self::TEXTURE_TYPE_NONE) {
		$this->protection = $protection;
		$this->textureType = $textureType;
	}

	public function getName() : string {
		return "minecraft:armor";
	}

	public function getValue() : array {
		return [
			"protection" => $this->protection,
			"texture_type" => $this->textureType
		];
	}

	public function isProperty() : bool {
		return false;
	}
}
