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

final class RenderOffsetsComponent implements ItemComponent {

	private int $textureWidth;
	private int $textureHeight;
	private bool $handEquipped;

	public function __construct(int $textureWidth, int $textureHeight, bool $handEquipped = false) {
		$this->textureWidth = $textureWidth;
		$this->textureHeight = $textureHeight;
		$this->handEquipped = $handEquipped;
	}

	public function getName() : string {
		return "minecraft:render_offsets";
	}

	public function getValue() : array {
		$horizontal = ($this->handEquipped ? 0.075 : 0.1) / ($this->textureWidth / 16);
		$vertical = ($this->handEquipped ? 0.125 : 0.1) / ($this->textureHeight / 16);
		$perspectives = [
			"first_person" => [
				"scale" => [$horizontal, $vertical, $horizontal],
			],
			"third_person" => [
				"scale" => [$horizontal, $vertical, $horizontal]
			]
		];
		return [
			"main_hand" => $perspectives,
			"off_hand" => $perspectives
		];
	}

	public function isProperty() : bool {
		return false;
	}
}
