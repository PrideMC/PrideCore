<?php

declare(strict_types=1);

namespace PrideCore\Commands\Managements;

use pocketmine\utils\TextFormat as T;
use PrideCore\PrideCore;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use PrideCore\RPlayer;
use PrideCore\Utils\Interfaces\IMessages;
use PrideCore\Utils\Interfaces\IPermissions;

class MaintenanceCommand extends Command implements IMessages, IPermissions 
{
	
	public function __construct()
	{
		$this->plugin = PrideCore::getInstance();
		parent::__construct("maintenance", "Managements - Maintenance a Game", "/maintenance <gamemode> <status>", ["mm"]);
		$this->setPermission(IPermissions::MAINTENANCE);
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args) 
	{
		if(!$sender instanceof RPlayer)
		{
			$sender->sendMessage(IMessages::NOT_PLAYER);
			return;
		}
		if(!$sender->hasPermission(IPermissions::MAINTENANCE)){
			$sender->sendMessage(IMessages::NO_PERMISSION);
			return;
		}
		if(!isset($args[0])){
			$sender->sendMessage(vsprintf(IMessages::INVALID_ARGS, [$this->usageMessage]));
			return;
		}
		
		switch(strtolower($args[0])){
			case "practice":
				if($args[1] === "maintenance"){
					$this->plugin->getConfig()->set("practice", "maintenance");
					foreach($this->plugin->getServer()->getWorldManager()->getWorldByName("practice-lobby")->getPlayers() as $player){
						$player->chat("/spawn");
					}
					foreach($this->plugin->getServer()->getWorldManager()->getWorldByName("soup-ffa")->getPlayers() as $player){
						$player->chat("/spawn");
					}
					foreach($this->plugin->getServer()->getWorldManager()->getWorldByName("build-ffa")->getPlayers() as $player){
						$player->chat("/spawn");
					}
					foreach($this->plugin->getServer()->getWorldManager()->getWorldByName("nodebuff-ffa")->getPlayers() as $player){
						$player->chat("/spawn");
					}
					foreach($this->plugin->getServer()->getWorldManager()->getWorldByName("gapple-ffa")->getPlayers() as $player){
						$player->chat("/spawn");
					}
					foreach($this->plugin->getServer()->getWorldManager()->getWorldByName("sumo-ffa")->getPlayers() as $player){
						$player->chat("/spawn");
					}
					foreach($this->plugin->getServer()->getWorldManager()->getWorldByName("fist-ffa")->getPlayers() as $player){
						$player->chat("/spawn");
					}
					$sender->sendMessage(T::GREEN . "You have been updated the gamemode status to maintenance.");
					$this->plugin->getConfig()->save();
					return;
				}
				if($args[1] === "online"){
					$this->plugin->getConfig()->set("practice", "online");
					$sender->sendMessage(T::GREEN . "You have been updated the gamemode status to online.");
					$this->plugin->getConfig()->save();
					return;
				}
				if($args[1] === "offline"){
					$this->plugin->getConfig()->set("practice", "offline");
					foreach($this->plugin->getServer()->getWorldManager()->getWorldByName("practice-lobby")->getPlayers() as $player){
						$player->chat("/spawn");
					}
					foreach($this->plugin->getServer()->getWorldManager()->getWorldByName("soup-ffa")->getPlayers() as $player){
						$player->chat("/spawn");
					}
					$sender->sendMessage(T::GREEN . "You have been updated the gamemode status to offline.");
					$this->plugin->getConfig()->save();
					return;
				}
				$sender->sendMessage(T::RED . "Theres any 3 option you can modify. (maintenance, offline, online)");
				break;
			case "bedwars":
				if($args[1] === "maintenance"){
					$this->plugin->getConfig()->set("bedwars", "maintenance");
					$sender->sendMessage(T::GREEN . "You have been updated the gamemode status to maintenance.");
					$this->plugin->getConfig()->save();
					return;
				}
				if($args[1] === "online"){
					$this->plugin->getConfig()->set("bedwars", "online");
					$sender->sendMessage(T::GREEN . "You have been updated the gamemode status to online.");
					$this->plugin->getConfig()->save();
					return;
				}
				if($args[1] === "offline"){
					$this->plugin->getConfig()->set("bedwars", "offline");
					$sender->sendMessage(T::GREEN . "You have been updated the gamemode status to offline.");
					$this->plugin->getConfig()->save();
					return;
				}
				$sender->sendMessage(T::RED . "Theres any 3 option you can modify. (maintenance, offline, online)");
				break;
			case "skywars":
				if($args[1] === "maintenance"){
					$this->plugin->getConfig()->set("skywars", "maintenance");
					$sender->sendMessage(T::GREEN . "You have been updated the gamemode status to maintenance.");
					$this->plugin->getConfig()->save();
					return;
				}
				if($args[1] === "online"){
					$this->plugin->getConfig()->set("skywars", "online");
					$sender->sendMessage(T::GREEN . "You have been updated the gamemode status to online.");
					$this->plugin->getConfig()->save();
					return;
				}
				if($args[1] === "offline"){
					$this->plugin->getConfig()->set("skywars", "offline");
					$sender->sendMessage(T::GREEN . "You have been updated the gamemode status to offline.");
					$this->plugin->getConfig()->save();
					return;
				}
				$sender->sendMessage(T::RED . "Theres any 3 option you can modify. (maintenance, offline, online)");
				break;
			default:
				$sender->sendMessage(T::RED . "Unable to find that gamemode. Please check if that gamemode exist.");
				break;
		}
	}
}