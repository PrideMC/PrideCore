<?php

declare(strict_types=1);

namespace PrideCore\Listeners;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\BlockLegacyIds;
use pocketmine\item\ItemFactory;
use pocketmine\utils\TextFormat as T;
use PrideCore\PrideCore;
use PrideCore\Utils\InventoryManager;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\TextPacket;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\scheduler\ClosureTask;
use pocketmine\permission\BanEntry;
use PrideCore\Utils\Interfaces\IPermissions;
use PrideCore\Tasks\Regular\KilledTask;
use pocketmine\player\GameMode;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;

class PracticeListener implements Listener {
	public function __construct(){
		$this->plugin = PrideCore::getInstance();
	}
	
	private array $clicks = [];
	
	
	public function onUse(PlayerItemUseEvent $ev) :void
	{
		$player = $ev->getPlayer();
		$item = $ev->getItem();
		$heart = $player->getHealth();
		$currentHealth1 = $player->getHealth() / 2;
		switch($item->getCustomName()) {
			case InventoryManager::SOUP_ITEM:
				if($player->getWorld() === $this->plugin->getServer()->getWorldManager()->getWorldByName("soup-ffa")){
					if($player->getHealth() === 20.0){
						$player->sendTip(T::RED . $currentHealth1 . T::DARK_RED ." ❤");
					} else {
						$player->setHealth($player->getHealth() + 2);
						$player->sendTip(T::GREEN . $currentHealth1 . T::DARK_RED ." ❤");
						$player->getInventory()->clear($player->getInventory()->getHeldItemIndex());
					}
				}
				break;
		}
	}
	
	public function onPacket(DataPacketReceiveEvent $event): void 
	{
        $packet = $event->getPacket();
        $player = $event->getOrigin()->getPlayer();
	}
	
	
    public function onDataPacketReceive(DataPacketReceiveEvent $ev): void
    {
        $player = $ev->getOrigin()->getPlayer();
        $packet = $ev->getPacket();
        if ($player !== null && $player->isOnline()) {
            switch ($packet->pid()) {
                case AnimatePacket::NETWORK_ID:
                    switch ($packet->action) {
                        case AnimatePacket::ACTION_SWING_ARM:
                            $player->getServer()->broadcastPackets($player->getViewers(), [$packet]);
                            $ev->cancel();
                            break;
                    }
                    break;
            }
        }
    }
	
	public function addCps() : void 
	{
		array_unshift($this->clicks, microtime(true));
		if(count($this->clicks) >= 100) array_pop($this->clicks);
	}

	public function getCps() : float 
	{
		if(empty($this->clicks)){
			return 0.0;
		}
		$ct = microtime(true);
		return round(count(array_filter($this->clicks, static function(float $t) use ($ct) : bool{
				return ($ct - $t) <= 1.0;
			})) / 1.0, 1);
	}
	
	public function onDeath(PlayerDeathEvent $ev)
	{
        $player = $ev->getPlayer();
        $cause = $player->getLastDamageCause();
        $ev->setDrops([]);
        if ($cause instanceof EntityDamageByEntityEvent) {
            $killer = $cause->getDamager();
            if ($killer instanceof RPlayer) {
                $deathmsg = ["§6{$player->getName()} §7was killed by §6{$killer->getName()}", "§6{$killer->getName()} §7was the better player against §6{$player->getName()}", "§6{$player->getName()} §7was knocked out by §6{$killer->getName()}", "§6{$player->getName()} §7was sent to space by §6{$killer->getName()}", "§6{$player->getName()} §7was taken out by §6{$killer->getName()}", "§6{$player->getName()} §7was sent to heaven by §6{$killer->getName()}", "§6{$killer->getName()} §7sent §6{$player->getName()} §7to spawn!", "§6{$player->getName()} §7was split open by §6{$killer->getName()}"];
                $ev->setDeathMessage($deathmsg[array_rand($deathmsg)]);
                $this->plugin->getServer()->dispatchCommand($killer, "rekit");
				$killer->sendMessage(T::RED . "You killed " . $player->getName());
				$ev->setDrops([]);
				return;
            }
		}
	}
	
}