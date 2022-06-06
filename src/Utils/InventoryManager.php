<?php

declare(strict_types=1);

namespace PrideCore\Utils;

use PrideCore\RPlayer;
use pocketmine\inventory\Inventory;
use pocketmine\utils\TextFormat as T;
use PrideCore\PrideCore;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\data\bedrock\EffectIdMap;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;

/*
 * A class that manages players inventory.
 */
class InventoryManager {
	
	public function __construct() {
		$this->plugin = PrideCore::getInstance();
	}
	
	
	public const GAME_SELECTOR = T::RESET . T::AQUA . "Game Selector " . T::GRAY . "[" . T::GREEN . "Use" . T::GRAY . "]";
	public const LOBBY_SELECTOR = T::RESET . T::AQUA . "Lobby Selector " . T::GRAY . "[" . T::GREEN . "Use" . T::GRAY . "]";
	public const COSMETICS_SELECTOR = T::RESET . T::AQUA . "Cosmetics " . T::GRAY . "[" . T::GREEN . "Use" . T::GRAY . "]";
	public const FFA = T::RESET . T::AQUA . "Free for All " . T::GRAY . "[" . T::GREEN . "Use" . T::GRAY . "]";
	public const DUELS = T::RESET . T::AQUA . "Duels " . T::GRAY . "[" . T::GREEN . "Use" . T::GRAY . "]";
	public const EVENTS = T::RESET . T::AQUA . "Events " . T::GRAY . "[" . T::GREEN . "Use" . T::GRAY . "]";
	public const SOUP_ITEM = T::RESET . T::AQUA . "Soup " . T::GRAY . "[" . T::GREEN . "Use" . T::GRAY . "]";
	public const APPLE_ITEM = T::RESET . T::AQUA . "Golden Apple " . T::GRAY . "[" . T::GREEN . "Eat" . T::GRAY . "]";
	public const POTION_ITEM = T::RESET . T::AQUA . "Health Potion " . T::GRAY . "[" . T::GREEN . "Use" . T::GRAY . "]";
	public const ENDER_ITEM = T::RESET . T::AQUA . "Ender Pearl " . T::GRAY . "[" . T::GREEN . "Use" . T::GRAY . "]";
	
	public const LOBBY = 0;
	public const PRACTICE_LOBBY = 1;
	public const SOUP_FFA = 2;
	public const GAPPLE_FFA = 3;
	public const BUILD_FFA = 4;
	public const NODEBUFF_FFA = 5;
	public const SUMO_FFA = 6;
	public const FIST_FFA = 7;
	
	public function inv(RPlayer $player, int $id) :void {
		switch($id){
			case self::LOBBY:
				//TODO: Add custom hub item.
				$player->getInventory()->clearAll();
				$player->getArmorInventory()->clearAll();
				$player->getEffects()->clear();
				$player->getInventory()->setItem(0, ItemFactory::getInstance()->get(ItemIds::COMPASS)->setCustomName(self::GAME_SELECTOR));
				$player->getInventory()->setItem(8, ItemFactory::getInstance()->get(ItemIds::CLOCK)->setCustomName(self::LOBBY_SELECTOR));
				$player->getInventory()->setItem(7, ItemFactory::getInstance()->get(ItemIds::CHEST)->setCustomName(self::COSMETICS_SELECTOR));
				break;
			case self::PRACTICE_LOBBY:
				$player->getInventory()->clearAll();
				$player->getArmorInventory()->clearAll();
				$player->getEffects()->clear();
				$player->getInventory()->setItem(0, ItemFactory::getInstance()->get(ItemIds::STONE_SWORD)->setCustomName(self::FFA));
				$player->getInventory()->setItem(1, ItemFactory::getInstance()->get(ItemIds::IRON_SWORD)->setCustomName(self::DUELS));
				$player->getInventory()->setItem(2, ItemFactory::getInstance()->get(ItemIds::DIAMOND_SWORD)->setCustomName(self::EVENTS));
				$player->getInventory()->setItem(8, ItemFactory::getInstance()->get(ItemIds::CHEST)->setCustomName(self::COSMETICS_SELECTOR));
				break;
			case self::SOUP_FFA:
				$player->getInventory()->clearAll();
				$player->getArmorInventory()->clearAll();
				$player->getEffects()->clear();
				$player->setNameTag(T::RED . $player->getName());
				$player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 214748364, 0, false));
				$player->getInventory()->setItem(0, ItemFactory::getInstance()->get(ItemIds::DIAMOND_SWORD));
				$helmet  = VanillaItems::DIAMOND_HELMET();
				$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
				$player->getArmorInventory()->setHelmet($helmet);
				$chestplate  = VanillaItems::DIAMOND_CHESTPLATE();
				$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FIRE_PROTECTION(), 1));
				$player->getArmorInventory()->setChestplate($chestplate);
				$leggings  = VanillaItems::DIAMOND_LEGGINGS();
				$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROJECTILE_PROTECTION(), 1));
				$player->getArmorInventory()->setLeggings($leggings);
				$boots  = VanillaItems::DIAMOND_BOOTS();
				$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::BLAST_PROTECTION(), 1));
				$player->getArmorInventory()->setBoots($boots);
				$player->getInventory()->addItem(ItemFactory::getInstance()->get(ItemIds::MUSHROOM_STEW, 0, 35)->setCustomName(self::SOUP_ITEM));
				$player->setImmobile(true);
				break;
			case self::GAPPLE_FFA:
				$player->getInventory()->clearAll();
				$player->getArmorInventory()->clearAll();
				$player->getEffects()->clear();
				$player->setNameTag(T::RED . $player->getName());
				$player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 214748364, 0, false));
				$sword = VanillaItems::IRON_SWORD();
				$sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 2));
				$player->getInventory()->setItem(0, $sword);
				$helmet  = VanillaItems::IRON_HELMET();
				$player->getArmorInventory()->setHelmet($helmet);
				$chestplate  = VanillaItems::IRON_CHESTPLATE();
				$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 1));
				$player->getArmorInventory()->setChestplate($chestplate);
				$leggings  = VanillaItems::IRON_LEGGINGS();
				$player->getArmorInventory()->setLeggings($leggings);
				$boots  = VanillaItems::IRON_BOOTS();
				$player->getArmorInventory()->setBoots($boots);
				$player->getInventory()->addItem(ItemFactory::getInstance()->get(ItemIds::GOLDEN_APPLE, 0, 64)->setCustomName(self::APPLE_ITEM));
				$player->setImmobile(true);
				break;
			case self::BUILD_FFA:
				$player->getInventory()->clearAll();
				$player->getArmorInventory()->clearAll();
				$player->getEffects()->clear();
				$player->setNameTag(T::RED . $player->getName());
				$sword = VanillaItems::IRON_SWORD();
				$player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 214748364, 0, false));
				$sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 2));
				$player->getInventory()->setItem(0, $sword);
				$player->getInventory()->addItem(ItemFactory::getInstance()->get(ItemIds::SANDSTONE, 1, 64));
				$player->getInventory()->addItem(ItemFactory::getInstance()->get(ItemIds::ENDER_PEARL, 0, 16));
				$player->getInventory()->addItem(ItemFactory::getInstance()->get(ItemIds::SANDSTONE, 0, 64));
				$player->getInventory()->addItem(ItemFactory::getInstance()->get(ItemIds::IRON_PICKAXE, 0, 1));
				$player->getInventory()->addItem(ItemFactory::getInstance()->get(ItemIds::SANDSTONE, 0, 64));
				$helmet  = VanillaItems::IRON_HELMET();
				$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 1));
				$player->getArmorInventory()->setHelmet($helmet);
				$chestplate  = VanillaItems::IRON_CHESTPLATE();
				$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 1));
				$player->getArmorInventory()->setChestplate($chestplate);
				$leggings  = VanillaItems::IRON_LEGGINGS();
				$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 1));
				$player->getArmorInventory()->setLeggings($leggings);
				$boots  = VanillaItems::IRON_BOOTS();
				$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 1));
				$player->getArmorInventory()->setBoots($boots);
				break;
			case self::NODEBUFF_FFA:
				$player->getInventory()->clearAll();
				$player->getArmorInventory()->clearAll();
				$player->getEffects()->clear();
				$player->setNameTag(T::RED . $player->getName());
				$player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 214748364, 0, false));
				$player->getInventory()->setItem(0, ItemFactory::getInstance()->get(ItemIds::DIAMOND_SWORD));
				$player->getInventory()->addItem(ItemFactory::getInstance()->get(ItemIds::ENDER_PEARL, 0, 16)->setCustomName(self::ENDER_ITEM));
				$helmet  = VanillaItems::DIAMOND_HELMET();
				$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
				$player->getArmorInventory()->setHelmet($helmet);
				$chestplate  = VanillaItems::DIAMOND_CHESTPLATE();
				$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FIRE_PROTECTION(), 1));
				$player->getArmorInventory()->setChestplate($chestplate);
				$leggings  = VanillaItems::DIAMOND_LEGGINGS();
				$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROJECTILE_PROTECTION(), 1));
				$player->getArmorInventory()->setLeggings($leggings);
				$boots  = VanillaItems::DIAMOND_BOOTS();
				$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::BLAST_PROTECTION(), 1));
				$player->getArmorInventory()->setBoots($boots);
				$player->getInventory()->addItem(ItemFactory::getInstance()->get(ItemIds::SPLASH_POTION, 21, 34)->setCustomName(self::POTION_ITEM));
				$player->setImmobile(true);
				break;
			case self::SUMO_FFA:
				$player->getInventory()->clearAll();
				$player->getArmorInventory()->clearAll();
				$player->getEffects()->clear();
				$player->setNameTag(T::RED . $player->getName());
				$player->getEffects()->add(new EffectInstance(EffectIdMap::getInstance()->fromId(1), 214748364, 0, false));
				$player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 214748364, 0, false));
				$player->getInventory()->setItem(0, ItemFactory::getInstance()->get(ItemIds::STICK));
				break;
			case self::FIST_FFA:
				$player->getInventory()->clearAll();
				$player->getArmorInventory()->clearAll();
				$player->getEffects()->clear();
				$player->setNameTag(T::RED . $player->getName());
				$player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 214748364, 0, false));
				$player->getInventory()->setItem(0, ItemFactory::getInstance()->get(ItemIds::COOKED_BEEF, 0, 64));
				break;
			default:
				$player->sendMessage(T::RED . "This gamemode is unavailable. Please join in other game.");
				break;
		}
	}
}