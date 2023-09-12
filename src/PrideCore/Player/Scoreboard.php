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

namespace PrideCore\Player;

use jackmd\scorefactory\ScoreFactory;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat as T;

use function str_replace;

/**
 * Scoreboard related class..
 */
class Scoreboard
{
	use SingletonTrait;

	public const DISPLAY_NAME = "pride.sb.logo";

	public const DEFAULT = 0;

	public function setScoreboard(Player $player, int $scoreboard) : void
	{
		switch ($scoreboard) {
			case Scoreboard::DEFAULT:
				ScoreFactory::setObjective(
					$player, // the player
					Scoreboard::DISPLAY_NAME, // display logo
					SetDisplayObjectivePacket::SORT_ORDER_ASCENDING, // ascending order
					"sidebar", // sidebar
					$player->getName(), // player name
					"dummy", // dummy
				);
				ScoreFactory::sendObjective($player);
				ScoreFactory::setScoreLine($player, 0, $this->format($player, T::RED . "")); // 15
				ScoreFactory::setScoreLine($player, 1, T::RESET . T::RESET);
				ScoreFactory::setScoreLine($player, 2, $this->format($player, T::GRAY . " Your Name: " . T::AQUA . "{player_name}"));
				ScoreFactory::setScoreLine($player, 3, T::RESET . T::RESET . T::RESET);
				ScoreFactory::setScoreLine($player, 4, $this->format($player, T::GRAY . " Your Ping: {player_ping}"));
				ScoreFactory::setScoreLine($player, 5, T::RESET);
				ScoreFactory::setScoreLine($player, 6, T::RED . $this->format($player, "" . T::RESET));
				ScoreFactory::setScoreLine($player, 7, T::YELLOW . $this->format($player, "play.mcpridebedrock.tk"));
				ScoreFactory::sendLines($player);
				break;
		}
	}

	public function format(Player $player, string $str) : string // format the variable to a new one string
	{
		$str = str_replace("{player_name}", $player->getName(), $str);
		$str = str_replace("{world}", $player->getWorld()->getDisplayName(), $str);

		/// PING VISUALATOR ///
		if ($player->getNetworkSession()->getPing() < 80) {
			$str = str_replace("{player_ping}", T::GREEN . $player->getNetworkSession()->getPing() . " ms", $str);
		} else {
			if ($player->getNetworkSession()->getPing() < 100) {
				$str = str_replace("{player_ping}", T::YELLOW . $player->getNetworkSession()->getPing() . " ms", $str);
			} else {
				if ($player->getNetworkSession()->getPing() < 150) {
					$str = str_replace("{player_ping}", T::GOLD . $player->getNetworkSession()->getPing() . " ms", $str);
				} else {
					if ($player->getNetworkSession()->getPing() < 200) {
						$str = str_replace("{player_ping}", T::RED . $player->getNetworkSession()->getPing() . " ms", $str);
					}
				}
			}
		}

		return $str;
	}
}
