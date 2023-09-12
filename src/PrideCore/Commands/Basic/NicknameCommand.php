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
use PrideCore\Player\Player;
use PrideCore\Utils\Rank;
use PrideCore\Utils\Utils;
use function count;

/**
 * Change player nickname.
 */
class NicknameCommand extends Command implements PluginOwned {

	public function getOwningPlugin() : Core {
		return Core::getInstance();
	}

	public function __construct(){
		parent::__construct("nickname", "Change your nickname.", "/nickname [reset]", ["nick"]);
		$this->setPermission("pride.media.nick");
	}

	public function execute(CommandSender $sender, string $label, array $args) : void{
		if (!$this->testPermission($sender)) {
			return;
		}

		if(!$sender instanceof Player){
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . TF::RED . " Sorry, this can be only executed as a player.");
			return;
		}
		if(!(count($args) === 0) && $args[0] === "reset"){
			$sender->removeNick();
			Rank::getInstance()->displayName($sender);
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . TF::GREEN . " Successfully reseted your nickname.");
		} else {
			$name = Utils::generateName();
			$sender->setNick($name);
			$sender->setNametag(TF::GOLD . $sender->getNick() . TF::RESET . " " . TF::DARK_GRAY . "[" . Rank::getInstance()->displayTag(Rank::PLAYER) . TF::DARK_GRAY . "]");
			$sender->sendMessage(Core::PREFIX . " " . Core::ARROW . TF::GREEN . " Successfully changed your nickname to " . TF::GOLD . $name . TF::GREEN . "!");
		}
	}
}
