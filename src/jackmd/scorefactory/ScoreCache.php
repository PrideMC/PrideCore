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

declare(strict_types = 1);

namespace jackmd\scorefactory;

use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\player\Player;

/**
 * @internal
 */
class ScoreCache {

	/** @var ScorePacketEntry[] */
	private array $entries = [];

	private function __construct(
		private Player $player,
		private string $objective,
		private SetDisplayObjectivePacket $objectivePacket
	) {}

	public static function init(Player $player, string $objective, SetDisplayObjectivePacket $objectivePacket) : self {
		return new self($player, $objective, $objectivePacket);
	}

	public function getPlayer() : Player {
		return $this->player;
	}

	public function getObjective() : string {
		return $this->objective;
	}

	public function setObjective(string $objective) : void {
		$this->objective = $objective;
	}

	public function getObjectivePacket() : SetDisplayObjectivePacket {
		return $this->objectivePacket;
	}

	public function setObjectivePacket(SetDisplayObjectivePacket $objectivePacket) : void {
		$this->objectivePacket = $objectivePacket;
	}

	/**
	 * Indexed by (int) line -> ScorePacketEntry
	 *
	 * @return ScorePacketEntry[][]
	 */
	public function getEntries() : array {
		return $this->entries;
	}

	/**
	 * Should be indexed by (int) line -> ScorePacketEntry
	 * No more than 15 entries allowed. #blameMojang
	 *
	 * @param ScorePacketEntry[] $entries
	 */
	public function setEntries(array $entries) : void {
		$this->entries = $entries;
	}

	/**
	 * Index should be in between 1 and 15
	 */
	public function setEntry(int $index, ScorePacketEntry $entry) {
		$this->entries[$index] = $entry;
	}

	public function removeEntry(int $index) {
		unset($this->entries[$index]);
	}

	public function __destruct() {
		unset($this->entries);
	}
}
