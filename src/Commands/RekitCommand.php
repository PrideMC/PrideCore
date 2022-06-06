<?php

namespace PrideCore\Commands;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat as T;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use PrideCore\PrideCore;
use PrideCore\Utils\InventoryManager;
use PrideCore\RPlayer;
use PrideCore\Utils\Interfaces\IMessages;

class RekitCommand extends Command {
	
	public function __construct(){
		$this->plugin = PrideCore::getInstance();
		$this->inv = new InventoryManager();
		parent::__construct("rekit", "Rekit Inventory", "/rekit");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args) {
		if(!$sender instanceof Player){
			$sender->sendMessage(IMessages::NOT_PLAYER);
			return;
		}
		if($sender->getWorld() === $this->plugin->getServer()->getWorldManager()->getWorldByName("gapple-ffa")){
			$this->inv->inv($sender, InventoryManager::GAPPLE_FFA);
			return;
		}
		if($sender->getWorld() === $this->plugin->getServer()->getWorldManager()->getWorldByName("nodebuff-ffa")){
			$this->inv->inv($sender, InventoryManager::NODEBUFF_FFA);
			return;
		}
		if($sender->getWorld() === $this->plugin->getServer()->getWorldManager()->getWorldByName("sumo-ffa")){
			$this->inv->inv($sender, InventoryManager::SUMO_FFA);
			return;
		}
		if($sender->getWorld() === $this->plugin->getServer()->getWorldManager()->getWorldByName("build-ffa")){
			$this->inv->inv($sender, InventoryManager::BUILD_FFA);
			return;
		}
		if($sender->getWorld() === $this->plugin->getServer()->getWorldManager()->getWorldByName("soup-ffa")){
			$this->inv->inv($sender, InventoryManager::SOUP_FFA);
			return;
		}
		if($sender->getWorld() === $this->plugin->getServer()->getWorldManager()->getWorldByName("practice-lobby")){
			$sender->sendMessage(T::RED . "You can only use this at combat.");
			return;
		}
		if($sender->getWorld() === $this->plugin->getServer()->getWorldManager()->getWorldByName("lobby-1")){
			$sender->sendMessage(T::RED . "You dont have permission to use this command.");
			return;
		}
		if($sender->getWorld() === $this->plugin->getServer()->getWorldManager()->getWorldByName("lobby-2")){
			$sender->sendMessage(T::RED . "You dont have permission to use this command.");
			return;
		}
		if($sender->getWorld() === $this->plugin->getServer()->getWorldManager()->getWorldByName("fist-ffa")){
			$this->inv->inv($sender, InventoryManager::FIST_FFA);
			return;
		}
		$sender->sendMessage(IMessages::REKIT_ERROR);
	}
}