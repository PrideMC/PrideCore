<?php 

declare(strict_types=1);

namespace PrideCore\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use PrideCore\PrideCore;
use PrideCore\RPlayer;
use PrideCore\Utils\Interfaces\IMessages;
use PrideCore\Utils\InventoryManager;

class SpawnCommand extends Command implements IMessages
{
	public function __construct()
	{
		$this->plugin = PrideCore::getInstance();
		$this->inventory = new InventoryManager();
		parent::__construct("spawn", "Back to Lobby", "/spawn", ["lobby", "hub"]);
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if(!$sender instanceof RPlayer)
		{
			$sender->sendMessage(IMessages::NOT_PLAYER);
			return;
		}
		if($this->plugin->getServer()->getWorldManager()->getWorldByName("soup-ffa") === $sender->getWorld()){
			$world = $this->plugin->getServer()->getWorldManager()->getWorldByName("practice-lobby");
			$pos = $world->getSpawnLocation();
			$this->inventory->inv($sender, InventoryManager::PRACTICE_LOBBY);
			$sender->teleport($pos);
			$sender->setScoreTag("");
			return;
		}
		if($this->plugin->getServer()->getWorldManager()->getWorldByName("gapple-ffa") === $sender->getWorld()){
			$world = $this->plugin->getServer()->getWorldManager()->getWorldByName("practice-lobby");
			$pos = $world->getSpawnLocation();
			$this->inventory->inv($sender, InventoryManager::PRACTICE_LOBBY);
			$sender->teleport($pos);
			$sender->setScoreTag("");
			return;
		}
		if($this->plugin->getServer()->getWorldManager()->getWorldByName("build-ffa") === $sender->getWorld()){
			$world = $this->plugin->getServer()->getWorldManager()->getWorldByName("practice-lobby");
			$pos = $world->getSpawnLocation();
			$this->inventory->inv($sender, InventoryManager::PRACTICE_LOBBY);
			$sender->teleport($pos);
			$sender->setScoreTag("");
			return;
		}
		if($this->plugin->getServer()->getWorldManager()->getWorldByName("nodebuff-ffa") === $sender->getWorld()){
			$world = $this->plugin->getServer()->getWorldManager()->getWorldByName("practice-lobby");
			$pos = $world->getSpawnLocation();
			$this->inventory->inv($sender, InventoryManager::PRACTICE_LOBBY);
			$sender->teleport($pos);
			$sender->setScoreTag("");
			return;
		}
		if($this->plugin->getServer()->getWorldManager()->getWorldByName("sumo-ffa") === $sender->getWorld()){
			$world = $this->plugin->getServer()->getWorldManager()->getWorldByName("practice-lobby");
			$pos = $world->getSpawnLocation();
			$this->inventory->inv($sender, InventoryManager::PRACTICE_LOBBY);
			$sender->teleport($pos);
			$sender->setScoreTag("");
			return;
		}
		if($this->plugin->getServer()->getWorldManager()->getWorldByName("fist-ffa") === $sender->getWorld()){
			$world = $this->plugin->getServer()->getWorldManager()->getWorldByName("practice-lobby");
			$pos = $world->getSpawnLocation();
			$this->inventory->inv($sender, InventoryManager::PRACTICE_LOBBY);
			$sender->teleport($pos);
			$sender->setScoreTag("");
			return;
		}
		if(count($this->plugin->getServer()->getWorldManager()->getWorldByName("lobby-1")->getPlayers()) === 100){
			$world = $this->plugin->getServer()->getWorldManager()->getWorldByName("lobby-2");
			$pos = $world->getSpawnLocation();
			$this->inventory->inv($sender, InventoryManager::LOBBY);
			$sender->teleport($pos);
			$sender->setScoreTag("");
			return;
		}
		$world = $this->plugin->getServer()->getWorldManager()->getWorldByName("lobby-1");
		$pos = $world->getSpawnLocation();
		$this->inventory->inv($sender, InventoryManager::LOBBY);
		$sender->teleport($pos);
		$sender->setScoreTag("");
	}
}