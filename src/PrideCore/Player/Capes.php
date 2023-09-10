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
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat as TF;
use PrideCore\Core;
use PrideCore\Utils\Database;
use PrideCore\Utils\Rank;
use PrideCore\Utils\Utils;
use function basename;
use function explode;
use function implode;
use function scandir;
use function str_replace;

/**
 * Capes related cosmetics...
 */
class Capes {
	use SingletonTrait;

	public function __construct()
	{
		self::setInstance($this);
	}

	public ?array $capes = null;

	public function getAllCapes() : array {

		if($this->capes !== null) return $this->capes;

		foreach(scandir(Core::getInstance()->getDataFolder() . "capes/") as $cape){
			$this->capes[] = basename($cape);
		}

		return $this->capes;
	}

	public function getActiveCape(Player $player) : string{
		if($player->getCurrentCape() === null) return "None";

		return $this->capes[$player->getCurrentCape()];
	}

	public static function getOwnedCape(Player $player) : ?array {
		if($player->getOwnedCape() === null) return null;

		$tags = explode(",", $player->getOwnedCape());

		return $tags;
	}

	public static function addCape(Player $player, string $cape_name) : void {
		$tags = explode(",", $player->getOwnedCape());

		$tags[] = [$tag_id => ""];
		$result = implode(",", $tags);
		$player->setOwnedCape($result);
		Database::getInstance()->getDatabase()->executeGeneric("setCapeOwned", ["uuid" => $player->getUniqueId()->__toString(), "cape_owned" => $result], null, fn (SqlError $err) => Server::getInstance()->getLogger()->error(Core::PREFIX . Core::ARROW . $err->getMessage()));
	}

	public static function removeCape(Player $player, string $cape_name) : void{
		$capes = explode(",", $player->getOwnedCape());

		unset($capes[$cape_name]);
		$result = implode(",", $capes);
		$player->setOwnedCape($result);
		Database::getInstance()->getDatabase()->executeGeneric("setCapeOwned", ["uuid" => $player->getUniqueId()->__toString(), "cape_owned" => $result], null, fn (SqlError $err) => Server::getInstance()->getLogger()->error(Core::PREFIX . Core::ARROW . $err->getMessage()));
	}

	public function QueryCape(string $uuid, Closure $resolve) : void{
		Database::getInstance()->getDatabase()->executeSelect("getCapeOwned", ["uuid" => Utils::removeDashes($uuid)], function (array $rows) use ($resolve) {
			$owned = $rows[0]["cape_owned"] ?? "";
			$resolve($owned);
		}, fn (SqlError $err) => Server::getInstance()->getLogger()->error(Core::PREFIX . Core::ARROW . $err->getMessage()));
	}

	/**
	 * @param mixed $player
	 */
	public function updatePlayerCape($player) : void {
		$this->QueryCape($player->getUniqueId()->__toString(), function (string $owned) use ($player) {
			$player->setOwnedCape($owned);
		});
	}

	public function toPrettyPrint(string $text) : string {
		$text = str_replace("_", " ", $text);
		$text = str_replace(".png", "", $text);

		return $text;
	}

	private function query(string $uuid, Closure $resolve) : void{
		Database::getInstance()->getDatabase()->executeSelect("getCape", ["uuid" => $uuid], function (array $rows) use ($uuid, $resolve) {
			$tag = $rows[0]["cape_name"] ?? Tags::NONE;
			$resolve($tag);
		}, fn (SqlError $err) => Server::getInstance()->getLogger()->error(Core::PREFIX . Core::ARROW . $err->getMessage()));
	}

	public function setTag(Player $player, int $cape_name) : void{
		Database::getInstance()->getDatabase()->executeGeneric("setCape", ["uuid" => $player->getUniqueId()->__toString(), "cape_name" => $tag_id], null, fn (SqlError $err) => Server::getInstance()->getLogger()->error(Core::PREFIX . Core::ARROW . $err->getMessage()));
		$player->setCape($cape_name);
	}

	public static function checkIfHasCape(Player $player) : void {
		if($player->getPlayerInfo()->getSkin()->getCapeData() !== ""){
			if(!$player->getServer()->isOp($player->getName()) || $player->getRankId() === Rank::PLAYER){
				$player->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "Your custom cape has been disabled by our network. To use custom capes, purchase " . Rank::getInstance()->displayTag(Rank::PLUS) . TF::RED . " rank, " . Rank::getInstance()->displayTag(Rank::VIP) . TF::RED . " or " . Rank::getInstance()->displayTag(Rank::MVP) . TF::RED . " rank to continue using custom capes, or you can customize your server unlockable capes at your locker.");
				$player->removeCape();
			}
		}
	}

	public static function syncAccount(Player $player) : void {
		Capes::getInstance()->updatePlayerCape($player);
	}
}
