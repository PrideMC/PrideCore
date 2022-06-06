<?php

declare(strict_types=1);

namespace PrideCore\Utils;

use PrideCore\Utils\Forms\SimpleForm;
use PrideCore\Utils\Forms\CustomForm;
use PrideCore\Utils\Forms\ModalForm;
use PrideCore\PrideCore;
use pocketmine\utils\TextFormat as T;
use pocketmine\player\Player;
use PrideCore\Utils\InventoryManager;
use PrideCore\Utils\Utils;



class FormManager {
	
	public function __construct(){
		$this->plugin = PrideCore::getInstance();
		$this->inventory = new InventoryManager();
		$this->utils = new Utils();
	}
	
	public function sendGameManagerForm($player){
		$form = new SimpleForm(function(Player $player, $data){
			if($data === null) return;
			
			switch($data){
				case 0:
					$player->setImmobile(true);
					$location = $this->plugin->getServer()->getWorldManager()->getWorldByName("practice-lobby");
					$pos = $location->getSpawnLocation();
					$player->teleport($pos);
					$this->inventory->inv($player, InventoryManager::PRACTICE_LOBBY);
					$player->sendMessage(T::GREEN . "You have been transfered to ". T::YELLOW . "Practice Server" . T::GREEN . ".");
					$this->utils->playSound($player, "note.pling");
					$player->setImmobile(false);
					$player->setScoreTag("");
					break;
				case 1:
					$player->chat("/bw random");
					$this->utils->playSound($player, "note.pling");
					$player->setScoreTag("");
					break;
				case 2:
					$player->sendMessage(T::GRAY . "Coming Soon!");
					$this->utils->playSound($player, "note.bass");
					break;
				case 3:
					$player->sendMessage(T::GRAY . "Coming Soon!");
					$this->utils->playSound($player, "note.bass");
					break;
				case "offline":
					$player->sendMessage(T::RED . "This server went down. Check back later or view our server status at status.pridemc.ml and try to join again.");
					$this->utils->playSound($player, "note.bass");
					break;
				case "maintenance":
					$player->sendMessage(T::RED . "Please wait until the maintenance is done. Check back later and try to join again.");
					$this->utils->playSound($player, "note.bass");
					break;
			}
		});
		
		$form->setTitle(T::GRAY . T::BOLD . "- " . T::RESET . T::AQUA . "Game Selector" . T::GRAY . T::BOLD . " -");
		$form->setContent(T::RESET . T::GRAY . "Here you can join any games in one click!");
		if($this->plugin->getConfig()->get("practice") === "online"){
			$form->addButton(T::AQUA . "Practice Server\n". T::GREEN . "Online", 0, "textures/items/diamond_sword.png");
		} 
		if($this->plugin->getConfig()->get("practice") === "offline"){
			$form->addButton(T::AQUA . "Practice Server\n". T::RED . "Offline", 0, "textures/items/barrier.png", "offline");
		}
		if($this->plugin->getConfig()->get("practice") === "maintenance"){
			$form->addButton(T::AQUA . "Practice Server\n". T::YELLOW . "Maintenance", 0, "textures/items/barrier.png", "maintenance");
		}
		if($this->plugin->getConfig()->get("bedwars") === "online"){
			$form->addButton(T::AQUA . "BedWars Server\n". T::GREEN . "Online", 0, "textures/items/bed_red.png");
		} 
		if($this->plugin->getConfig()->get("bedwars") === "offline"){
			$form->addButton(T::AQUA . "BedWars Server\n". T::RED . "Offline", 0, "textures/items/barrier.png", "offline");
		}
		if($this->plugin->getConfig()->get("bedwars") === "maintenance"){
			$form->addButton(T::AQUA . "BedWars Server\n". T::YELLOW . "Maintenance", 0, "textures/items/barrier.png", "maintenance");
		}
		
		if($this->plugin->getConfig()->get("skywars") === "online"){
			$form->addButton(T::AQUA . "SkyWars Server\n". T::GREEN . "Online", 0, "textures/items/iron_chestplate.png");
		} 
		if($this->plugin->getConfig()->get("skywars") === "offline"){
			$form->addButton(T::AQUA . "SkyWars Server\n". T::RED . "Offline", 0, "textures/items/barrier.png", "offline");
		}
		if($this->plugin->getConfig()->get("skywars") === "maintenance"){
			$form->addButton(T::AQUA . "SkyWars Server\n". T::YELLOW . "Maintenance", 0, "textures/items/barrier.png", "maintenance");
		}
		$this->utils->playSound($player, "random.pop", 1.0, 1.0, 1.2);
		$player->sendForm($form);
	}
	
	public function sendFFAForm($player){
		$form = new SimpleForm(function(Player $player, $data){
			if($data === null) return;
			
			switch($data){
				case "soup":
					$this->inventory->inv($player, InventoryManager::SOUP_FFA);
					$location = $this->plugin->getServer()->getWorldManager()->getWorldByName("soup-ffa");
					$pos = $location->getSpawnLocation();
					$player->teleport($pos);
					$player->setImmobile(false);
					$this->utils->playSound($player, "note.pling");
					$player->setScoreTag("");
					break;
				case "gapple":
					$this->inventory->inv($player, InventoryManager::GAPPLE_FFA);
					$location = $this->plugin->getServer()->getWorldManager()->getWorldByName("gapple-ffa");
					$pos = $location->getSpawnLocation();
					$player->teleport($pos);
					$player->setImmobile(false);
					$this->utils->playSound($player, "note.pling");
					$player->setScoreTag("");
					break;
				case "build":
					$this->inventory->inv($player, InventoryManager::BUILD_FFA);
					$location = $this->plugin->getServer()->getWorldManager()->getWorldByName("build-ffa");
					$pos = $location->getSpawnLocation();
					$player->teleport($pos);
					$player->setImmobile(false);
					$player->setScoreTag("");
					$this->utils->playSound($player, "note.pling");
					break;
				case "nodebuff":
					$this->inventory->inv($player, InventoryManager::NODEBUFF_FFA);
					$location = $this->plugin->getServer()->getWorldManager()->getWorldByName("nodebuff-ffa");
					$pos = $location->getSpawnLocation();
					$player->teleport($pos);
					$player->setImmobile(false);
					$this->utils->playSound($player, "note.pling");
					$player->setScoreTag("");
					break;
				case "sumo":
					$this->inventory->inv($player, InventoryManager::SUMO_FFA);
					$location = $this->plugin->getServer()->getWorldManager()->getWorldByName("sumo-ffa");
					$pos = $location->getSpawnLocation();
					$player->teleport($pos);
					$player->setImmobile(false);
					$this->utils->playSound($player, "note.pling");
					$player->setScoreTag("");
					break;
				case "fist":
					$this->inventory->inv($player, InventoryManager::FIST_FFA);
					$this->utils->playSound($player, "note.pling");
					$location = $this->plugin->getServer()->getWorldManager()->getWorldByName("fist-ffa");
					$pos = $location->getSpawnLocation();
					$player->teleport($pos);
					$player->setImmobile(false);
					$player->setScoreTag("");
					break;
			}
		});
		
		$form->setTitle(T::GRAY . T::BOLD . "- " . T::RESET . T::AQUA . "FFA" . T::GRAY . T::BOLD . " -");
		$form->setContent(T::GRAY . "Here you can join available games.");
		$form->addButton(T::AQUA . "Soup FFA\n" . T::GREEN . "Click to Join!", 0, "textures/items/mushroom_stew.png", "soup");
		$form->addButton(T::AQUA . "Gapple FFA\n" . T::GREEN . "Click to Join!", 0, "textures/items/apple_golden.png", "gapple");
		$form->addButton(T::AQUA . "Build FFA\n" . T::GREEN . "Click to Join!", 0, "textures/items/bow_standby.png", "build");
		$form->addButton(T::AQUA . "Nodebuff FFA\n" . T::GREEN . "Click to Join!", 0, "textures/items/potion_bottle_splash_heal.png", "nodebuff");
		$form->addButton(T::AQUA . "Fist FFA\n" . T::GREEN . "Click to Join!", 0, "textures/items/beef_cooked.png", "fist");
		$form->addButton(T::AQUA . "Sumo FFA\n" . T::GREEN . "Click to Join!", 0, "textures/items/stick.png", "sumo");
		$this->utils->playSound($player, "random.pop", 1.0, 1.0, 1.2);
		$player->sendForm($form);
	}
	
	public function sendLobbiesMenu($player){
		$form = new SimpleForm(function(Player $player, $data){
			if($data === null) return;
			
			switch($data){
				case "full":
					$player->sendMessage(T::RED . "Sorry, this lobby is full. Check back later if theres an open space for you.");
					$this->utils->playSound($player, "note.bass");
					break;
				case 0:
					if($player->getWorld() === $this->plugin->getServer()->getWorldManager()->getWorldByName("lobby-1")){
						$player->sendMessage(T::RED . "You are already connected in this server.");
						$this->utils->playSound($player, "note.bass");
						return;
					}
					$player->setImmobile(true);
					$world = $this->plugin->getServer()->getWorldManager()->getWorldByName("lobby-1");
					$pos = $world->getSpawnLocation();
					$player->teleport($pos);
					$this->inventory->inv($player, InventoryManager::LOBBY);
					$player->setImmobile(false);
					$player->sendMessage(T::GREEN . "You have been transfered to " . T::AQUA . "Lobby #1");
					$player->setScoreTag("");
					break;
				case 1:
					if($player->getWorld() === $this->plugin->getServer()->getWorldManager()->getWorldByName("lobby-2")){
						$player->sendMessage(T::RED . "You are already connected in this server.");
						$this->utils->playSound($player, "note.bass");
						return;
					}
					$player->setImmobile(true);
					$world = $this->plugin->getServer()->getWorldManager()->getWorldByName("lobby-2");
					$pos = $world->getSpawnLocation();
					$player->teleport($pos);
					$this->inventory->inv($player, InventoryManager::LOBBY);
					$player->setImmobile(false);
					$player->sendMessage(T::GREEN . "You have been transfered to " . T::AQUA . "Lobby #2");
					$player->setScoreTag("");
					break;
			}
		});
		
		$form->setTitle(T::GRAY . T::BOLD . "- " . T::RESET . T::AQUA . "Lobby Selector" . T::GRAY . T::BOLD . " -");
		$form->setContent(T::GRAY . "Here you can change what lobby do you want to transfer them.");
		$world = $this->plugin->getServer()->getWorldManager();
		if(count($world->getWorldByName("lobby-1")->getPlayers()) === 100){
			$form->addButton(T::YELLOW . "Lobby #1\n" . T::RED . count($world->getWorldByName("lobby-1")->getPlayers()) . " / 100", 0, "", "full");
		} else {
			$form->addButton(T::YELLOW . "Lobby #1\n" . T::GREEN . count($world->getWorldByName("lobby-1")->getPlayers()) . " / 100", 0, "");
		}
		if(count($world->getWorldByName("lobby-2")->getPlayers()) === 100){
			$form->addButton(T::YELLOW . "Lobby #2\n" . T::RED . count($world->getWorldByName("lobby-2")->getPlayers()) . " / 100", 0, "", "full");
		} else {
			$form->addButton(T::YELLOW . "Lobby #2\n" . T::GREEN . count($world->getWorldByName("lobby-2")->getPlayers()) . " / 100", 0, "");
		}
		$this->utils->playSound($player, "random.pop", 1.0, 1.0, 1.2);
		$player->sendForm($form);
	}
	
	public function sendCosmeticsForm($player){
		$form = new SimpleForm(function(Player $player, $data){
			if($data === null) return;	
			
			switch($data){
				case "tags":
					$this->sendTagsForm($player);
					break;
			}
		});
		
		$form->setTitle(T::GRAY . T::BOLD . "- " . T::RESET . T::AQUA . "Cosmetics" . T::GRAY . T::BOLD . " -");
		$form->setContent(T::GRAY . "Here you can change what cosmectics do you want to apply.");
		$form->addButton(T::YELLOW . "Lobby Tags\n" . T::RESET . T::AQUA . $this->utils->getOwnedTags($player) . " Owned", 0, "textures/items/name_tag.png", "tags");
		$this->utils->playSound($player, "random.pop", 1.0, 1.0, 1.2);
		$player->sendForm($form);
	}
	
	
	public function sendTagsForm($player){
		$form = new SimpleForm(function(Player $player, $data){
			if($data === null) return;	
			
			switch($data){
				case "notowned":
					$player->sendMessage(T::RED . "This item is locked. Purchase it on shop.pridemc.ml!");
					$this->utils->playSound($player, "note.bass");
					break;
				case "cool":
					$format = T::YELLOW . "Cool" . T::RESET;
					$player->setScoreTag($format);
					$player->sendMessage(T::GREEN . "You have been selected " . $format . T::GREEN . " as your tag!");
					$this->utils->playSound($player, "note.pling");
					break;
				case "prideday":
					$format = T::GOLD . "Happy PrideDay!" . T::RESET;
					$player->setScoreTag($format);
					$player->sendMessage(T::GREEN . "You have been selected " . $format . T::GREEN . " as your tag!");
					$this->utils->playSound($player, "note.pling");
					break;
				case "wtf":
					$format = T::LIGHT_PURPLE . "WTF?!" . T::RESET;
					$player->setScoreTag($format);
					$player->sendMessage(T::GREEN . "You have been selected " . $format . T::GREEN . " as your tag!");
					$this->utils->playSound($player, "note.pling");
					break;
				case "wonderful":
					$format = T::AQUA . "Wonderful Day!" . T::RESET;
					$player->setScoreTag($format);
					$player->sendMessage(T::GREEN . "You have been selected " . $format . T::GREEN . " as your tag!");
					$this->utils->playSound($player, "note.pling");
					break;
				case "wonderful":
					$format = T::AQUA . "Wonderful Day!" . T::RESET;
					$player->setScoreTag($format);
					$player->sendMessage(T::GREEN . "You have been selected " . $format . T::GREEN . " as your tag!");
					$this->utils->playSound($player, "note.pling");
					break;
				case "rainbowlife":
					$format = "§fR§aa§bi§cn§db§eo§gw §1L§2i§3f§4e §f<3" . T::RESET;
					$player->setScoreTag($format);
					$player->sendMessage(T::GREEN . "You have been selected " . $format . T::GREEN . " as your tag!");
					$this->utils->playSound($player, "note.pling");
					break;
				case "hmm":
					$format = T::GOLD . "<:@3" . T::RESET;
					$player->setScoreTag($format);
					$player->sendMessage(T::GREEN . "You have been selected " . $format . T::GREEN . " as your tag!");
					$this->utils->playSound($player, "note.pling");
					break;
				case "sh":
					$format = T::GREEN . "Shhshshssh" . T::RESET;
					$player->setScoreTag($format);
					$player->sendMessage(T::GREEN . "You have been selected " . $format . T::GREEN . " as your tag!");
					$this->utils->playSound($player, "note.pling");
					break;
				case "lmao":
					$format = T::WHITE . "Lmao" . T::RESET;
					$player->setScoreTag($format);
					$player->sendMessage(T::GREEN . "You have been selected " . $format . T::GREEN . " as your tag!");
					$this->utils->playSound($player, "note.pling");
					break;
				case "maybe":
					$format = T::WHITE . "Maybe?" . T::RESET;
					$player->setScoreTag($format);
					$player->sendMessage(T::GREEN . "You have been selected " . $format . T::GREEN . " as your tag!");
					$this->utils->playSound($player, "note.pling");
					break;
				case "amazing":
					$format = T::YELLOW . "Amazing Day!" . T::RESET;
					$player->setScoreTag($format);
					$player->sendMessage(T::GREEN . "You have been selected " . $format . T::GREEN . " as your tag!");
					$this->utils->playSound($player, "note.pling");
					break;
			}
		});
		
		$form->setTitle(T::GRAY . T::BOLD . "- " . T::RESET . T::AQUA . "Cosmetics" . T::WHITE . " > " . T::AQUA . "Tags" . T::GRAY . T::BOLD . " -");
		$form->setContent(T::GRAY . "Here you can change what tags do you want to apply.");
		if($player->hasPermission("cosmetics.tag.1")){
			$form->addButton(T::YELLOW . "Cool" . "\n" . T::RESET . T::GREEN . "Purchased", 0, "textures/ui/arrow_right.png", "cool");
		} else {
			$form->addButton(T::YELLOW . "Cool". "\n" . T::RESET . T::RED . "Purchase", 0, "textures/ui/arrow_right.png", "notowned");
		}
		if($player->hasPermission("cosmetics.tag.2")){
			$form->addButton(T::GOLD . "Happy PrideDay!" . "\n" . T::RESET . T::GREEN . "Purchased", 0, "textures/ui/arrow_right.png", "prideday");
		} else {
			$form->addButton(T::GOLD . "Happy PrideDay!". "\n" . T::RESET . T::RED . "Purchase", 0, "textures/ui/arrow_right.png", "notowned");
		}
		if($player->hasPermission("cosmetics.tag.3")){
			$form->addButton(T::LIGHT_PURPLE . "WTF?!" . "\n" . T::RESET . T::GREEN . "Purchased", 0, "textures/ui/arrow_right.png", "wtf");
		} else {
			$form->addButton(T::LIGHT_PURPLE . "WTF?!". "\n" . T::RESET . T::RED . "Purchase", 0, "textures/ui/arrow_right.png", "notowned");
		}
		if($player->hasPermission("cosmetics.tag.4")){
			$form->addButton(T::AQUA . "Wonderful Day!" . "\n" . T::RESET . T::GREEN . "Purchased", 0, "textures/ui/arrow_right.png", "wonderful");
		} else {
			$form->addButton(T::AQUA . "Wonderful Day!". "\n" . T::RESET . T::RED . "Purchase", 0, "textures/ui/arrow_right.png", "notowned");
		}
		if($player->hasPermission("cosmetics.tag.5")){
			$form->addButton("§fR§aa§bi§cn§db§eo§gw §1L§2i§3f§4e §f<3" . "\n" . T::RESET . T::GREEN . "Purchased", 0, "textures/ui/arrow_right.png", "rainbowlife");
		} else {
			$form->addButton("§fR§aa§bi§cn§db§eo§gw §1L§2i§3f§4e §f<3" . "\n" . T::RESET . T::RED . "Purchase", 0, "textures/ui/arrow_right.png", "notowned");
		}
		if($player->hasPermission("cosmetics.tag.6")){
			$form->addButton(T::GOLD . "<:@3" . "\n" . T::RESET . T::GREEN . "Purchased", 0, "textures/ui/arrow_right.png", "hmm");
		} else {
			$form->addButton(T::GOLD . "<:@3" . "\n" . T::RESET . T::RED . "Purchase", 0, "textures/ui/arrow_right.png", "notowned");
		}
		if($player->hasPermission("cosmetics.tag.7")){
			$form->addButton(T::GREEN . "Shhshshssh" . "\n" . T::RESET . T::GREEN . "Purchased", 0, "textures/ui/arrow_right.png", "sh");
		} else {
			$form->addButton(T::GREEN . "Shhshshssh" . "\n" . T::RESET . T::RED . "Purchase", 0, "textures/ui/arrow_right.png", "notowned");
		}
		if($player->hasPermission("cosmetics.tag.8")){
			$form->addButton(T::WHITE . "Lmao" . "\n" . T::RESET . T::GREEN . "Purchased", 0, "textures/ui/arrow_right.png", "lmao");
		} else {
			$form->addButton(T::WHITE . "Shhshshssh" . "\n" . T::RESET . T::RED . "Purchase", 0, "textures/ui/arrow_right.png", "notowned");
		}
		if($player->hasPermission("cosmetics.tag.9")){
			$form->addButton(T::WHITE . "Maybe?" . "\n" . T::RESET . T::GREEN . "Purchased", 0, "textures/ui/arrow_right.png", "maybe");
		} else {
			$form->addButton(T::WHITE . "Maybe?" . "\n" . T::RESET . T::RED . "Purchase", 0, "textures/ui/arrow_right.png", "notowned");
		}
		if($player->hasPermission("cosmetics.tag.10")){
			$form->addButton(T::YELLOW . "Amazing Day!" . "\n" . T::RESET . T::GREEN . "Purchased", 0, "textures/ui/arrow_right.png", "amazing");
		} else {
			$form->addButton(T::YELLOW . "Amazing Day!" . "\n" . T::RESET . T::RED . "Purchase", 0, "textures/ui/arrow_right.png", "notowned");
		}
		$this->utils->playSound($player, "random.pop", 1.0, 1.0, 1.2);
		$player->sendForm($form);
	}
	
	public function sendSkinsForm($player){
		
	}
}