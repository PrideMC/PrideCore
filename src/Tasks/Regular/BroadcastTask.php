<?php

declare(strict_types=1);

namespace PrideCore\Tasks\Regular;

use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat as T;
use PrideCore\PrideCore;

class BroadcastTask extends Task {
	
	private array $message = [
		"Season #3 is now out!",
		"Thank you for joining in the server!",
		"Do you see hacking or breaking rules? Report us by using command " . T::LIGHT_PURPLE . "/report",
	];
	
	private int $old = 0;
	
	public function __construct(){
	}
	
	public function onRun() :void {
		PrideCore::getInstance()->getServer()->broadcastMessage(T::GRAY . "[" . T::YELLOW . "!" . T::GRAY . "] " . T::RESET . $this->message[$this->old]);
		++$this->old;
		if($this->old === count($this->message)) $this->old = 0;
	}
}