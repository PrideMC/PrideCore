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

namespace PrideCore\Anticheat;

use pocketmine\block\BlockTypeIds;

use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat as TF;
use PrideCore\Anticheat\Modules\Flight;
use PrideCore\Anticheat\Modules\Glitch;
use PrideCore\Anticheat\Modules\Instabreak;
use PrideCore\Anticheat\Modules\NoClip;
use PrideCore\Anticheat\Modules\Killaura;
use PrideCore\Anticheat\Modules\NoPacket;
use PrideCore\Anticheat\Modules\Reach;
use PrideCore\Anticheat\Modules\Timer;
use PrideCore\Core;
use PrideCore\Player\Player;

use function base64_encode;

/**
 * TODO:
 * - Improve AntiAura
 * - Improve more checks on AntiReach
 * - Velocity check
 * - Bad packets check
 * - Chest stealer check
 * - Edition Faker (for most advance clients)
 * - Improve player speed check
 */
abstract class Anticheat {

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
	public const CHEST_STEALLER = 12;
	public const EDITION_FAKER = 13;

	private int $flag;

	public function __construct(int $flag_id)
	{
		$this->flag = $flag_id;
	}

	public function getFlagId() : int{
		return $this->flag;
	}

	public array $failed = [];

	public function kick(Player $player, string $reason) : void{
		$player->kick(TF::GRAY . "Error: " . base64_encode($reason), $reason);
	}

	public function fail(Player $player){
		if(!isset($this->failed[$player->getUniqueId()->__toString()])) $this->failed[$player->getUniqueId()->__toString()] = 1;
		if($this->failed[$player->getUniqueId()->__toString()] > $this->getMaxViolation()){
			unset($this->failed[$player->getUniqueId()->__toString()]);
			Core::getInstance()->getLogger()->info(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . $player->getName() . " is kicked for suspected using " . $this->typeIdToString($this->flag) . "!");
			$this->kick($player, $this->typetoReasonString($this->flag));
		} else {
			Core::getInstance()->getLogger()->info(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . $player->getName() . " is suspected using " . $this->typeIdToString($this->flag) . "!");
			$this->failed[$player->getUniqueId()->__toString()]++;
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
				return "Flight";
				break;
			case Anticheat::INSTABREAK:
				return "Instabreak or Nuke";
				break;
			case Anticheat::BADPACKET:
				return "Bad Packets";
				break;
			case Anticheat::NOPACKET:
				return "NoPacket";
				break;
			case Anticheat::CHEST_STEALLER:
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
			case Anticheat::CHEST_STEALLER:
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
		(new Reach());
		//(new Flight()); TODO
        (new Killaura());
		(new NoClip());
		(new Glitch());
		(new Instabreak());
		(new NoPacket());
		(new Timer());
	}
}
