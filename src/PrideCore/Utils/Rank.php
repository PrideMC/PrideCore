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

namespace PrideCore\Utils;

use Closure;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat as TF;
use PrideCore\Commands\Permissions;
use PrideCore\Player\Player;
use function strtolower;

class Rank
{
	use SingletonTrait;

	public const PLAYER = 0;
	public const BOOSTER = 1;
	public const VOTER = 2;
	public const DISCORD = 3;
	public const MEDIA = 4;
	public const MVP = 5;
	public const VIP = 6;
	public const PLUS = 7;
	public const PRIDE = 8;
	public const TRIAL = 9;
	public const HELPER = 10;
	public const BUILDER = 11;
	public const MODERATOR = 12;
	public const STAFF = 13;
	public const TEAM = 14;
	public const ADMIN = 15;
	public const OWNER = 16;

	private array $players = [];

	public function setRank(Player $player, int $rank) : void
	{
		Database::getInstance()->getDatabase()->executeGeneric("setRank", ["uuid" => $player->getUniqueId()->__toString(), "rank_id" => $rank], null, fn (SqlError $err) => Server::getInstance()->getLogger()->error(Core::PREFIX . Core::ARROW . $err->getMessage()));
		$player->setRankId($rank);
		$this->updatePermissions($player);
	}

	private function query(string $uuid, Closure $resolve) : void
	{
		Database::getInstance()->getDatabase()->executeSelect("getRank", ["uuid" => $uuid], function (array $rows) use ($uuid, $resolve) {
			$rank = $rows[0]["rank_id"] ?? Rank::PLAYER;
			$resolve($rank);
		}, fn (SqlError $err) => Server::getInstance()->getLogger()->error(Core::PREFIX . Core::ARROW . $err->getMessage()));
	}

	public function displayName(Player $player) : void
	{
		$this->query($player->getUniqueId()->__toString(), function (?int $rank) use ($player) {
			$player->setRankId(($rank ?? Rank::PLAYER));
			switch($rank){
				case Rank::PLAYER:
					$player->setNametag(TF::GRAY . $player->getName() . TF::RESET);
					break;
				case Rank::BOOSTER:
					$player->setNametag(TF::LIGHT_PURPLE . $player->getName() . TF::RESET . " " . TF::DARK_GRAY . "[" . TF::LIGHT_PURPLE . "Booster" . TF::DARK_GRAY . "]");
					break;
				case Rank::VOTER:
					$player->setNametag(TF::GOLD . $player->getName() . TF::RESET . " " . TF::DARK_GRAY . "[" . TF::LIGHT_PURPLE . "Voter" . TF::DARK_GRAY . "]");
					break;
				case Rank::DISCORD:
					$player->setNametag(TF::BLUE . $player->getName() . TF::RESET . " " . TF::DARK_GRAY . "[" . TF::BLUE . "Discord" . TF::DARK_GRAY . "]");
					break;
				case Rank::MEDIA:
					$player->setNametag(TF::RED . $player->getName() . TF::RESET . " " . TF::DARK_GRAY . "[" . TF::RED . "Media" . TF::DARK_GRAY . "]");
					break;
				case Rank::MVP:
					$player->setNametag(TF::DARK_RED . $player->getName() . TF::RESET . " " . TF::DARK_GRAY . "[" . TF::DARK_RED . TF::BOLD . "MVP" . TF::DARK_GRAY . "]");
					break;
				case Rank::VIP:
					$player->setNametag(TF::MINECOIN_GOLD . $player->getName() . TF::RESET . " " . TF::DARK_GRAY . "[" . TF::MINECOIN_GOLD . TF::BOLD . "VIP" . TF::DARK_GRAY . "]");
					break;
				case Rank::PLUS:
					$player->setNametag(TF::GREEN . $player->getName() . TF::RESET . " " . TF::DARK_GRAY . "[" . TF::GREEN . TF::BOLD . "+" . TF::DARK_GRAY . "]");
					break;
				case Rank::PRIDE:
					$player->setNametag(TF::GREEN . $player->getName() . TF::RESET . " " . TF::DARK_GRAY . "[" . TF::RED . TF::BOLD . "P" . TF::BLUE . "R" . TF::GREEN . "I" . TF::YELLOW . "D" . TF::AQUA . "E" . TF::RESET . TF::DARK_GRAY . "]");
					break;
				case Rank::TRIAL:
					$player->setNametag(TF::DARK_AQUA . $player->getName() . TF::RESET . " " . TF::DARK_GRAY . "[" . TF::DARK_AQUA . TF::BOLD . "TRIAL" . TF::DARK_GRAY . "]");
					break;
				case Rank::HELPER:
					$player->setNametag(TF::GREEN . $player->getName() . TF::RESET . " " . TF::DARK_GRAY . "[" . TF::GREEN . "Helper" . TF::DARK_GRAY . "]");
					break;
				case Rank::BUILDER:
					$player->setNametag(TF::DARK_AQUA . $player->getName() . TF::RESET . " " . TF::DARK_GRAY . "[" . TF::GREEN . "Builder" . TF::DARK_GRAY . "]");
					break;
				case Rank::MODERATOR:
					$player->setNametag(TF::AQUA . $player->getName() . TF::RESET . " " . TF::DARK_GRAY . "[" . TF::AQUA . "Moderator" . TF::DARK_GRAY . "]");
					break;
				case Rank::STAFF:
					$player->setNametag(TF::GREEN . $player->getName() . TF::RESET . " " . TF::DARK_GRAY . "[" . TF::GREEN . "Staff" . TF::DARK_GRAY . "]");
					break;
				case Rank::TEAM:
					$player->setNametag(TF::YELLOW . $player->getName() . TF::RESET . " " . TF::DARK_GRAY . "[" . TF::GREEN . "Team" . TF::DARK_GRAY . "]");
					break;
				case Rank::ADMIN:
					$player->setNametag(TF::RED . $player->getName() . TF::RESET . " " . TF::DARK_GRAY . "[" . TF::RED . "Admin" . TF::DARK_GRAY . "]");
					break;
				case Rank::OWNER:
					$player->setNametag(TF::DARK_RED . $player->getName() . TF::RESET . " " . TF::DARK_GRAY . "[" . TF::RED . "Owner" . TF::DARK_GRAY . "]");
					break;
			}
		});
	}

	public function displayTag(int $rank) : string
	{
		switch($rank) {
			case Rank::PLAYER:
				return TF::GRAY . "Player" . TF::RESET;
				break;
			case Rank::BOOSTER:
				return TF::LIGHT_PURPLE . "Booster" . TF::RESET;
				break;
			case Rank::VOTER:
				return TF::GOLD . "Voter" . TF::RESET;
				break;
			case Rank::DISCORD:
				return TF::BLUE . "Discord" . TF::RESET;
				break;
			case Rank::MEDIA:
				return TF::RED . "Media" . TF::RESET;
				break;
			case Rank::MVP:
				return TF::RED . TF::BOLD . "MVP" . TF::RESET;
				break;
			case Rank::VIP:
				return TF::MINECOIN_GOLD . TF::BOLD . "VIP" . TF::RESET;
				break;
			case Rank::PLUS:
				return TF::GREEN . TF::BOLD . "+" . TF::RESET;
				break;
			case Rank::PRIDE:
				return TF::RED . TF::BOLD . "P" . TF::BLUE . "R" . TF::GREEN . "I" . TF::YELLOW . "D" . TF::AQUA . "E" . TF::RESET;
				break;
			case Rank::TRIAL:
				return TF::DARK_AQUA . TF::BOLD . "TRIAL" . TF::RESET;
				break;
			case Rank::HELPER:
				return TF::GREEN . "Helper" . TF::RESET;
				break;
			case Rank::BUILDER:
				return TF::DARK_AQUA . "Builder" . TF::RESET;
				break;
			case Rank::MODERATOR:
				return TF::AQUA . "Moderator" . TF::RESET;
				break;
			case Rank::STAFF:
				return TF::GREEN . "Staff" . TF::RESET;
				break;
			case Rank::TEAM:
				return TF::YELLOW . "Team" . TF::RESET;
				break;
			case Rank::ADMIN:
				return TF::RED . "Admin" . TF::RESET;
				break;
			case Rank::OWNER:
				return TF::RED . "Own" . TF::DARK_RED . "er" . TF::RESET;
				break;
		}
	}

	public function toUnicode(int $rank) : string
	{
		switch($rank) {
			case Rank::PLAYER:
				return "";
				break;
			case Rank::BOOSTER:
				return "";
				break;
			case Rank::VOTER:
				return "";
				break;
			case Rank::DISCORD:
				return "";
				break;
			case Rank::MEDIA:
				return "";
				break;
			case Rank::MVP:
				return "";
				break;
			case Rank::VIP:
				return "";
				break;
			case Rank::PLUS:
				return "";
				break;
			case Rank::PRIDE:
				return "";
				break;
			case Rank::TRIAL:
				return "";
				break;
			case Rank::HELPER:
				return "";
				break;
			case Rank::BUILDER:
				return "";
				break;
			case Rank::MODERATOR:
				return "";
				break;
			case Rank::STAFF:
				return "";
				break;
			case Rank::TEAM:
				return "";
				break;
			case Rank::ADMIN:
				return "";
				break;
			case Rank::OWNER:
				return "";
				break;
		}
	}

	public function idConverter(int|string $rank) : int|bool
	{
		switch(strtolower($rank)) {
			case "player":
			case Rank::PLAYER:
				return Rank::PLAYER;
				break;
			case "booster":
			case Rank::BOOSTER:
				return Rank::BOOSTER;
				break;
			case "voter":
			case Rank::VOTER:
				return Rank::VOTER;
				break;
			case "discord":
			case Rank::DISCORD:
				return Rank::DISCORD;
				break;
			case "media":
			case Rank::MEDIA:
				return Rank::MEDIA;
				break;
			case "mvp":
			case Rank::MVP:
				return Rank::MVP;
				break;
			case "vip":
			case Rank::VIP:
				return Rank::VIP;
				break;
			case "plus":
			case Rank::PLUS:
				return Rank::PLUS;
				break;
			case "pride":
			case Rank::PRIDE:
				return Rank::PRIDE;
				break;
			case "trial":
			case Rank::TRIAL:
				return Rank::TRIAL;
				break;
			case "helper":
			case Rank::HELPER:
				return Rank::HELPER;
				break;
			case "builder":
			case Rank::BUILDER:
				return Rank::BUILDER;
				break;
			case "moderator":
			case "mod":
			case Rank::MODERATOR:
				return Rank::MODERATOR;
				break;
			case "staff":
			case Rank::STAFF:
				return Rank::STAFF;
				break;
			case "team":
			case "employee":
			case Rank::TEAM:
				return Rank::TEAM;
				break;
			case "administrator":
			case "admin":
			case Rank::ADMIN:
				return Rank::ADMIN;
				break;
			case "ceo":
			case "owner":
			case "founder":
			case "co-owner":
			case "co-founder":
			case Rank::OWNER:
				return Rank::OWNER;
				break;
			default:
				return false;
				break;
		}
	}

	public function syncRanks(Player $player) : void{
		$this->query($player->getUniqueId()->__toString(), function (?int $rank) use ($player) {
			$player->setRankId(($rank ?? Rank::PLAYER));
		});
	}

	public static function updatePermissions(Player $player) : void {
		$permissions = [];
		switch($player->getRankId()){
			case Rank::PLAYER:
				$permissions["pride.command.basic"] = true;
				break;
			case Rank::BOOSTER:
				$permissions["pride.command.basic"] = true;
				$permissions["pride.staff.fly"] = true;
				break;
			case Rank::VOTER:
				$permissions["pride.command.basic"] = true;
				break;
			case Rank::DISCORD:
				$permissions["pride.command.basic"] = true;
				break;
			case Rank::MEDIA:
				$permissions["pride.command.basic"] = true;
				$permissions["pride.media.nick"] = true;
				$permissions["pride.staff.fly"] = true;
				break;
			case Rank::MVP:
				$permissions["pride.command.basic"] = true;
				$permissions["pride.staff.fly"] = true;
				$permissions["pride.bypass.player_count"] = true;
				break;
			case Rank::VIP:
				$permissions["pride.command.basic"] = true;
				$permissions["pride.staff.fly"] = true;
				$permissions["pride.bypass.player_count"] = true;
				$permissions["pride.bypass.vpn"] = true;
				break;
			case Rank::PLUS:
				$permissions["pride.command.basic"] = true;
				$permissions["pride.bypass.player_count"] = true;
				break;
			case Rank::PRIDE:
				$permissions["pride.command.basic"] = true;
				$permissions["pride.staff.fly"] = true;
				$permissions["pride.bypass.player_count"] = true;
				$permissions["pride.bypass.vpn"] = true;
				$permissions["pride.media.nick"] = true;
				break;
			case Rank::TRIAL:
				$permissions["pride.command.basic"] = true;
				$permissions["pride.staff.fly"] = true;
				$permissions["pride.bypass.player_count"] = true;
				$permissions["pride.bypass.vpn"] = true;
				$permissions["pride.media.nick"] = true;
				break;
			case Rank::HELPER:
				$permissions["pride.command.basic"] = true;
				$permissions["pride.staff.fly"] = true;
				$permissions["pride.bypass.player_count"] = true;
				$permissions["pride.bypass.vpn"] = true;
				$permissions["pride.media.nick"] = true;
				$permissions["pride.staff.mute"] = true;
				$permissions["pride.staff.globalmute"] = true;
				$permissions["pride.bypass.globalmute"] = true;
				break;
			case Rank::BUILDER:
				$permissions["pride.command.basic"] = true;
				$permissions["pride.staff.fly"] = true;
				$permissions["pride.bypass.player_count"] = true;
				$permissions["pride.bypass.vpn"] = true;
				$permissions["pride.media.nick"] = true;
				$permissions["pride.builder.build"] = true;
				$permissions["pride.bypass.globalmute"] = true;
				break;
			case Rank::MODERATOR:
				$permissions["pride.command.basic"] = true;
				$permissions["pride.staff.fly"] = true;
				$permissions["pride.bypass.player_count"] = true;
				$permissions["pride.bypass.vpn"] = true;
				$permissions["pride.media.nick"] = true;
				$permissions["pride.staff.mute"] = true;
				$permissions["pride.staff.globalmute"] = true;
				$permissions["pride.staff.ban"] = true;
				$permissions["pride.staff.kick"] = true;
				$permissions["pride.staff.pardon"] = true;
				$permissions["pride.staff.freeze"] = true;
				$permissions["pride.staff.warn"] = true;
				$permissions["pride.bypass.globalmute"] = true;
				break;
			case Rank::STAFF:
			case Rank::TEAM:
			case Rank::ADMIN:
			case Rank::OWNER:
				$permissions["pride.command.basic"] = true;
				$permissions["pride.staff.fly"] = true;
				$permissions["pride.bypass.player_count"] = true;
				$permissions["pride.bypass.vpn"] = true;
				$permissions["pride.media.nick"] = true;
				$permissions["pride.staff.mute"] = true;
				$permissions["pride.staff.globalmute"] = true;
				$permissions["pride.staff.ban"] = true;
				$permissions["pride.staff.kick"] = true;
				$permissions["pride.staff.pardon"] = true;
				$permissions["pride.staff.freeze"] = true;
				$permissions["pride.staff.warn"] = true;
				$permissions["pride.staff.create_redeem_code"] = true;
				$permissions["pride.staff.remove_redeem_code"] = true;
				$permissions["pride.staff.maintenance"] = true;
				$permissions["pride.staff.rank"] = true;
				$permissions["pride.staff.disguise"] = true;
				$permissions["pride.builder.build"] = true;
				$permissions["pride.bypass.globalmute"] = true;
				break;
		}
		Permissions::getInstance()->resetPlayerPermissions();
		Permissions::getInstance()->addPlayerPermissions($player, $permissions);
	}
}
