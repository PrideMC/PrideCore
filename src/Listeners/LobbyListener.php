<?php

declare(strict_types=1);

namespace PrideCore\Listeners;

use pocketmine\utils\TextFormat as T;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use PrideCore\Utils\FormManager;
use PrideCore\Utils\InventoryManager;
use PrideCore\PrideCore;

class LobbyListener implements Listener {
	public function __construct()
	{
		$this->ui = new FormManager();
		$this->plugin = PrideCore::getInstance();
	}
	
	public function onUse(PlayerItemUseEvent $ev)
	{
		$item = $ev->getItem();
		$player = $ev->getPlayer();
		switch($item->getCustomName()){
			case InventoryManager::GAME_SELECTOR:
				$this->ui->sendGameManagerForm($player);
				break;
			case InventoryManager::LOBBY_SELECTOR:
				$this->ui->sendLobbiesMenu($player);
				break;
			case InventoryManager::COSMETICS_SELECTOR:
				$this->ui->sendCosmeticsForm($player);
				break;
			case InventoryManager::FFA:
				$this->ui->sendFFAForm($player);
				break;
			case InventoryManager::DUELS:
				$player->sendMessage(T::GRAY . "Coming Soon!");
				break;
			case InventoryManager::EVENTS:
				$player->sendMessage(T::GRAY . "Coming Soon!");
				break;
		}
	}
}