<?php

namespace PrideCore\Anticheat\Modules;

use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJumpEvent;
use PrideCore\Anticheat\Anticheat;

class HighJump extends Anticheat implements Listener{

    public function __construct()
    {
        parent::__construct(Anticheat::HIGHJUMP);
    }

    public function highJumpV1(PlayerJumpEvent $event) : void{

        $player = $event->getPlayer();

        if($player->getEffects()->has(VanillaEffects::JUMP_BOOST())) return;

        
    }
}