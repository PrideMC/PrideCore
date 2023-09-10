<?php

namespace PrideCore\Tasks\Anticheat;

use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\scheduler\Task;

class TeleportTask extends Task {

    private Entity $entity;
    private Location $location;

	public function __construct(Entity $entity, Location $location) {
		$this->entity = $entity;
		$this->location = $location;
	}

	public function onRun() : void{
		$this->entity->teleport($this->location);
	}
}