<?php

declare(strict_types=1);

namespace PrideCore\Tasks\Regular;

use PrideCore\PrideCore;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat as T;

class HealthTask extends Task {
	public function __construct(){
		$this->plugin = PrideCore::getInstance();
	}
	
	public function onRun() :void {
		foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
			$world = $this->plugin->getServer()->getWorldManager();
			switch($player->getWorld()){
				case $world->getWorldByName("soup-ffa"):
					$player->setScoreTag(floor($player->getHealth() / 2) . T::RED . " ❤");
					break;
				case $world->getWorldByName("gapple-ffa"):
					$player->setScoreTag(floor($player->getHealth() / 2) . T::RED . " ❤");
					break;
				case $world->getWorldByName("nodebuff-ffa"):
					$player->setScoreTag(floor($player->getHealth() / 2) . T::RED . " ❤");
					break;
				case $world->getWorldByName("build-ffa"):
					$player->setScoreTag(floor($player->getHealth() / 2) . T::RED . " ❤");
					break;
				case $world->getWorldByName("fist-ffa"):
					$player->setScoreTag(floor($player->getHealth() / 2) . T::RED . " ❤");
					break;
				case $world->getWorldByName("sumo-ffa"):
					$player->setScoreTag(floor($player->getHealth() / 2) . T::RED . " ❤");
					break;
			}
		}
	}
}