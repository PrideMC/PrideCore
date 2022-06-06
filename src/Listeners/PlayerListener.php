<?php

declare(strict_types=1);

namespace PrideCore\Listeners;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\permission\BanEntry;
use pocketmine\event\player\PlayerChangeSkinEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\block\BlockLegacyIds;
use pocketmine\utils\TextFormat as T;
use pocketmine\player\GameMode;
use pocketmine\block\Opaque;
use pocketmine\block\VanillaBlocks;
use pocketmine\scheduler\ClosureTask;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\Server;
use PrideCore\RPlayer;
use PrideCore\PrideCore;
use PrideCore\Utils\Interfaces\IPermissions;
use PrideCore\Utils\Interfaces\IMessages;
use PrideCore\Tasks\Regular\KilledTask;
use PrideCore\Utils\InventoryManager;


class PlayerListener implements Listener
{
	
	public function onCreation(PlayerCreationEvent $ev) :void 
	{
		$ev->setPlayerClass(RPlayer::class);
	}
	
	public function onJoin(PlayerJoinEvent $ev) :void 
	{
		$player = $ev->getPlayer();
		if (!$player instanceof RPlayer) return;
		PrideCore::getInstance()->getServer()->dispatchCommand($player, 'spawn');
		$ev->setJoinMessage("");
		
		switch(strtolower($player->getName())){
			case "hen2527":
				$player->setNameTag(T::GRAY . "[" . T::RED . "Owner" . T::GRAY . "] " . T::RESET . T::RED . $player->getName());
				$perms = ["cosmetics.tag.1", "cosmetics.tag.10"];
				$this->addPermissions($player, $perms);
				break;
			case "xqwtxon":
				$player->setNameTag(T::GRAY . "[" . T::RED . "Owner" . T::GRAY . "] " . T::RESET . T::RED . $player->getName());
				$perms = ["cosmetics.tag.1", "cosmetics.tag.10"];
				$this->addPermissions($player, $perms);
				break;
			default: 
				$player->setNameTag(T::GRAY . $player->getName());
				break;
		}
	}
	
	public function onLogin(PlayerPreLoginEvent $ev) :void
	{
		$pinfo = $ev->getPlayerInfo();
		$player = PrideCore::getInstance()->getServer()->getPlayerExact($pinfo->getUsername());
		$entry = new BanEntry($pinfo->getUsername());
		$reason = $entry->getReason() ? "Banned by an operator." : "Unspecified";
		if (!$player instanceof RPlayer) return;
		if ($player->isBanned()) {
            $player->kick(T::YELLOW . "PrideMC Network\n\n" . T::GRAY . "You have been banned to server.\n" . T::GRAY . "Reason: " . T::RESET . $reason);
        }
	}
	
	
	public function onRespawn(PlayerRespawnEvent $ev)
    {
		PrideCore::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($ev): void {
				$player = $ev->getPlayer();
				$player->setGamemode(GameMode::SPECTATOR());
				$player->setMaxHealth(3);
				$player->sendTitle(T::RED . T::BOLD . "YOU DIED!");
				$player->sendTip(T::GREEN . "Spawning in 5");
			}), 2);
		PrideCore::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($ev): void {
				$player = $ev->getPlayer();
				$player->sendTitle(T::RED . T::BOLD . "YOU DIED!");
				$player->sendTip(T::GREEN . "Spawning in 4");
			}), 40);
		PrideCore::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($ev): void {
				$player = $ev->getPlayer();
				$player->sendTitle(T::RED . T::BOLD . "YOU DIED!");
				$player->sendTip(T::GREEN . "Spawning in 3");
			}), 60);
		PrideCore::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($ev): void {
				$player = $ev->getPlayer();
				$player->sendTitle(T::RED . T::BOLD . "YOU DIED!");
				$player->sendTip(T::GREEN . "Spawning in 2");
			}), 80);
		PrideCore::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($ev): void {
				$player = $ev->getPlayer();
				$player->sendTitle(T::RED . T::BOLD . "YOU DIED!");
				$player->sendTip(T::GREEN . "Spawning in 1");
			}), 100);
		PrideCore::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($ev): void {
				$player = $ev->getPlayer();
				$player->sendTitle(T::YELLOW . T::BOLD . "RESPAWNED!");
				$player->setGamemode(GameMode::SURVIVAL());
				$pos = $player->getWorld()->getSpawnLocation();
				$player->teleport($pos);
				$player->setMaxHealth(20);
				(new InventoryManager())->inv($player, InventoryManager::PRACTICE_LOBBY);
			}), 120);
        return false;
    }
	
	public function onPlace(BlockPlaceEvent $ev): void
    {
		$block = $ev->getBlock();
        $player = $ev->getPlayer();
		if($player->getWorld() === PrideCore::getInstance()->getServer()->getWorldManager()->getWorldByName("build-ffa"))
		{
			PrideCore::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($ev): void {
				$block = $ev->getBlock();
				$block->getPosition()->getWorld()->setBlock($block->getPosition(), VanillaBlocks::AIR());
			}), 20*15);
		} else 
		{
				$ev->cancel();
				return;
		}
		if(!$player->hasPermission(IPermissions::BUILD))
		{
			$ev->cancel();
		}
	}
	
	public function onDestroy(BlockBreakEvent $ev) 
	{
		$player = $ev->getPlayer();
		$block = $ev->getBlock();
		if($player->getWorld() === PrideCore::getInstance()->getServer()->getWorldManager()->getWorldByName("build-ffa")){
			return;
		}
		if(!$player->hasPermission(IPermissions::BUILD)){
			$ev->cancel();
			return;
		}
	}
	
	public function onSkinChange(PlayerChangeSkinEvent $ev)
    {
        if (!$ev->getPlayer()->hasPermission(IPermissions::CHANGE_SKIN)){
            $ev->cancel();
			return;
        }
    }
	
	
	public function onInventoryTransaction(InventoryTransactionEvent $ev): void
    {
        $transaction = $ev->getTransaction();
        $player = $transaction->getSource();
		if ($player->getWorld() === PrideCore::getInstance()->getServer()->getWorldManager()->getWorldByName("practice-lobby") and !$player->isImmobile() && !$player->getGamemode() === GameMode::CREATIVE()) $ev->cancel();
		if ($player->getWorld() === PrideCore::getInstance()->getServer()->getWorldManager()->getWorldByName("lobby-1") and !$player->isImmobile() && !$player->getGamemode() === GameMode::CREATIVE()) $ev->cancel();
        if ($player->getWorld() === PrideCore::getInstance()->getServer()->getWorldManager()->getWorldByName("lobby-2") and !$player->isImmobile() && !$player->getGamemode() === GameMode::CREATIVE()) $ev->cancel();
    }

    public function onDrop(PlayerDropItemEvent $ev): void
    {
        if ($ev->getPlayer()->getGamemode() === GameMode::SURVIVAL()) {
            $ev->cancel();
			return;
        }
    }
	
	
	public function onChat(PlayerChatEvent $ev) :void {
		$player = $ev->getPlayer();
		
		switch(strtolower($player->getName())){
			case "xqwtxon":
				$ev->setFormat(T::GRAY . "[" . T::RED . "Owner" . T::GRAY . "] " . T::RESET . T::RED . $player->getName() . T::DARK_GRAY . " > " . T::RESET . $ev->getMessage());
				break;
			case "hen2527":
				$ev->setFormat(T::GRAY . "[" . T::RED . "Owner" . T::GRAY . "] " . T::RESET . T::RED . $player->getName() . T::DARK_GRAY . " > " . T::RESET . $ev->getMessage());
				break;
			default:
				$ev->setFormat(T::GRAY . $player->getName() . T::DARK_GRAY . " > " . T::RESET . $ev->getMessage());
				break;
		}
	}
	
	public function addPermissions(RPlayer $player, array $perms = []): void
    {
        if (!isset($this->permissions[$player->getNameTag()])) $this->permissions[$player->getName()] = [];
        $permissions = $this->permissions[$player->getName()];
        $permissions = array_merge($permissions, $perms);
        $this->permissions[$player->getName()] = $permissions;
        foreach ($permissions as $perm) {
            $attachement = $player->addAttachment(PrideCore::getInstance());
            $attachement->setPermission($perm, true);
            $player->addAttachment(PrideCore::getInstance(), $perm);
        }
    }
}