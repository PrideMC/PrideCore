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

namespace PrideCore\Anticheat;

use pocketmine\block\BlockTypeIds;

use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat as TF;
use PrideCore\Anticheat\Modules\AutoClicker;
use PrideCore\Anticheat\Modules\BadPackets;
use PrideCore\Anticheat\Modules\EditionFaker;
use PrideCore\Anticheat\Modules\Flight;
use PrideCore\Anticheat\Modules\Glitch;
use PrideCore\Anticheat\Modules\Instabreak;
use PrideCore\Anticheat\Modules\Killaura;
use PrideCore\Anticheat\Modules\NoClip;
use PrideCore\Anticheat\Modules\NoPacket;
use PrideCore\Anticheat\Modules\Reach;
use PrideCore\Anticheat\Modules\Speed;
use PrideCore\Anticheat\Modules\Timer;
use PrideCore\Core;
use PrideCore\Discord\DiscordWebhook;
use PrideCore\Player\Player;
use PrideCore\Utils\Rank;

use function base64_encode;
use function is_string;
use function microtime;

/**
 * **Guardian Anticheat - PrideMC Network**
 *
 * `Anticheat Development` - *Fast-development mode*
 *
 * TODO:
 * - Improve KillAura
 * - Improve more checks on Reach
 * - Velocity check
 * - Bad packets check
 * - Chest stealer check
 * - Edition Faker (for most advance clients)
 * - Improve player speed check
 *
 * Checks:
 * - Reach (90% done, some false-positive)
 * - Timer (100% done, unchecked)
 * - Bad Packets (100% done, unchecked)
 * - Instabreak or Nuke (100% done, checked & clean)
 * - Flight (50% done, unchecked)
 * - Glitch (100% done, unchecked)
 * - NoClip (100% done, checked & clean)
 * - Killaura (20% done, unchecked)
 * - NoPacket/Blink (100% done, unchecked)
 * - Autoclicker (100% done, unchecked)
 * - Chest stealer (0% done)
 * - Edition Faker (100% done, unchecked)
 * - Speed (90% done)
 * - Velocity (0% done)
 */
abstract class Anticheat {

	public const PREFIX = TF::GRAY . "(" . TF::YELLOW . TF::BOLD . "Guardian" . TF::RESET . TF::GRAY . ")";

	// Errors: Simply do like hive.
	public const BADPACKET_HACK = "Bad packet recieved.";
	public const NOPACKET_HACK = "No packet recieved.";
	public const AUTOCLICKER_HACK = "Too high packet recieved.";
	public const NOCLIP_HACK = "Invalid block actor packet.";
	public const VELOCITY_HACK = "Velocity knockback detected.";
	public const TIMER_HACK = "Bad player ticks recieved.";
	public const REACH_HACK = "Invalid entity distance hit recieved.";
	public const KILLAURA_HACK = "Invalid hit registry recieved.";
	public const GLITCH_HACK = "Invalid packet.";
	public const FLIGHT_HACK = "Unexpected movement location packet recieved.";
	public const INSTABREAK_HACK = "Packet recieved miss-match.";
	public const CHESTSTEALER_HACK = "Unexpected transaction packet recieved.";
	public const EDITIONFAKER_HACK = "Unexpected Game Edition.";
	public const SPEED_HACK = "Invalid movement recieved.";
	public const XTRAY_HACK = "Invalid visual packet recieved.";

	public const REACH = 0;
	public const SPEED = 1;
	public const AUTOCLICKER = 2;
	public const NOCLIP = 3;
	public const VELOCITY = 4;
	public const TIMER = 5;
	public const KILLAURA = 6;
	public const GLITCH = 7;
	public const FLIGHT = 8;
	public const INSTABREAK = 9;
	public const BADPACKET = 10;
	public const NOPACKET = 11;
	public const CHEST_STEALER = 12;
	public const EDITION_FAKER = 13;
	public const XTRAY = 14;

	private int $flag;

	public function __construct(int $flag_id)
	{
		$this->flag = $flag_id;
	}

	public function getFlagId() : int{
		return $this->flag;
	}

	public array $failed = [];
	public array $lastFail = [];

	public function kick(Player $player, string $reason) : void{
		$player->kick(TF::GRAY . "Error: " . base64_encode($reason), $reason);
	}

	public function fail(Player $player){
		if(!isset($this->failed[$player->getUniqueId()->__toString()][$this->flag])) $this->failed[$player->getUniqueId()->__toString()][$this->flag] = 1;
		if($this->failed[$player->getUniqueId()->__toString()][$this->flag] > $this->getMaxViolation()){
			unset($this->failed[$player->getUniqueId()->__toString()][$this->flag]);
			$this->failed[$player->getUniqueId()->__toString()][$this->flag] = 0;
			$this->notifyAdmins($player, false);
			$this->notifyOnDiscord($player, true, "Blocked");
			Core::getInstance()->getServer()->getLogger()->info(Anticheat::PREFIX . " " . Core::ARROW . " " . TF::RED . $player->getName() . " is kicked for suspected using " . $this->typeIdToString($this->flag) . "!");
			$this->kick($player, $this->typetoReasonString($this->flag));
		} else {
			if(isset($this->lastFail[$player->getUniqueId()->__toString()][$this->flag])){
				if($this->lastFail[$player->getUniqueId()->__toString()][$this->flag] - microtime(true) > 5.0){
					unset($this->lastFail[$player->getUniqueId()->__toString()][$this->flag]);
					$this->failed[$player->getUniqueId()->__toString()][$this->flag] = 0;
				} else {
					$this->lastFail[$player->getUniqueId()->__toString()][$this->flag] = microtime(true);
				}
			}
			$this->notifyAdmins($player, true, "Blocked");
			$this->notifyOnDiscord($player, true);
			Core::getInstance()->getServer()->getLogger()->info(Anticheat::PREFIX . " " . Core::ARROW . " " . TF::RED . $player->getName() . " is suspected using " . $this->typeIdToString($this->flag) . "!");
			$this->failed[$player->getUniqueId()->__toString()][$this->flag]++;
			if(!isset($this->lastFail[$player->getUniqueId()->__toString()][$this->flag])) $this->lastFail[$player->getUniqueId()->__toString()][$this->flag] = microtime(true);
		}
	}

	public function notifyAdmins(Player|string $player, bool $punish = false) : void{
		foreach(Core::getInstance()->getServer()->getOnlinePlayers() as $staff){
			if($staff->hasPermission("pride.staff.anticheat") || Core::getInstance()->getServer()->isOp($staff->getName()) || $staff->getRankId() === Rank::OWNER || $staff->getRankId() === Rank::STAFF || $staff->getRankId() === Rank::ADMIN){
				if(is_string($player)){
					if($punish){
						$staff->sendMessage(Anticheat::PREFIX . " " . Core::ARROW . " " . TF::RED . $player . " is kicked for suspected using " . $this->typeIdToString($this->flag) . "!");
					} else {
						$staff->sendMessage(Anticheat::PREFIX . " " . Core::ARROW . " " . TF::RED . $player . " is suspected using " . $this->typeIdToString($this->flag) . "!");
					}
				} else {
					if($punish){
						$staff->sendMessage(Anticheat::PREFIX . " " . Core::ARROW . " " . TF::RED . $player->getName() . " is kicked for suspected using " . $this->typeIdToString($this->flag) . "!");
					} else {
						$staff->sendMessage(Anticheat::PREFIX . " " . Core::ARROW . " " . TF::RED . $player->getName() . " is suspected using " . $this->typeIdToString($this->flag) . "!");
					}
				}
			}
		}
	}

	public function notifyOnDiscord(Player|string $player, bool $blocked = true, string $punish = "Block") : void{
		if(is_string($player)){
			DiscordWebhook::getInstance()->sendAnticheatLog($player, $this->typeIdToString($this->getFlagId()), $this->typeToReasonString($this->getFlagId()), base64_encode($this->typeToReasonString($this->getFlagId())), $blocked, $punish);
		}

		if($player instanceof Player){
			DiscordWebhook::getInstance()->sendAnticheatLog($player->getName(), $this->typeIdToString($this->getFlagId()), $this->typeToReasonString($this->getFlagId()), base64_encode($this->typeToReasonString($this->getFlagId())), $blocked, $punish);
		}
	}

	public function getMaxViolation() : int{
		return Core::getInstance()->getConfigs()->getServerConfig()->getNested("anticheat.max-violation", 20);
	}

	public function typeIdToString(int $flag) : string{
		switch($flag){
			case Anticheat::REACH:
				return "Reach";
				break;
			case Anticheat::SPEED:
				return "Speed";
				break;
			case Anticheat::AUTOCLICKER:
				return "AutoClicker";
				break;
			case Anticheat::NOCLIP:
				return "NoClip or Phase";
				break;
			case Anticheat::VELOCITY:
				return "Velocity";
				break;
			case Anticheat::TIMER:
				return "Timer";
				break;
			case Anticheat::KILLAURA:
				return "Killaura";
				break;
			case Anticheat::GLITCH:
				return "Glitch or Bugging";
				break;
			case Anticheat::FLIGHT:
				return "Flight or Flying";
				break;
			case Anticheat::INSTABREAK:
				return "Instabreak or Nuke";
				break;
			case Anticheat::BADPACKET:
				return "Bad Packets";
				break;
			case Anticheat::NOPACKET:
				return "No Packet or Blink";
				break;
			case Anticheat::CHEST_STEALER:
				return "ChestStealer";
				break;
			case Anticheat::EDITION_FAKER:
				return "Edition Faker";
				break;
		}
	}

	public function typeToReasonString(int $flag) : string{
		switch($flag){
			case Anticheat::REACH:
				return Anticheat::REACH_HACK;
				break;
			case Anticheat::SPEED:
				return Anticheat::SPEED_HACK;
				break;
			case Anticheat::AUTOCLICKER:
				return Anticheat::AUTOCLICKER_HACK;
				break;
			case Anticheat::NOCLIP:
				return Anticheat::NOCLIP_HACK;
				break;
			case Anticheat::VELOCITY:
				return Anticheat::VELOCITY_HACK;
				break;
			case Anticheat::TIMER:
				return Anticheat::TIMER_HACK;
				break;
			case Anticheat::KILLAURA:
				return Anticheat::KILLAURA_HACK;
				break;
			case Anticheat::GLITCH:
				return Anticheat::GLITCH_HACK;
				break;
			case Anticheat::FLIGHT:
				return Anticheat::FLIGHT_HACK;
				break;
			case Anticheat::INSTABREAK:
				return Anticheat::INSTABREAK_HACK;
				break;
			case Anticheat::BADPACKET:
				return Anticheat::BADPACKET_HACK;
				break;
			case Anticheat::NOPACKET:
				return Anticheat::NOPACKET_HACK;
				break;
			case Anticheat::CHEST_STEALER:
				return Anticheat::CHESTSTEALER_HACK;
				break;
			case Anticheat::EDITION_FAKER:
				return Anticheat::EDITIONFAKER_HACK;
				break;
		}
	}

	public static function areAllBlocksAboveAir(Player $player) : bool {
		$level = $player->getWorld();
		$posX = $player->getPosition()->x;
		$posY = $player->getPosition()->y + 2;
		$posZ = $player->getPosition()->z;

		// loop through 3x3 square above player head to check for any non-air blocks
		for ($xidx = $posX - 1; $xidx <= $posX + 1; $xidx = $xidx + 1) {
			  for ($zidx = $posZ - 1; $zidx <= $posZ + 1; $zidx = $zidx + 1) {
				$pos = new Vector3($xidx, $posY, $zidx);
				$block = $level->getBlock($pos)->getTypeId();
				if ($block != BlockTypeIds::AIR){
					  return false;
				}
			  }
		}
		return true;
	}

	public static function getCurrentFrictionFactor(Player $player){
		$level = $player->getWorld();
		$posX = $player->getPosition()->x;
		$posY = $player->getPosition()->y - 1; #define position of block below player
		$posZ = $player->getPosition()->z;
		$frictionFactor = $level->getBlock(new Vector3($posX, $posY, $posZ))->getFrictionFactor(); # get friction factor from block
		for ($xidx = $posX - 1; $xidx <= $posX + 1; $xidx = $xidx + 1){
			  for ($zidx = $posZ - 1; $zidx <= $posZ + 1; $zidx = $zidx + 1){
				$pos = new Vector3($xidx, $posY, $zidx);
				if($level->getBlock($pos)->getTypeId() != BlockTypeIds::AIR){ # only use friction factor if block below isn't air
					  if($frictionFactor <= $level->getBlock($pos)->getFrictionFactor()){ # use new friction factor only if it has a higher value
						$frictionFactor = $level->getBlock($pos)->getFrictionFactor();
					  } else { # use block that is two blocks below otherwise
						  $pos->y = ($player->getPosition()->y - 2);
						  if($frictionFactor <= $level->getBlock($pos)->getFrictionFactor()){
							$frictionFactor = $level->getBlock($pos)->getFrictionFactor();
						  }
					}
				}
			  }
		}
		return $frictionFactor;
	}

	public static function load() : void{
		// load all available check class
		// some checks are aren't done
		foreach([
			new AutoClicker(),
			new Reach(),
			new NoClip(),
			new Instabreak(),
			new NoPacket(),
			new BadPackets(),
			new EditionFaker(),
			new Timer(),
			new Killaura(),
			new Glitch(),
			new Flight(),
			new BadPackets(),
			new Speed(),
		] as $module){
			$module->register($module);
			Core::getInstance()->getServer()->getLogger()->info(Anticheat::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "Enabled \"" . $module->typeIdToString($module->getFlagId()) . "\" module!");
		}
	}

	public static function XZDistanceSquared(Vector3 $v1, Vector3 $v2) : float{
		return ($v1->x - $v2->x) ** 2 + ($v1->z - $v2->z) ** 2;
	}

	public function register(Anticheat $module) : void{
		Core::getInstance()->getServer()->getPluginManager()->registerEvents($module, Core::getInstance());
		Core::getInstance()->getServer()->getLogger()->debug(Anticheat::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "Registered \"" . $module->typeIdToString($module->getFlagId()) . "\" module!");
	}
}
