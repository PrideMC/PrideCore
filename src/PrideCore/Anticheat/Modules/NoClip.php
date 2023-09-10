<?php

namespace PrideCore\Anticheat\Modules;

use pocketmine\block\BlockTypeIds;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\math\Vector3;
use pocketmine\player\GameMode;
use PrideCore\Anticheat\Anticheat;
use PrideCore\Core;
use PrideCore\Player\Player;

class NoClip extends Anticheat implements Listener
{

    public function __construct()
    {
        parent::__construct(Anticheat::NOCLIP);
        Core::getInstance()->getServer()->getPluginManager()->registerEvents($this, Core::getInstance());
    }

    private array $lastMoveUpdates = [];

    public function onMove(PlayerMoveEvent $event) {
        $id = $event->getPlayer()->getWorld()->getBlock($event->getPlayer()->getLocation())->getTypeId();
        if ($event->getPlayer()->getWorld()->getBlock($event->getPlayer()->getLocation()->add(0, 1, 0))->isSolid() and $id !== BlockTypeIds::SAND and $id !== BlockTypeIds::GRAVEL and $event->getPlayer()->getGamemode() !== GameMode::SPECTATOR()) {
            $event->cancel();
            $this->fail($event->getPlayer());
            $event->getPlayer()->teleport(new Vector3($event->getPlayer()->getLocation()->getX(), ($event->getPlayer()->getWorld()->getHighestBlockAt($event->getPlayer()->getLocation()->getX(), $event->getPlayer()->getLocation()->getZ()) + 1), $event->getPlayer()->getLocation()->getZ()));
            return;
        }
        $this->lastMoveUpdates[$event->getPlayer()->getName()] = $event->getTo();
    }

    public function onTeleport(EntityTeleportEvent $event) {
        if (!$event->getEntity() instanceof Player) return;
        $this->lastMoveUpdates[$event->getEntity()->getName()] = $event->getTo();
    }

}
