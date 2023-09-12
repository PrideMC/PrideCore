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

namespace PrideCore\Commands\Basic;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat as TF;
use PrideCore\Core;

/**
 * Links
 */
class LinksCommand extends Command implements PluginOwned
{

	public function getOwningPlugin() : Core
	{
		return Core::getInstance();
	}

	public function __construct()
	{
		parent::__construct("links", "Links", "/links", ["link"]);
		$this->setPermission("pride.basic.command");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : void
	{
		$sender->sendMessage(TF::GRAY . "--- " . Core::PREFIX . TF::GOLD . " Network" . "" . TF::GRAY . " ---");
		$sender->sendMessage(TF::BOLD . TF::YELLOW . "- Official Links");
		$sender->sendMessage(TF::YELLOW . "  * " . TF::GREEN . TF::BOLD . "WEBSITE: " . TF::RESET . TF::AQUA . "https://www.mcpride.tk/");
		$sender->sendMessage(TF::YELLOW . "  * " . TF::GREEN . TF::BOLD . "SUPPORT: " . TF::RESET . TF::AQUA . "https://support.mcpride.tk/");
		$sender->sendMessage(TF::YELLOW . "  * " . TF::GREEN . TF::BOLD . "APPEAL: " . TF::RESET . TF::AQUA . "https://appeal.mcpride.tk/");
		$sender->sendMessage(TF::YELLOW . "  * " . TF::GREEN . TF::BOLD . "NEWSLETTER: " . TF::RESET . TF::AQUA . "https://news.mcpride.tk/");
		$sender->sendMessage(TF::YELLOW . "  * " . TF::GREEN . TF::BOLD . "CAREERS: " . TF::RESET . TF::AQUA . "https://jobs.mcpride.tk/");
		$sender->sendMessage(TF::YELLOW . "  * " . TF::GREEN . TF::BOLD . "DISCORD: " . TF::RESET . TF::AQUA . "https://www.mcpride.tk/discord");
		$sender->sendMessage(TF::YELLOW . "  * " . TF::GREEN . TF::BOLD . "STORE: " . TF::RESET . TF::AQUA . "https://store.mcpride.tk/");
		$sender->sendMessage("");
		$sender->sendMessage(TF::BOLD . TF::YELLOW . "- Official Social Media");
		$sender->sendMessage(TF::YELLOW . "  * " . TF::BLUE . TF::BOLD . "FACEBOOK: " . TF::RESET . TF::AQUA . "@PrideMC");
		$sender->sendMessage(TF::YELLOW . "  * " . TF::RED . TF::BOLD . "YOUTUBE: " . TF::RESET . TF::AQUA . "@PrideMC");
		$sender->sendMessage(TF::YELLOW . "  * " . TF::AQUA . TF::BOLD . "TWITTER: " . TF::RESET . TF::AQUA . "@PrideMC");
		$sender->sendMessage(TF::YELLOW . "  * " . TF::WHITE . TF::BOLD . "GITHUB: " . TF::RESET . TF::AQUA . "@PrideMC");
		$sender->sendMessage(TF::YELLOW . "  * " . TF::DARK_AQUA . TF::BOLD . "LINKEDIN: " . TF::RESET . TF::AQUA . "@PrideMC");
		$sender->sendMessage(TF::RED . "Please " . TF::DARK_RED . "ENTER" . TF::RED . " this in your " . TF::DARK_RED . "BROWSER!");
		$sender->sendMessage("");
		$sender->sendMessage(TF::GRAY . Core::PREFIX . " is under development of " . TF::YELLOW . "PrideGames Limited (c) 2023");
	}
}
