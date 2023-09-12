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
 *                     Season #5
 *
 *  www.mcpride.tk                 github.com/PrideMC
 *  twitter.com/PrideMC         youtube.com/c/PrideMC
 *  discord.gg/PrideMC           facebook.com/PrideMC
 *               bit.ly/JoinInPrideMC
 *  #PrideGames                           #PrideMonth
 *
 */

declare(strict_types=1);

namespace PrideCore\Commands\Staff;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;
use PrideCore\Core;
use PrideCore\Player\Player;
use PrideCore\Utils\Cache;
use PrideCore\Utils\Forms\SimpleForm;

/**
 * Absolute warn to player.
 */
class WarnCommand extends Command implements PluginOwned
{

	public function getOwningPlugin() : Core
	{
		return Core::getInstance();
	}

	public function __construct()
	{
		parent::__construct("warn", "Warn a player.", "/warn <player_name> <reason>");
		$this->setPermission("pride.staff.warn");
	}

	public function execute(CommandSender $sender, string $label, array $args) : void
	{
		if (!$this->testPermission($sender)) {
			return;
		}

		if (!isset($args[0])) {
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "Usage: " . $this->usageMessage);
			return;
		}

		if (!isset($args[1])) {
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "Usage: " . $this->usageMessage);
			return;
		}

		if (($target = Server::getInstance()->getPlayerExact($args[0])) === null) {
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "The player " . TF::DARK_RED . $args[0] . TF::RED . " couldnt find in the server.");
		} else {
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "Successfully warned " . TF::YELLOW . $args[0] . TF::GREEN . "for " . TF::YELLOW . $args[1]);
			Cache::getInstance()->addWarnCount($target->getUniqueId()->__toString());
			$this->warn($target, $args[1], Cache::getInstance()->getWarnCount($target->getUniqueId()->__toString()), $sender);
		}
	}

	private function warn(Player $player, string $reason, int $violation = 0, $source = null) : void
	{
		if (Cache::getInstance()->getWarnCount($player->getUniqueId()->__toString()) === 5) {
			$player->kick(Core::PREFIX . TF::GRAY . "\nYou have been kicked from our network." . "\n\n" . Core::ARROW . TF::RESET . TF::GRAY . " Reason: " . TF::YELLOW . "Multiple Warnings" . TF::RESET);
			return;
		}

		if (Cache::getInstance()->getWarnCount($player->getUniqueId()->__toString()) === 10) {
			$p = Server::getInstance()->getPlayerExact($player);
			Server::getInstance()->getNameBans()->addBan($player, $reason, Utils::stringToTimestamp("7d")[0], "PrideMC");
			$p->kick(Core::PREFIX . TF::GRAY . "\nYou have been banned from our network." . "\n\n" . Core::ARROW . TF::RESET . TF::GRAY . " Reason: " . TF::YELLOW . "Multiple Warnings" . TF::RESET . "\n" . Core::ARROW . TF::RESET . TF::GRAY . "Expires: " . TF::YELLOW . Utils::stringToTimestamp("30d")[0]->format("Y-m-d H:i:s"));
			return;
		}

		$form = new SimpleForm(function (Player $player, $data) use ($reason, $violation, $source) {
			if ($data === null) {
				$this->warn($player, $reason, $violation, $source);
			} elseif($data === 0){
				$player->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "To avoid punishments, please checkout our server rules. Once you're warned 5x or more, you'll get punishments from our server.");
				$source->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . $player->getName() . " is accepted their warning.");
			} elseif($data === 1){
				$player->kick(Core::PREFIX . TF::GRAY . "\nYou have been kicked from our network." . "\n\n" . Core::ARROW . TF::RESET . TF::GRAY . " Reason: " . TF::YELLOW . "Declined our Warnings." . TF::RESET);
				$source->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . $player->getName() . " is declined their warning.");
			}
		});

		$form->setTitle(Core::PREFIX . " " . Core::ARROW . " " . TF::YELLOW . TF::BOLD . "WARNING!");
		$form->setContent(TF::YELLOW . "You have been warned to our server!" . "\n\n" . TF::GOLD . "Reason " . Core::ARROW . " " . TF::RED . $reason . "\n" . TF::GOLD . "Warning Count " . Core::ARROW . " " . TF::RED . $violation . "\n\n" . TF::BOLD . TF::AQUA . "»»»" . " " . TF::RESET . TF::DARK_RED . "Warning Information" . TF::BOLD . " " . TF::AQUA . "«««" . TF::RESET . "\n\n" . TF::RED . "When you're seeing this form, that means one of our moderators has been warned you about the reason provided. To avoid punishments, Please checkout our server rules. Once you're warned 5x or more, you'll get punishments.");
		$form->addButton(TF::GREEN . "Accept, i'll not do it again.");
		$form->addButton(TF::RED . "Decline, i'll not accepting their decisions.");
		$player->sendForm($form);
	}
}
