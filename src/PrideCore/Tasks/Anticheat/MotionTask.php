<?php

namespace PrideCore\Tasks\Anticheat;

use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;

class MotionTask extends Task {

    private Entity $entity;
    private Vector3 $vector3;

	public function __construct(Entity $entity, Vector3 $vector3) {
		$this->entity = $entity;
		$this->vector3 = $vector3;
	}

	public function onRun() : void{
		$this->entity->setMotion($this->vector3);
	}
}