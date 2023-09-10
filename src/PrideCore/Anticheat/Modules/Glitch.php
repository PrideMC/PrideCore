<?php

namespace PrideCore\Anticheat\Modules;

use pocketmine\block\Door;
use pocketmine\block\FenceGate;
use pocketmine\block\Trapdoor;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\EnderPearl;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\math\Vector3;
use pocketmine\player\GameMode;
use pocketmine\world\Position;
use PrideCore\Anticheat\Anticheat;
use PrideCore\Core;
use PrideCore\Player\Player;
use PrideCore\Tasks\Anticheat\MotionTask;
use PrideCore\Tasks\Anticheat\TeleportTask;

class Glitch extends Anticheat implements Listener{

    public function __construct()
    {
        parent::__construct(Anticheat::GLITCH);
        Core::getInstance()->getServer()->getPluginManager()->registerEvents($this, Core::getInstance());
    }

    private array $pearlland = [];

    public function onPearlLandBlock(ProjectileHitEvent $event) {
        $player = $event->getEntity()->getOwningEntity();
        if ($player instanceof Player && $event->getEntity() instanceof EnderPearl) $this->pearlland[$player->getName()] = Core::getInstance()->getServer()->getTick();
    }

    public function onTP(EntityTeleportEvent $event) {
        $entity = $event->getEntity();
        if (!$entity instanceof Player) return;
        $world = $entity->getWorld();
        $to = $event->getTo();
        if (!isset($this->pearlland[$entity->getName()])) return;
        if (Core::getInstance()->getServer()->getTick() != $this->pearlland[$entity->getName()]) return; //Check if teleportation was caused by enderpearl (by checking is a projectile landed at the same time as teleportation) TODO Find a less hacky way of doing this?

        //Get coords and adjust for negative quadrants.
        $x = $to->getX();
        $y = $to->getY();
        $z = $to->getZ();
        if($x < 0) $x = $x - 1;
        if($z < 0) $z = $z - 1;

        //If pearl is in a block as soon as it lands (which could only mean it was shot into a block over a fence), put it back down in the fence. TODO Find a less hacky way of doing this?
        if($this->isInHitbox($world, $x, $y, $z)) $y = $y - 0.5;

        if ($this->isInHitbox($world, $entity->getLocation()->getX(), $entity->getLocation()->getY() + 1.5, $entity->getLocation()->getZ())) {
            $this->fail($entity);
			$event->cancel();
			return;
		}

        //Try to find a good place to teleport.
		$ys = $y;
        foreach (range(0, 1.9, 0.05) as $n) {
			$xb = $x;
			$yb = ($ys - $n);
			$zb = $z;

			if ($this->isInHitbox($world, ($x + 0.05), $yb, $z)) $xb = $xb - 0.3;
			if ($this->isInHitbox($world, ($x - 0.05), $yb, $z)) $xb = $xb + 0.3;
			if ($this->isInHitbox($world, $x, $yb, ($z - 0.05))) $zb = $zb + 0.3;
			if ($this->isInHitbox($world, $x, $yb, ($z + 0.05))) $zb = $zb - 0.3;


            if($this->isInHitbox($world, $xb, $yb, $zb)) {
                break;
            } else {
				$x = $xb;
				$y = $yb;
				$z = $zb;
			}
        }

		//Check if pearl lands in an area too small for the player
		foreach (range(0.1, 1.8, 0.1) as $n) {
			if($this->isInHitbox($world, $x, ($y + $n), $z)) {

				//Teleport the player into the middle of the block so they can't phase into an adjacent block.
				if(isset($world->getBlockAt((int)$xb, (int)$yb, (int)$zb)->getCollisionBoxes()[0])) {
					$blockHitBox = $world->getBlockAt((int)$xb, (int)$yb, (int)$zb)->getCollisionBoxes()[0];
					if($x < 0) {
						$x = (($blockHitBox->minX + $blockHitBox->maxX) / 2) - 1;
					} else {
						$x = ($blockHitBox->minX + $blockHitBox->maxX) / 2;
					}
					if($z < 0) {
						$z = (($blockHitBox->minZ + $blockHitBox->maxZ) / 2) - 1;
					} else {
						$z = ($blockHitBox->minZ + $blockHitBox->maxZ) / 2;
					}
				}
				//Prevent pearling into areas too small (configurable in config)
				$this->fail($entity);
				$event->cancel();
				if($x < 0) $x = $x + 1;
				if($z < 0) $z = $z + 1;
				Core::getInstance()->getScheduler()->scheduleDelayedTask(new TeleportTask($entity, new Location($x, $y, $z, $entity->getWorld(), $entity->getLocation()->getYaw(), $entity->getLocation()->getPitch())), 5);
			}
		}

        //Readjust for negative quadrants
        if($x < 0) $x = $x + 1;
        if($z < 0) $z = $z + 1;

        //Send new safe location
        $event->setTo(new Position($x, $y, $z, $world));
    }

    public function isInHitbox($level, $x, $y, $z) {
        if(!isset($level->getBlockAt((int)$x, (int)$y, (int)$z)->getCollisionBoxes()[0])) return False;
        foreach ($level->getBlockAt((int)$x, (int)$y, (int)$z)->getCollisionBoxes() as $blockHitBox) {
			if($x < 0) $x = $x + 1;
			if($z < 0) $z = $z + 1;
			if (($blockHitBox->minX < $x) AND ($x < $blockHitBox->maxX) AND ($blockHitBox->minY < $y) AND ($y < $blockHitBox->maxY) AND ($blockHitBox->minZ < $z) AND ($z < $blockHitBox->maxZ)) return True;
		}
        return False;
    }

    public function onBlockPlace(BlockPlaceEvent $event) {
        $player = $event->getPlayer();
		$block = $event->getBlockAgainst();
		if ($player->isCreative() or $player->isSpectator()) return;
            if ($event->isCancelled()) {
			    $playerX = $player->getLocation()->getX();
			    $playerZ = $player->getLocation()->getZ();
				if($playerX < 0) $playerX = $playerX - 1;
				if($playerZ < 0) $playerZ = $playerZ - 1;
				if (($block->getPosition()->getX() == (int)$playerX) AND ($block->getPosition()->getZ() == (int)$playerZ) AND ($player->getPosition()->getY() > $block->getPosition()->getY())) { #If block is under the player
					$playerMotion = $player->getMotion();
					Core::getInstance()->getScheduler()->scheduleDelayedTask(new MotionTask($player, new Vector3($playerMotion->getX(), -0.1, $playerMotion->getZ())), 2);
                    $this->fail($player);
            }
        }
    }

    public function onBlockBreak(BlockBreakEvent $event) {
        $player = $event->getPlayer();
        $block = $event->getBlock();
		if ($player->isCreative() or $player->isSpectator()) return;
        if ($event->isCancelled()) {
			$x = $player->getLocation()->getX();
			$y = $player->getLocation()->getY();
			$z = $player->getLocation()->getZ();
			$playerX = $player->getLocation()->getX();
			$playerZ = $player->getLocation()->getZ();
			if($playerX < 0) $playerX = $playerX - 1;
			if($playerZ < 0) $playerZ = $playerZ - 1;
			if (($block->getPosition()->getX() == (int)$playerX) AND ($block->getPosition()->getZ() == (int)$playerZ) AND ($player->getLocation()->getY() > $block->getPosition()->getY())) { #If block is under the player
				foreach ($block->getCollisionBoxes() as $blockHitBox) {
					$y = max([$y, $blockHitBox->maxY]);
				}
				$player->teleport(new Vector3($x, $y, $z));
			} else { #If block is on the side of the player
				$xb = 0;
				$zb = 0;
				foreach ($block->getCollisionBoxes() as $blockHitBox) {
					if (abs($x - ($blockHitBox->minX + $blockHitBox->maxX) / 2) > abs($z - ($blockHitBox->minZ + $blockHitBox->maxZ) / 2)) {
						$xb = (5 / ($x - ($blockHitBox->minX + $blockHitBox->maxX) / 2)) / 24;
					} else {
						$zb = (5 / ($z - ($blockHitBox->minZ + $blockHitBox->maxZ) / 2)) / 24;
					}
				}
				$player->setMotion(new Vector3($xb, 0, $zb));
			}
			$this->fail($player);
        }
    }

    public function onInteract(PlayerInteractEvent $event) {
		if ($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK) return;
		$player = $event->getPlayer();
		if ($player->isCreative() or $player->isSpectator()) return;
		$block = $event->getBlock();
		if ($event->isCancelled()) {
			if ($block instanceof Door or $block instanceof FenceGate or $block instanceof Trapdoor) {
			$x = $player->getLocation()->getX();
			$y = $player->getLocation()->getY();
			$z = $player->getLocation()->getZ();
			$playerX = $player->getLocation()->getX();
			$playerZ = $player->getLocation()->getZ();
			if ($playerX < 0) $playerX = $playerX - 1;
			if ($playerZ < 0) $playerZ = $playerZ - 1;
			if (($block->getPosition()->getX() == (int)$playerX) and ($block->getPosition()->getZ() == (int)$playerZ) and ($player->getLocation()->getY() > $block->getPosition()->getY())) { #If block is under the player
					foreach ($block->getCollisionBoxes() as $blockHitBox) {
						$y = max([$y, $blockHitBox->maxY + 0.05]);
					}
					$player->teleport(new Vector3($x, $y, $z), $player->getLocation()->getYaw(), 35);
				} else {
					foreach ($block->getCollisionBoxes() as $blockHitBox) {
						if (abs($x - ($blockHitBox->minX + $blockHitBox->maxX) / 2) > abs($z - ($blockHitBox->minZ + $blockHitBox->maxZ) / 2)) {
							$xb = (3 / ($x - ($blockHitBox->minX + $blockHitBox->maxX) / 2)) / 25;
							$zb = 0;
						} else {
							$xb = 0;
							$zb = (3 / ($z - ($blockHitBox->minZ + $blockHitBox->maxZ) / 2)) / 25;
						}
						$player->teleport($player->getLocation()->asVector3(), $player->getLocation()->getYaw(), 85);
						$player->setMotion(new Vector3($xb, 0, $zb));
					}
				}

				$this->fail($player);
			}
		}
	}


}