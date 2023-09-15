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

namespace PrideCore\Anticheat\Modules;

use PrideCore\Anticheat\Anticheat;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\entity\effect\VanillaEffects;

class Speed extends Anticheat implements Listener {
    
    public function __construct()
    {
        parent::__construct(Anticheat::SPEED);
    }

    public const MAX_SPEED = 1.4;

    public function speedV1(PlayerMoveEvent $event) : void{
        if($event->getPlayer()->getEffects()->has(VanillaEffects::SPEED())) return;
        
        if(($d = Anticheat::XZDistanceSquared($event->getFrom(), $event->getTo())) > Speed::MAX_SPEED){
            $event->cancel();
            $this->fail($event->getPlayer());
        }elseif($d > 3){
            $event->cancel();
            $this->fail($event->getPlayer());
        }
    }
}