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

namespace PrideCore\Anticheat\Modules;

use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\math\Vector3;
use pocketmine\world\Position;
use PrideCore\Anticheat\Anticheat;
use PrideCore\Core;
use function array_filter;
use function ceil;
use function floor;
use function round;

class Flight extends Anticheat implements Listener{

	private $isElevating = [];
	private $flyTags = [];
	private $kicks = [];
	private $speedpoints = [];

	public const FLY_TAGS = 5;
	public const MAX_KICKS = 3;
	public const MAX_POINTS = 7;

	public function __construct()
	{
		parent::__construct(Anticheat::FLIGHT);
		Core::getInstance()->getServer()->getPluginManager()->registerEvents($this, Core::getInstance());
	}

	public function onMove(PlayerMoveEvent $event){
		$p = $event->getPlayer();

		if($p->isCreative() || $p->isSpectator() || $p->getAllowFlight() || $p->getEffects()->has(VanillaEffects::JUMP_BOOST()) || $p->getRankId() === Rank::OWNER || $p->getRankId() === Rank::STAFF || $p->getRankId() === Rank::ADMIN) return;

		$name = $p->getName();
		$isAirUnder = Flight::isAirUnder($p->getPosition());

		if(!$isAirUnder){
			if(isset($this->isElevating[$name])){
				unset($this->isElevating[$name]);
			}
		} else {
			$fromY = $event->getFrom()->y;
			$toY = $event->getTo()->y;

			if($toY < $fromY && isset($this->isElevating[$name])){
				$this->isElevating[$name] -= $fromY - $toY;
				if($this->isElevating[$name] <= 0){
					unset($this->isElevating[$name]);
				}
			}

			elseif($toY > $fromY){
				isset($this->isElevating[$name]) ?
					$this->isElevating[$name] += $toY - $fromY
					:
					$this->isElevating[$name] = $toY - $fromY
				;

				if($this->isElevating[$name] > 1.5){
					Flight::FLY_TAGS !== -1 && ++$this->flyTags[$name];
				}
			}

			elseif(round($fromY, 5) === round($toY, 5)){
				Flight::FLY_TAGS !== -1 && ++$this->flyTags[$name];
			}

			$this->fail($p);

			if($p->getEffects()->has(VanillaEffects::SPEED()) || $p->getRankId() === Rank::OWNER || $p->getRankId() === Rank::STAFF || $p->getRankId() === Rank::ADMIN) return;

			if(($d = Flight::XZDistanceSquared($event->getFrom(), $event->getTo())) > 1.4){
				++$this->speedpoints[$name];
			}elseif($d > 3){
				isset($this->speedpoints[$name]) ? $this->speedpoints[$name] = 2 : $this->speedpoints[$name] += 2;
			   }elseif($d > 0){
				$this->speedpoints[$name] -= 1;
			}

			if(isset($this->speedpoints[$name]) && $this->speedpoints[$name] === Flight::MAX_POINTS){
				if((isset($this->kicks[$name]) && $this->kicks[$name] < Flight::MAX_KICKS - 1) || !isset($this->kicks[$name])){
					unset($this->speedpoints[$name]);
					 ++$this->kicks[$name];
					 $this->fail($p);
				} else {
					   unset($this->kicks[$name]);
				}
				return;
			}
		}
	}

	public static function isAirUnder(Position $pos) : bool{
		$under = [];
		$last = [];
		$y = $pos->y - 1;

		$under[] = $pos->world->getBlockAt($pos->x, $y, $pos->z);

		if(round($pos->x) === floor($pos->x)){
			$under[] = $pos->world->getBlockAt($pos->x - 1, $y, $pos->z);
			$last[0] = floor($pos->x) - 1;
		}elseif(round($pos->x) === ceil($pos->x)){
			$under[] = $pos->world->getBlockAt($pos->x, $y, $pos->z);
			$last[0] = ceil($pos->x);
		}

		if(round($pos->z) === floor($pos->z)){
			$under[] = $pos->world->getBlockAt($pos->x, $y, $pos->z - 1);
			$last[1] = floor($pos->z) - 1;
		}elseif(round($pos->z) === ceil($pos->z)){
			$under[] = $pos->world->getBlockAt($pos->x, $y, $pos->z);
			$last[1] = ceil($pos->z);
		}

		$under[] = $pos->world->getBlockAt($last[0], $y, $last[1]);

		return !array_filter($under);
	}

	public static function XZDistanceSquared(Vector3 $v1, Vector3 $v2) : float{
		return ($v1->x - $v2->x) ** 2 + ($v1->z - $v2->z) ** 2;
	}
}
