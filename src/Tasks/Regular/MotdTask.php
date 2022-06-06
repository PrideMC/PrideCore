<?php

declare(strict_types=1);

namespace PrideCore\Tasks\Regular;

use pocketmine\scheduler\Task;
use PrideCore\PrideCore;
use pocketmine\utils\TextFormat as T;

class MotdTask extends Task {
	
	private array $motd = [
		"§e§lPride§gMC§r §l§b» §r§aPractice Server!",
        "§e§lPride§gMC§r §l§b» §r§aSession #3!",
        "§e§lPride§gMC§r §l§b» §r§aNew Games!",
        "§e§lPride§gMC§r §l§b» §r§aNew Cosmetics!"
	];
	
	private int $old = 0;
	
	public function __construct(){
	}
	
	public function onRun() :void {
		PrideCore::getInstance()->getServer()->getNetwork()->setName($this->motd[$this->old]);
		++$this->old;
		
		if($this->old === count($this->motd)) $this->old = 0;
	}
}