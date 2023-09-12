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

namespace customiesdevs\customies\item\component;

final class BlockPlacerComponent implements ItemComponent {

	private string $blockIdentifier;
	private bool $useBlockDescription;

	public function __construct(string $blockIdentifier, bool $useBlockDescription = false) {
		$this->blockIdentifier = $blockIdentifier;
		$this->useBlockDescription = $useBlockDescription;
	}

	public function getName() : string {
		return "minecraft:block_placer";
	}

	public function getValue() : array {
		return [
			"block" => $this->blockIdentifier,
			"use_block_description" => $this->useBlockDescription
		];
	}

	public function isProperty() : bool {
		return false;
	}
}
