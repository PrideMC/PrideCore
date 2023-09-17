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

namespace PrideCore\Discord;

use JaxkDev\DiscordBot\Models\Presence\Status;
use JaxkDev\DiscordBot\Models\Presence\Activity\Activity;
use JaxkDev\DiscordBot\Plugin\ApiRejection;
use JaxkDev\DiscordBot\Plugin\Events\DiscordReady;
use pocketmine\event\Listener;
use pocketmine\utils\Config as PMConfig;
use pocketmine\utils\TextFormat as TF;
use PrideCore\Core;
use PrideCore\Utils\Config;
use function strtolower;
use function strval;

class PrideBot implements Listener{
	public const PREFIX = TF::GRAY . "(" . TF::YELLOW . "PrideBot" . TF::GRAY . ")" . TF::RESET;

	public function onReady(DiscordReady $event){
		$type = match(strtolower(strval($this->getConfig()->getNested("presence.type", "Playing")))){
			'listening', 'listen' => ActivityType::LISTENING,
			'watching', 'watch' => ActivityType::WATCHING,
			default => ActivityType::PLAYING,
		};
		$status = match(strtolower(strval($this->getConfig()->getNested("presence.status", "Online")))){
			'idle' => Status::STATUS_IDLE,
			'dnd' => Status::STATUS_DND,
			'offline' => Status::STATUS_OFFLINE,
			default => Status::STATUS_ONLINE,
		};
		$activity = new Activity(strval($this->getConfig()->getNested("presence.message", "play.mcpride.tk")), $type);
		$this->plugin->getDiscord()->getApi()->updateBotPresence($activity, $status)->then(function(){
			$this->plugin->getLogger()->debug(PrideBot::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "Presence successfully updated.");
		}, function(ApiRejection $rejection){
			$this->plugin->getLogger()->error(PrideBot::PREFIX . " " . Core::ARROW . " " . TF::RED . "Failed to update presence: " . $rejection->getMessage());
		});
	}

	public function getConfig() : PMConfig{
		return Config::getInstance()->getDiscordConfig();
	}

	public static function load() : void{
		Core::getInstance()->getServer()->getPluginManager()->registerEvents(new PrideBot(), Core::getInstance());
	}
}
