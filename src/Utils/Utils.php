<?php

declare(strict_types=1);

namespace PrideCore\Utils;

use PrideCore\PrideCore;
use pocketmine\player\Player;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;

class Utils {
	
	private array $data = [];
	
	public function getOwnedTags(Player $player) {
		foreach([
			"cosmetics.tag.1",
			"cosmetics.tag.2",
			"cosmetics.tag.3",
			"cosmetics.tag.4",
			"cosmetics.tag.5",
			"cosmetics.tag.6",
			"cosmetics.tag.7",
			"cosmetics.tag.8",
			"cosmetics.tag.9",
			"cosmetics.tag.10",
		] as $perm){
			if($player->hasPermission($perm)){
				$this->addOwn();
			}
		}
		
		return $this->getOwn();
	}
	
	public function playSound(Player $player, string $sound, float $minimumVolume = 1.0, float $volume = 1.0, float $pitch = 1.0){
		$position = null;
		
		$pos = $player->getPosition();
        $pk = new PlaySoundPacket();
        $pk->soundName = $sound;
        $pk->volume = $volume > $minimumVolume ? $minimumVolume : $volume;
        $pk->pitch = $pitch;
        $pk->x = $pos->x;
        $pk->y = $pos->y;
        $pk->z = $pos->z;
		$player->getNetworkSession()->sendDataPacket($pk);
		
	}
	
	public function addOwn(){
		array_unshift($this->data, microtime(true));
	}
	
	public function getOwn() : float {
		if(empty($this->data)){
			return 0.0;
		}
		$ct = microtime(true);
		return round(count(array_filter($this->data, static function(float $t) use ($ct) : bool{
				return ($ct - $t) <= 1.0;
			})) / 1.0, 1);
	}
}