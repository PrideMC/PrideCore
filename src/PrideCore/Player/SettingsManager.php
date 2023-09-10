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

namespace PrideCore\Player;

use Closure;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use PrideCore\Core;

use PrideCore\Utils\Database;
use function is_bool;
use function is_int;
use function is_string;

/**
 * Player personalize settings.
 */
class SettingsManager {

	use SingletonTrait;

	public function __construct(){
		self::setInstance($this);
	}

	private function QueryVisibility(string $uuid, Closure $resolve) : void{
		Database::getInstance()->getDatabase()->executeSelect("getPlayerVisibility", ["uuid" => $uuid], function (array $rows) use ($uuid, $resolve) {
			$visible = $rows[0]["players_visible"] ?? false;
			$resolve($visible);
		}, fn (SqlError $err) => Server::getInstance()->getLogger()->error(Core::PREFIX . Core::ARROW . $err->getMessage()));
	}

	private function QueryAlwaysSprinting(string $uuid, Closure $resolve) : void{
		Database::getInstance()->getDatabase()->executeSelect("getAlwaysSprinting", ["uuid" => $uuid], function (array $rows) use ($uuid, $resolve) {
			$sprint = $rows[0]["always_sprinting"] ?? false;
			$resolve($sprint);
		}, fn (SqlError $err) => Server::getInstance()->getLogger()->error(Core::PREFIX . Core::ARROW . $err->getMessage()));
	}

	public function setVisiblityToPlayers(Player $player, bool $confirm = true) : void{
		if($confirm){
			foreach($player->getWorld()->getPlayers() as $p){
				$player->showPlayer($p);
			}
		} else {
			foreach($player->getWorld()->getPlayers() as $p){
				$player->hidePlayer($p);
			}
		}
		Database::getInstance()->getDatabase()->executeGeneric("setPlayerVisibility", ["uuid" => $player->getUniqueId()->__toString(), "confirm" => $confirm], null, fn (SqlError $err) => Server::getInstance()->getLogger()->error(Core::PREFIX . Core::ARROW . $err->getMessage()));
		$this->updateVisiblityToPlayers($player);
	}

	public function updateVisiblityToPlayers(Player $player) : bool{
		$this->QueryVisibility($player->getUniqueId()->__toString(), function ($confirm) use ($player) {
			$player->setVisibleAllPlayers($this->convertToBoolean($confirm));
		});

		return $player->isVisibleAllPlayers();
	}

	public function updateAlwaysSprinting(Player $player) : bool{
		$this->QueryAlwaysSprinting($player->getUniqueId()->__toString(), function ($confirm) use ($player) {
			$player->setAlwaysSprinting($this->convertToBoolean($confirm));
		});

		return $player->isAlwaysSprinting();
	}

	public function setAlwaysSprinting(Player $player, bool $confirm = true) : void{
		Database::getInstance()->getDatabase()->executeGeneric("setAlwaysSprinting", ["uuid" => $player->getUniqueId()->__toString(), "confirm" => $confirm], null, fn (SqlError $err) => Server::getInstance()->getLogger()->error(Core::PREFIX . Core::ARROW . $err->getMessage()));
		$this->updateAlwaysSprinting($player);
	}

	/**
	 * @return [type]
	 */
	public function init(Player $player) {
		$this->updateAlwaysSprinting($player);
		$this->updateVisiblityToPlayers($player);
	}

	/**
	 * Why libasynql needs to convert into boolean?
	 * @internal
	 */
	private function convertToBoolean(mixed $bool) : bool{
		// check if it is a natural bool
		if(is_bool($bool)){
			return $bool;
		}

		// check if it is a string
		if(is_string($bool)){
			switch($bool){
				case "true":
				case "yes":
				case "y":
				case "1":
					return true;
					break;
				case "no":
				case "n":
				case "false":
				case "0":
					return false;
					break;
			}
		}

		// check if it is a int
		if(is_int($bool)){
			switch($bool){
				case 0:
					return false;
					break;
				case 1:
					return true;
					break;
			}
		}
	}
}
