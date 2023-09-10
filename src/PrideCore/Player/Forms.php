<?php

/*
 *
 *       _____      _     _      __  __  _____
 *      |  __ \    (_)   | |    |  \/  |/ ____|
 *      | |__) | __ _  __| | ___| \  / | |
 *      |  ___/ '__| |/ _` |/ _ \ |\/| | |
 *      | |   | |  | | (_| |  __/ |  | | |____
 *      |_|   |_|  |_|\__,_|\___|_|  |_|\_____|
 *            A minecraft bedrock server.
 *
 *      This project and it’s contents within
 *     are copyrighted and trademarked property
 *   of PrideMC Network. No part of this project or
 *    artwork may be reproduced by any means or in
 *   any form whatsoever without written permission.
 *
 *  Copyright © PrideMC Network - All Rights Reserved
 *
 *  www.mcpride.tk                 github.com/PrideMC
 *  twitter.com/PrideMC         youtube.com/c/PrideMC
 *  discord.gg/PrideMC           facebook.com/PrideMC
 *               bit.ly/JoinInPrideMC
 *  #StandWithUkraine                     #PrideMonth
 *
 */

declare(strict_types=1);

namespace PrideCore\Player;

use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\ModalForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat as TF;
use PrideCore\Core;
use PrideCore\Utils\Rank;
use function array_values;

/**
 * Form UI
 */
class Forms
{

	use SingletonTrait;

	/**
	 * @param mixed $player
	 */
	public static function viewRanks($player) : void
	{
		$rank = Rank::getInstance();
		$form = new SimpleForm(function (Player $player, $data) use ($rank) {
			if ($data === null) {
				return;
			}
			switch($data) {
				case 0:
					$player->sendMessage(Core::ARROW . " " . $rank->displayTag(Rank::PLUS));
					$player->sendMessage(Core::ARROW . " - " . TF::GREEN . "1 Sessional Capes.");
					$player->sendMessage(Core::ARROW . " - " . TF::GREEN . "2 Sessional Pets.");
					$player->sendMessage(Core::ARROW . " - " . TF::GREEN . "3 Mystery Crates.");
					$player->sendMessage(Core::ARROW . " - " . TF::GREEN . "12 Premium Killphrase");
					$player->sendMessage(Core::ARROW . " - " . TF::GREEN . "2x Level Boost");
					break;
				case 1:
					$player->sendMessage(Core::ARROW . " " . $rank->displayTag(Rank::VIP));
					$player->sendMessage(Core::ARROW . " - " . TF::GOLD . "3 Sessional Capes.");
					$player->sendMessage(Core::ARROW . " - " . TF::GOLD . "5 Sessional Pets.");
					$player->sendMessage(Core::ARROW . " - " . TF::GOLD . "10 Mystery Crates.");
					$player->sendMessage(Core::ARROW . " - " . TF::GOLD . "15 Premium Killphrase");
					$player->sendMessage(Core::ARROW . " - " . TF::GOLD . "3x Level Boost");
					$player->sendMessage(Core::ARROW . " - " . TF::GOLD . "Fly on the lobbies.");
					$player->sendMessage(Core::ARROW . " - " . TF::GOLD . "5 Cosmetic Particles.");
					break;
				case 2:
					$player->sendMessage(Core::ARROW . " " . $rank->displayTag(Rank::MVP));
					$player->sendMessage(Core::ARROW . " - " . TF::RED . "5 Sessional Capes.");
					$player->sendMessage(Core::ARROW . " - " . TF::RED . "10 Sessional Pets.");
					$player->sendMessage(Core::ARROW . " - " . TF::RED . "15 Mystery Crates.");
					$player->sendMessage(Core::ARROW . " - " . TF::RED . "17 Premium Killphrase");
					$player->sendMessage(Core::ARROW . " - " . TF::RED . "4x Level Boost");
					$player->sendMessage(Core::ARROW . " - " . TF::RED . "Fly on the lobbies.");
					$player->sendMessage(Core::ARROW . " - " . TF::RED . "10 Cosmetic Particles.");
					$player->sendMessage(Core::ARROW . " - " . TF::RED . "5 Cosmetic Costumes.");
					break;
			}
		});

		$form->setTitle(Core::PREFIX . " " . Core::ARROW . TF::GREEN . " Buy Ranks");
		$form->setContent(TF::YELLOW . "Session v4" . TF::WHITE . " | " . Core::PREFIX . "\n\n" . $rank->displayTag(Rank::PLUS) . TF::WHITE . TF::BOLD . "- " . TF::RESET . TF::RED . TF::STRIKETHROUGH . "$5.99 " . TF::RESET . TF::GREEN . "$2.99 " . TF::YELLOW . "(79% off)" . "\n" . $rank->displayTag(Rank::VIP) . TF::WHITE . TF::BOLD . "- " . TF::RESET . TF::RED . TF::STRIKETHROUGH . "$10.99 " . TF::RESET . TF::GREEN . " $6.99 " . TF::YELLOW . "(49% off)" . "\n" . $rank->displayTag(Rank::MVP) . TF::WHITE . TF::BOLD . "- " . TF::RESET . TF::RED . TF::STRIKETHROUGH . "$15.99 " . TF::RESET . TF::GREEN . "$9.99 " . TF::YELLOW . "(49% off)" . "\n\n" . $rank->displayTag(Rank::PRIDE) . TF::WHITE . TF::BOLD . "- " . TF::RESET . TF::RED . TF::STRIKETHROUGH . "$20.99 " . TF::RESET . TF::GREEN . "$10.99 " . TF::YELLOW . "(30% off)");
		$form->addButton($rank->displayTag(Rank::PLUS));
		$form->addButton($rank->displayTag(Rank::VIP));
		$form->addButton($rank->displayTag(Rank::MVP));
		$form->addButton($rank->displayTag(Rank::PRIDE));
		$player->sendForm($form);
	}

	/**
	 * @param mixed $player
	 */
	public static function manageRanks($player) : void
	{
		$rank = Rank::getInstance();
		$form = new CustomForm(function (Player $player, $data) use ($rank) {
			if ($data === null) {
				return;
			}
			if (($target = Core::getInstance()->getServer()->getPlayerExact($data[1])) === null) {
				$player->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "Sorry, we couldnt find player: " . $data[1]);
			} else {
				Core::getInstance()->getRanks()->setRank($target, $data[2]);
				$player->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "Successfully changed rank of " . $data[1] . " to " . $rank->displayTag($data[2]) . TF::RESET . TF::GREEN . " Rank.");
			}
		});

		$form->setTitle(Core::PREFIX . " " . Core::ARROW . TF::GREEN . "Manage Ranks");
		$form->addLabel(TF::AQUA . "Here, you can customize server ranks from the player.");
		$form->addInput("", "Username");
		$form->addDropdown("", [
			$rank->displayTag(Rank::PLAYER),
			$rank->displayTag(Rank::BOOSTER),
			$rank->displayTag(Rank::VOTER),
			$rank->displayTag(Rank::DISCORD),
			$rank->displayTag(Rank::MEDIA),
			$rank->displayTag(Rank::MVP),
			$rank->displayTag(Rank::VIP),
			$rank->displayTag(Rank::PLUS),
			$rank->displayTag(Rank::PRIDE),
			$rank->displayTag(Rank::TRIAL),
			$rank->displayTag(Rank::HELPER),
			$rank->displayTag(Rank::BUILDER),
			$rank->displayTag(Rank::MODERATOR),
			$rank->displayTag(Rank::STAFF),
			$rank->displayTag(Rank::TEAM),
			$rank->displayTag(Rank::ADMIN),
			$rank->displayTag(Rank::OWNER)
		]);
		$player->sendForm($form);
	}

	/**
	 * @param mixed $player
	 */
	public static function viewProfile($player) : void
	{
		$rank = Rank::getInstance();
		$form = new SimpleForm(function (Player $player, $data) use ($rank) {
			if ($data === null) {
				return;
			}
		});

		$form->setTitle(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . $player->getName() . "'s Profile");
		$form->setContent(TF::AQUA . "Username: " . $player->getName() . "\n\n" . TF::YELLOW . "Rank: " . $rank->displayTag($player->getRankId()) . "\n\n" . TF::GOLD . "Coins: " . $player->getCoins());
		$player->sendForm($form);
	}

	/**
	 * @param mixed $sender
	 * @param mixed $player
	 */
	public static function viewPlayerProfile($sender, $player) : void
	{
		$rank = Rank::getInstance();
		$form = new SimpleForm(function (Player $sender, $data) use ($rank, $player) {
			if ($data === null) {
				return;
			}
		});

		$form->setTitle(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . $player->getName() . "'s Profile");
		$form->setContent(TF::AQUA . "Username: " . $player->getName() . "\n\n" . TF::YELLOW . "Rank: " . $rank->displayTag($player->getRankId()) . "\n\n" . TF::GOLD . "Coins: " . $player->getCoins());
		$sender->sendForm($form);
	}

	/**
	 * @param mixed $player
	 */
	public static function viewGames($player) : void
	{
		$form = new SimpleForm(function (Player $player, $data) {
			if ($data === null) {
				return;
			}

			switch($data) {
				case 0:
					$player->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GRAY . "Coming Soon?");
					break;
				case 1:
					$player->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GRAY . "Coming Soon?");
					break;
				case 3:
					$player->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GRAY . "Coming Soon?");
					break;
			}
		});

		$form->setTitle(Core::PREFIX . " " . Core::ARROW . " " . TF::AQUA . "Game Selector");
		$form->setContent(TF::GRAY . "Select Games, Do you want to play in this server!");
		$form->addButton(TF::YELLOW . "Practice", 0, "textures/ui/pride/icons/ranked.png");
		$form->addButton(TF::RED . "Bed" . TF::AQUA . "Wars", 0, "textures/ui/pride/icons/build.png");
		$form->addButton(TF::AQUA . "Sky" . TF::RED . "Wars", 0, "textures/items/diamond_sword.png");
		$player->sendForm($form);
	}

	/**
	 * @param mixed $player
	 */
	public static function viewCosmetics($player) : void{
		$form = new SimpleForm(function(Player $player, $data){
			if($data === null) return;
			switch($data){
				case 0:
					//Forms::getInstance()->viewCapes();
					$player->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GRAY . "Coming Soon...");
					break;
				case 1:
					Forms::getInstance()->viewTags($player);
					break;
			}
		});

		$form->setTitle(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "Your Locker");
		$form->addButton(TF::GREEN . "Capes\n" . TF::AQUA . "Tap to Manage", 0, "textures/ui/pride/icons/cape.png");
		$form->addButton(TF::GREEN . "Tags\n" . TF::AQUA . "Tap to Manage", 0, "textures/ui/pride/icons/tag.png");
		$player->sendForm($form);
	}

	/**
	 * @param mixed $player
	 */
	public function viewTags($player) : void{
		$form = new SimpleForm(function(Player $player, $data){
			if($data === null) return;

			switch($data){
				case 0:
					$this->changeTag($player);
					break;
				case 1:
					Tags::getInstance()->setTag($player, Tags::NONE);
					Tags::getInstance()->updateTag($player);
					$player->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "Your tag is now removed.");
					break;
			}
		});

		$form->setTitle(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "Tags");
		$form->addButton(TF::GREEN . "Change Tag");
		$form->addButton(TF::RED . "Remove Tag");
		$player->sendForm($form);
	}

	/**
	 * @param mixed $player
	 */
	public function changeTag($player) : void{
		$form = new SimpleForm(function(Player $player, $data){
			if($data === null) return $this->viewTags($player);

			Tags::getInstance()->setTag($player, ($data ?? Tags::NONE));
		});

		$form->setTitle(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "Tags");
		if(Tags::getOwnedTags($player) === null){
			$form->setContent(TF::RED . "You do not have any owned tags yet.");
		} else {
			$form->setContent(TF::GRAY . "Choose a tag do you want to change.");
			foreach(Tags::getOwnedTags($player) as $tag){
				$form->addButton(Tags::$tags[$tag], 0, "textures/ui/pride/icons/tag.png", $tag);
			}
		}
		$player->sendForm($form);
	}

	/**
	 * @param mixed $player
	 */
	public static function viewSettings($player) : void{
		$form = new SimpleForm(function(Player $player, $data){
			if($data === null) return;

			switch($data){
				case 0:
					SettingsManager::getInstance()->setVisiblityToPlayers($player, $player->isVisibleAllPlayers());
					$player->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "Successfully toggled visibility of players.");
					break;
				case 1:
					Forms::getInstance()->redeemForm($player);
					break;
				case 2:
					SettingsManager::getInstance()->setAlwaysSprinting($player, $player->isAlwaysSprinting());
					$player->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "Successfully toggled autosprint.");
					break;
				case 3:
					Core::getInstance()->getServer()->dispatchCommand($player, "fly");
					break;
				case 4:
					//Forms::getInstance()->disguiseForm($player);
					break;
			}
		});

		$form->setTitle(Core::PREFIX . " " . Core::ARROW . " " . TF::YELLOW . "Settings");
		$form->setContent(TF::GRAY . "Manage your personalized settings.");
		$form->addButton(TF::YELLOW . "Hide/Show Players");
		$form->addButton(TF::YELLOW . "Redeem a code");
		$form->addButton(TF::YELLOW . "AutoSprint");
		if($player->hasPermission("pride.staff.fly")) $form->addButton(TF::YELLOW . "Ability to Fly");
		if($player->hasPermission("pride.staff.disguise")) $form->addButton(TF::YELLOW . "Disguise Identity");
		$player->sendForm($form);
	}

	/**
	 * @param mixed $player
	 */
	public function redeemForm($player) : void{
		$form = new CustomForm(function (Player $player, $data){
			if ($data === null) {
				return;
			}

			Core::getInstance()->getServer()->dispatchCommand($player, "/redeem " . $data[0]);
		});

		$form->setTitle(Core::PREFIX . " " . Core::ARROW . " " . TF::YELLOW . "Redeem a code");
		$form->addLabel(TF::GRAY . "Enter the code do you want to redeem.");
		$form->addInput("", "Enter the code.", null, null);
		$player->sendForm($form);
	}

	/**
	 * @param mixed $player
	 */
	public function regionForm($player) : void {
		$form = new SimpleForm(function(Player $player, $data){
			if($data === null) return;

			switch($data){
				case 0:
					$this->northAmericaServerTransferForm($player);
					break;
				case 1:
					$this->asiaServerTransferForm($player);
					break;
				case 2:
					$this->europeServerTransferForm($player);
					break;
			}
		});

		$form->setTitle(Core::PREFIX . " " . Core::ARROW . " " . TF::YELLOW . "Region");
		$form->setContent(TF::GRAY . "Choose a region do you want to transfer.");
		$form->addButton(TF::AQUA . "North America", 0, "textures/items/heart_of_the_sea.png");
		$form->addButton(TF::GOLD . "Asia", 0, "textures/items/heart_of_the_sea.png");
		$form->addButton(TF::LIGHT_PURPLE . "Europe", 0, "textures/items/heart_of_the_sea.png");
		$player->sendForm($form);
	}

	/**
	 * @param mixed $player
	 */
	public function northAmericaServerTransferForm($player) : void{
		$form = new ModalForm(function (Player $player, $data){
			if ($data === null) {
				return;
			}

			switch($data){
				case 0:
					break;
				case 1:
					break;
			}
		});

		$form->setTitle(Core::PREFIX . " " . Core::ARROW . " " . TF::YELLOW . "Region");
		$form->setContent(TF::RED . "Are you sure do you want to transfer on " . TF::AQUA . "North America" . TF::RED . " region?\n" . TF::YELLOW . "Your game connection will be unstable and you will get a high ping from that server.\n" . TF::LIGHT_PURPLE . "You can change whether do you want by using this command again after transfering." . "\n\n" . TF::DARK_RED . "It cannot be undone.");
		$form->setButton1(TF::RED . "Yes, Transfer me!");
		$form->setButton2(TF::GREEN . "No, take me back.");
		$player->sendForm($form);
	}

	/**
	 * @param mixed $player
	 */
	public function asiaServerTransferForm($player) : void{
		$form = new ModalForm(function (Player $player, $data) {
			if ($data === null) {
				return;
			}

			switch($data){
				case 0:
					break;
				case 1:
					break;
			}
		});

		$form->setTitle(Core::PREFIX . " " . Core::ARROW . " " . TF::YELLOW . "Region");
		$form->setContent(TF::RED . "Are you sure do you want to transfer on " . TF::AQUA . "Asia" . TF::RED . " region?\n" . TF::YELLOW . "Your game connection will be unstable and you will get a high ping from that server.\n" . TF::LIGHT_PURPLE . "You can change whether do you want by using this command again after transfering." . "\n\n" . TF::DARK_RED . "It cannot be undone.");
		$form->setButton1(TF::RED . "Yes, Transfer me!");
		$form->setButton2(TF::GREEN . "No, take me back.");
		$player->sendForm($form);
	}

	/**
	 * @param mixed $player
	 */
	public function europeServerTransferForm($player) : void{
		$form = new ModalForm(function (Player $player, $data) {
			if ($data === null) {
				return;
			}

			switch($data){
				case 0:
					break;
				case 1:
					break;
			}
		});

		$form->setTitle(Core::PREFIX . " " . Core::ARROW . " " . TF::YELLOW . "Region");
		$form->setContent(TF::RED . "Are you sure do you want to transfer on " . TF::AQUA . "Europe" . TF::RED . " region?\n" . TF::YELLOW . "Your game connection will be unstable and you will get a high ping from that server.\n" . TF::LIGHT_PURPLE . "You can change whether do you want by using this command again after transfering." . "\n\n" . TF::DARK_RED . "It cannot be undone.");
		$form->setButton1(TF::RED . "Yes, Transfer me!");
		$form->setButton2(TF::GREEN . "No, take me back.");
		$player->sendForm($form);
	}

	/**
	 * @param mixed $player
	 */
	public function giveTags($player) : void {
		$form = new CustomForm(function (Player $player, $data){
			if ($data === null) {
				return;
			}

			if (($target = Core::getInstance()->getServer()->getPlayerExact($data[0])) === null) {
				$player->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "Sorry, we couldnt find player: " . $data[1]);
			} else {
				Tags::getInstance()->addTag($data[0], $data[1] + 1);
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . TF::GREEN . " Successfully given to " . TF::AQUA . $target->getName() . TF::GREEN . " a tag.");
			}
		});

		$form->setTitle(Core::PREFIX . " " . Core::ARROW . " " . TF::YELLOW . "Give Tags");
		$form->addInput("", "Enter the username.");
		$form->addDropdown("", array_values(Tags::$tags));
		$player->sendForm($form);
	}

	/**
	 * @param mixed $player
	 */
	public function removeTagsNameForm($player) : void {
		$form = new CustomForm(function (Player $player, $data){
			if ($data === null) {
				return;
			}

			if (($target = Core::getInstance()->getServer()->getPlayerExact($data[1])) === null) {
				$player->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "Sorry, we couldnt find player: " . $data[1]);
			} else {
				if(Tags::getInstance()->getOwnedTags($target) === null) {
					$player->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "The player " . TF::DARK_RED . " has no any tags owned.");
					return;
				}

				$tags = Tags::getOwnedTags($target);
				$list = [];
				foreach($tags as $tag){
					$list[] = [$tag => Tags::getInstance()->displayName($tag)]; // converter
				}
				$this->removeTag($player, $target, $list);
			}
		});

		$form->setTitle(Core::PREFIX . " " . Core::ARROW . " " . TF::YELLOW . "Remove Tags");
		$form->addInput("", "Enter the username.");
		$player->sendForm($form);
	}

	/**
	 * @param mixed $player
	 * @param mixed $target
	 * @param mixed $list
	 */
	public function removeTags($player, $target, $list) : void{
		$form = new SimpleForm(function (Player $player, $data) use ($list){
			if ($data === null) {
				return;
			}

			Tags::getInstance()->removeTag($target, $data);
			$player->sendMessage(Core::PREFIX . " " . Core::ARROW . TF::GREEN . " Successfully removed to " . TF::AQUA . $target->getName() . TF::GREEN . " the " . Tags::$tags[$data] . TF::GREEN . " tag.");
		});

		$form->setTitle(Core::PREFIX . " " . Core::ARROW . " " . TF::YELLOW . "Remove Tags");
		foreach($list as $tag){
			$form->addButton($list[$tag], 0, "textures/ui/pride/icons/tag.png", $tag);
		}
		$player->sendForm($form);
	}

	/**
	 * @param mixed $player
	 */
	public static function cosmeticsForm($player) : void{
		$form = new SimpleForm(function (Player $player, $data){
			if($data === null) return;

			switch($data){
				case 0:
					Forms::getInstance()->actionTags($player);
					break;
				case 1:
					Forms::getInstance()->actionCape($player);
					break;
			}
		});

		$form->setTitle(Core::PREFIX . " " . Core::ARROW . " " . TF::YELLOW . "Remove Tags");
		$form->setContent(TF::GRAY . "Choose an action do you want to change.");
		$form->addButton(TF::YELLOW . "Capes");
		$form->addButton(TF::YELLOW . "Tags");
		$player->sendForm($form);
	}

	/**
	 * @param mixed $player
	 */
	public function giveCape($player) : void {
		$form = new CustomForm(function (Player $player, $data){
			if ($data === null) {
				return;
			}

			if (($target = Core::getInstance()->getServer()->getPlayerExact($data[0])) === null) {
				$player->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "Sorry, we couldnt find player: " . $data[1]);
			} else {
				Capes::getInstance()->addCape($target, Capes::getInstance()->getAllCapes()[$data[1]]);
				$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . TF::GREEN . " Successfully given to " . TF::AQUA . $target->getName() . TF::GREEN . " a tag.");
			}
		});

		$form->setTitle(Core::PREFIX . " " . Core::ARROW . " " . TF::YELLOW . "Give Cape");
		$form->addInput("", "Enter the username.");
		$form->addDropdown("", Capes::getInstance()->toPrettyPrint(Capes::getInstance()->getAllCapes()));
		$player->sendForm($form);
	}

	/**
	 * @param mixed $player
	 */
	public function removeCapeNameForm($player) : void {
		$form = new CustomForm(function (Player $player, $data){
			if ($data === null) {
				return;
			}

			if (($target = Core::getInstance()->getServer()->getPlayerExact($data[1])) === null) {
				$player->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "Sorry, we couldnt find player: " . $data[1]);
			} else {
				if(Tags::getInstance()->getOwnedTags($target) === null) {
					$player->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "The player " . TF::DARK_RED . " has no any tags owned.");
					return;
				}

				$capes = Capes::getOwnedCape($target);
				$list = [];
				foreach($capes as $cape){
					$list[] = $cape; // converter
				}
				$this->removeCape($player, $target, $list);
			}
		});

		$form->setTitle(Core::PREFIX . " " . Core::ARROW . " " . TF::YELLOW . "Remove Tags");
		$form->addInput("", "Enter the username.");
		$player->sendForm($form);
	}

	/**
	 * @param mixed $player
	 * @param mixed $target
	 * @param mixed $list
	 */
	public function removeCape($player, $target, $list) : void{
		$form = new SimpleForm(function (Player $player, $data) use ($list){
			if ($data === null) {
				return;
			}

			Tags::removeCape($target, $data);
			$player->sendMessage(Core::PREFIX . " " . Core::ARROW . TF::GREEN . " Successfully removed to " . TF::AQUA . $target->getName() . TF::GREEN . " the " . Tags::$tags[$data] . TF::GREEN . " tag.");
		});

		$form->setTitle(Core::PREFIX . " " . Core::ARROW . " " . TF::YELLOW . "Remove Tags");
		foreach($list as $tag){
			$form->addButton($list[$tag], 0, "textures/ui/pride/icons/cape.png", $tag);
		}
		$player->sendForm($form);
	}

	/**
	 * @param mixed $player
	 */
	public function actionCape($player) : void{
		$form = new SimpleForm(function (Player $player, $data){
			if($data === null) return;

			switch($data){
				case 0:
					Forms::getInstance()->giveCape($player);
					break;
				case 1:
					Forms::getInstance()->removeCapeNameForm($player);
					break;
			}
		});

		$form->setTitle(Core::PREFIX . " " . Core::ARROW . " " . TF::YELLOW . "Cosmetics");
		$form->setContent(TF::GRAY . "Choose an action do you want to change.");
		$form->addButton(TF::YELLOW . "Add Capes to Player");
		$form->addButton(TF::YELLOW . "Remove Capes to Player");
		$player->sendForm($form);
	}

	/**
	 * @param mixed $player
	 */
	public function actionTags($player) : void{
		$form = new SimpleForm(function (Player $player, $data){
			if($data === null) return;

			switch($data){
				case 0:
					Forms::getInstance()->giveTags($player);
					break;
				case 1:
					Forms::getInstance()->removeTagsNameForm($player);
					break;
			}
		});

		$form->setTitle(Core::PREFIX . " " . Core::ARROW . " " . TF::YELLOW . "Cosmetics");
		$form->setContent(TF::GRAY . "Choose an action do you want to change.");
		$form->addButton(TF::YELLOW . "Add Tags to Player");
		$form->addButton(TF::YELLOW . "Remove Tags to Player");
		$player->sendForm($form);
	}

	public function viewCapes($player) : void{
		$form = new SimpleForm(function(Player $player, $data){
			if($data === null) return;
			$player->setCape($data);
		});

		$form->setTitle(Core::PREFIX . " " . Core::ARROW . " " . TF::YELLOW . "Capes");
		if($player->getOwnedCape() === null){
			$form->setContent(TF::RED . "You do not have owned capes yet.");
		} else {
			$form->setContent(TF::GRAY . "Choose a cape do you want to equip.");
			foreach(Capes::getInstance()->getOwnedCape($player) as $cape){
				$form->addButton(TF::YELLOW . Capes::getInstance()->toPrettyPrint($cape), 0, "", $cape);
			}
		}
	}
}
