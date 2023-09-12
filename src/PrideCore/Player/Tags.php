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

use Closure;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat as TF;
use poggit\libasynql\SqlError;
use PrideCore\Core;
use PrideCore\Utils\Database;
use PrideCore\Utils\Utils;
use function explode;
use function implode;

/**
 * Tags related...
 */
class Tags
{
	use SingletonTrait;
	public const NONE = -1;

	// Rank Freebie Tags (10)
	public const ROCK_N_ROLL = 0;
	public const WTF = 1;
	public const MAYBE = 2;
	public const LMAO = 3;
	public const COOL = 4;
	public const WONDERFUL = 5;
	public const RAINBOW = 6;
	public const HACK_N_LOOSE = 7;
	public const BRUH = 8;
	public const HUH = 9;
	public const OH_NO = 10;

	// Special/Event Tags (10)
	public const HAPPY_PRIDEDAY = 11;
	public const HEART = 12;
	public const CHRISTMAS_2022 = 13;
	public const HALLOWEEN_2022 = 14;
	public const NEW_YEAR_2023 = 15;
	public const SEASON_1 = 16;
	public const SEASON_2 = 17;
	public const SEASON_3 = 18;
	public const SEASON_4 = 19;
	public const SEASON_5 = 20;

	// Common Tags (20)
	public const THE_GRASSHOPER = 21;
	public const HARDCORE_PARKOUR = 22;
	public const THE_FROG = 23;
	public const DORA_THE_EXPLORER = 24;
	public const THE_HUNTER = 25;
	public const PIGMEN = 26;
	public const W = 27;
	public const FRRRR = 28;
	public const OH_SNAP = 29;
	public const EH = 30;
	public const SNIFFY_GOOF = 31;
	public const FLY_HIGH = 32;
	public const NICE = 33;
	public const THE_SUPPORTER = 34;
	public const THE_MVP = 35;
	public const IM_THE_CAPTAIN = 36;
	public const GG = 37;
	public const GOODJOB = 38;
	public const GOOFYAHH = 39;
	public const NOTHING_HERE = 40;

	// Rare Tags (10)
	public const AKA_ROCK = 41;
	public const STOP_THE_CAP = 42;
	public const LOL = 43;
	public const MASSIVE_GAMING_CHAIR = 44;
	public const BEST_GAMER = 45;
	public const HACKER = 46;
	public const HACKUSATED = 47;
	public const YOLO = 48;
	public const UNO = 49;
	public const BALD = 50;

	// In-purchase Tags (~)

	// Tags Format
	public static array $tags = [
		Tags::NONE => "",
		Tags::ROCK_N_ROLL => TF::RED . "RoCk AnD RoLl" . TF::RESET,
		Tags::WTF => TF::LIGHT_PURPLE . "WTF?!" . TF::RESET,
		Tags::MAYBE => TF::WHITE . "Maybe..." . TF::RESET,
		Tags::LMAO => TF::WHITE . "Lmao" . TF::RESET,
		Tags::COOL => TF::YELLOW . "Cool" . TF::RESET,
		Tags::WONDERFUL => TF::AQUA . "Wonderful Day!" . TF::RESET,
		Tags::RAINBOW => "§fR§aa§bi§cn§db§eo§gw §f<3" . TF::RESET,
		Tags::HACK_N_LOOSE => TF::DARK_RED . "Hack and Lose" . TF::RESET,
		Tags::BRUH => TF::BLUE . "Bruh." . TF::RESET,
		Tags::HUH => TF::DARK_GRAY . "Huh!?" . TF::GRAY . TF::RESET,
		Tags::OH_NO => TF::AQUA . "oh no :<" . TF::RESET,
		Tags::HAPPY_PRIDEDAY => TF::GOLD . "Happy PrideDay!" . TF::RESET,
		Tags::HEART => TF::RED . "<3" . TF::RESET,
		Tags::CHRISTMAS_2022 => TF::AQUA . "Winter" . TF::WHITE . "fest " . TF::RED . "2k22" . TF::RESET,
		Tags::HALLOWEEN_2022 => TF::GOLD . "Spooky" . TF::YELLOW . "fest " . TF::DARK_RED . "2k22" . TF::RESET,
		Tags::NEW_YEAR_2023 => TF::AQUA . "Another Year 2k23" . TF::RESET,
		Tags::SEASON_1 => TF::GOLD . "The " . TF::RED . "pIraTe." . TF::RESET,
		Tags::SEASON_2 => TF::GREEN . "Springiffy" . TF::RESET,
		Tags::SEASON_3 => TF::BLUE . "GenZ" . TF::RESET,
		Tags::SEASON_4 => TF::AQUA . "Weed" . TF::RESET,
		Tags::SEASON_5 => TF::RED . "E" . TF::RESET,
		Tags::THE_GRASSHOPER => TF::GREEN . "The Grasshoper" . TF::RESET,
		Tags::HARDCORE_PARKOUR => TF::RED . "HARDCORE " . TF::GOLD . "PARKOUR!" . TF::RESET,
		Tags::THE_FROG => TF::DARK_GREEN . "The Frog" . TF::RESET,
		Tags::DORA_THE_EXPLORER => TF::LIGHT_PURPLE . "Dora the Explorer" . TF::RESET,
		Tags::THE_HUNTER => TF::RED . "The " . TF::DARK_RED . "Hunter" . TF::RESET,
		Tags::PIGMEN => TF::GOLD . "Im the " . TF::RED . "pigmen." . TF::RESET,
		Tags::W => TF::YELLOW . "W" . TF::RESET,
		Tags::FRRRR => TF::DARK_GRAY . "Frrrrrr." . TF::RESET,
		Tags::OH_SNAP => TF::MINECOIN_GOLD . "Oh Snappp :<" . TF::RESET,
		Tags::EH => TF::BLUE . "Eh." . TF::RESET,
		Tags::SNIFFY_GOOF => TF::AQUA . "Sniffy" . TF::DARK_AQUA . "goof" . TF::RESET,
		Tags::FLY_HIGH => TF::AQUA . "flyhigh :3" . TF::RESET,
		Tags::NICE => TF::WHITE . "nice." . TF::RESET,
		Tags::THE_SUPPORTER => TF::LIGHT_PURPLE . "The Supporter" . TF::RESET,
		Tags::THE_MVP => TF::GOLD . "The " . TF::AQUA . "MVP" . TF::RESET,
		Tags::IM_THE_CAPTAIN => TF::YELLOW . "Im the " . TF::AQUA . "Captain!" . TF::RESET,
		Tags::GG => TF::GREEN . "GG!" . TF::RESET,
		Tags::GOODJOB => TF::WHITE . "goodjob :D" . TF::RESET,
		Tags::GOOFYAHH => TF::GREEN . "goofyaah." . TF::RESET,
		Tags::NOTHING_HERE => TF::DARK_PURPLE . "nothing here :P" . TF::RESET,
		Tags::AKA_ROCK => TF::MINECOIN_GOLD . "AKA Rock ;)" . TF::RESET,
		Tags::STOP_THE_CAP => TF::RED . "Stop the cap." . TF::RESET,
		Tags::LOL => TF::WHITE . "lol." . TF::RESET,
		Tags::MASSIVE_GAMING_CHAIR => TF::RED . "massive gamin chair." . TF::RESET,
		Tags::BEST_GAMER => TF::GREEN . "bEsT gAmEr UwU" . TF::RESET,
		Tags::HACKER => TF::GREEN . "hAckER" . TF::RESET,
		Tags::HACKUSATED => TF::RED . "hackusated." . TF::RESET,
		Tags::YOLO => TF::YELLOW . "YOLO!" . TF::RESET,
		Tags::UNO => TF::RED . "UNo!" . TF::RESET,
		Tags::BALD => TF::WHITE . "bald." . TF::RESET,
	];

	public function __construct()
	{
		self::setInstance($this);
	}

	private function query(string $uuid, Closure $resolve) : void{
		Database::getInstance()->getDatabase()->executeSelect("getTag", ["uuid" => $uuid], function (array $rows) use ($uuid, $resolve) {
			$tag = $rows[0]["tag_id"] ?? Tags::NONE;
			$resolve($tag);
		}, fn (SqlError $err) => Server::getInstance()->getLogger()->error(Core::PREFIX . Core::ARROW . $err->getMessage()));
	}

	public function setTag(Player $player, int $tag_id) : void{
		Database::getInstance()->getDatabase()->executeGeneric("setTag", ["uuid" => $player->getUniqueId()->__toString(), "tag_id" => $tag_id], null, fn (SqlError $err) => Server::getInstance()->getLogger()->error(Core::PREFIX . Core::ARROW . $err->getMessage()));
		$player->setTag($tag_id);
	}

	public function display(Player $player) : void{
		$this->query($player->getUniqueId()->__toString(), function($tag_id) use($player){
			$player->setTag(($tag_id ?? Tags::NONE));
			$player->setScoretag($this->displayName(($tag_id ?? Tags::NONE)));
		});
	}

	public function updateTag(Player $player) : void{
		$this->updatePlayerTags($player); // update database
		if($player->getTag() === Tags::NONE) return;
		$this->display($player);
	}

	public function getActiveTag(Player $player) : string{
		if($player->getTag() === Tags::NONE) return "None";

		return $this->displayName($player->getTag());
	}

	public function displayName(int $tag_id) : ?string{
		return (Tags::$tags[$tag_id] ?? null);
	}

	public static function getOwnedTags(Player $player) : ?array {
		if($player->getOwnedTags() === null) return null;

		$tags = explode(",", $player->getOwnedTags());

		return $tags;
	}

	public static function addTag(Player $player, int $tag_id) : void {
		$tags = explode(",", $player->getOwnedTags());

		$tags[] = [$tag_id => ""];
		$result = implode(",", $tags);
		$player->setOwnedTags($result);
		Database::getInstance()->getDatabase()->executeGeneric("setTagsOwned", ["uuid" => $player->getUniqueId()->__toString(), "tags_owned" => $result], null, fn (SqlError $err) => Server::getInstance()->getLogger()->error(Core::PREFIX . Core::ARROW . $err->getMessage()));
	}

	public static function removeTag(Player $player, int $tag_id) : void{
		$tags = explode(",", $player->getOwnedTags());

		unset($tags[$tag_id]);
		$result = implode(",", $tags);
		$player->setOwnedTags($result);
		Database::getInstance()->getDatabase()->executeGeneric("setTagsOwned", ["uuid" => $player->getUniqueId()->__toString(), "tags_owned" => $result], null, fn (SqlError $err) => Server::getInstance()->getLogger()->error(Core::PREFIX . Core::ARROW . $err->getMessage()));
	}

	public function QueryTags(string $uuid, Closure $resolve) : void {
		Database::getInstance()->getDatabase()->executeSelect("getTagsOwned", ["uuid" => Utils::removeDashes($uuid)], function (array $rows) use ($resolve) {
			$owned = $rows[0]["tags_owned"] ?? "";
			$resolve($owned);
		}, fn (SqlError $err) => Server::getInstance()->getLogger()->error(Core::PREFIX . Core::ARROW . $err->getMessage()));
	}

	/**
	 * @param mixed $player
	 */
	public function updatePlayerTags($player) : void {
		$this->QueryTags($player->getUniqueId()->__toString(), function (string $owned) use ($player) {
			$player->setOwnedTags($owned);
		});
	}
}
