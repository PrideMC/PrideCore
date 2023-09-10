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

namespace PrideCore\Utils;

use Closure;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat as TF;
use PrideCore\Core;
use PrideCore\Player\Player;

/**
 * Server-cached functions, storing data things...
 */
class Cache
{
	use SingletonTrait;

	private int $playerCount = 0;

	private array $warn = [];

	private array $pf = [];

	private array $offense = [];

	public function setPlayerCount(int $count) : void
	{
		$this->playerCount = $count;
	}

	public function getPlayerCount() : int
	{
		return $this->playerCount;
	}

	public function QueryCoins(string $uuid, Closure $resolve) : void
	{
		Database::getInstance()->getDatabase()->executeSelect("getCoins", ["uuid" => Utils::removeDashes($uuid)], function (array $rows) use ($resolve) {
			$coins = $rows[0]["coins"] ?? 0;
			$resolve($coins);
		}, fn (SqlError $err) => Server::getInstance()->getLogger()->error(Core::PREFIX . Core::ARROW . $err->getMessage()));
	}

	public function setPlayerCoins(Player $player) : void
	{
		$this->QueryCoins($player->getUniqueId()->__toString(), function (int $amount) use ($player) {
			$player->setPlayerCoins($amount);
		});
	}

	public function setCoins(Player $player, int $amount) : void
	{
		Database::getInstance()->getDatabase()->executeGeneric("setCoins", ["uuid" => $player->getUniqueId()->__toString(), "rank_id" => $rank], null, fn (SqlError $err) => Server::getInstance()->getLogger()->error(Core::PREFIX . Core::ARROW . $err->getMessage()));
		$this->setPlayerCoins($player);
	}

	public function addWarnCount(string $player) : void
	{
		$this->warn[$player] = isset($this->warn[$player]) ? $this->warn[$player] + 1 : 1;
	}

	public function getWarnCount(string $player) : int
	{
		return $this->warn[$player] ?? 0;
	}

	public function addProfanityCount(Player $player) : void
	{
		$this->pf[$player->getUniqueId()->__toString()] = isset($this->pf[$player->getUniqueId()->__toString()]) ? $this->pf[$player->getUniqueId()->__toString()] + 1 : 1;

		if($this->getProfanityCount($player->getUniqueId()->__toString()) === 5){
			$player->setTempMute(1200);
			$player->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "You're were muted by our system for hate speech/profanity.");
			return;
		}

		if($this->getProfanityCount($player->getUniqueId()->__toString()) === 10){
			$player->setTempMute(6000);
			$player->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "You're were muted by our system for hate speech/profanity.");
			return;
		}

		if($this->getProfanityCount($player->getUniqueId()->__toString()) == 15){
			$target->kick(Core::PREFIX . TF::GRAY . "\nYou have been kicked from our network." . "\n\n" . Core::ARROW . TF::RESET . TF::GRAY . "Reason: " . TF::YELLOW . "Hate Speech/Profanity" . TF::RESET);
			$this->setUserOffensive($player->getUniqueId()->__toString(), true);
			return;
		}

		if($this->isUserOffensive($player->getUniqueId()->__toString())){
			Server::getInstance()->getNameBans()->addBan($player, $reason, Utils::stringToTimestamp("1h")[0], "PrideMC");
			$player->kick(Core::PREFIX . TF::GRAY . "\nYou have been banned from our network." . "\n\n" . Core::ARROW . TF::RESET . TF::GRAY . " Reason: " . TF::YELLOW . "Profanity" . TF::RESET . "\n" . Core::ARROW . TF::RESET . TF::GRAY . "Expires: " . TF::YELLOW . Utils::stringToTimestamp("1hd")[0]->format("Y-m-d H:i:s"));
			$this->resetProfanityCount();
			return;
		}
	}

	public function getProfanityCount(string $player) : int
	{
		return $this->pf[$player] ?? 0;
	}

	public function resetProfanityCount(string $player) : void
	{
		$this->pf[$player->getUniqueId()->__toString()] = isset($this->pf[$player->getUniqueId()->__toString()]) ? 0 : 0; // reset
	}

	public function setUserOffensive(string $id, bool $value) : void {
		$this->offense[$id] = $value;
	}

	public function isUserOffensive(string $id) : bool {
		return $this->isUserOffensive[$id] ?? false;
	}
}
