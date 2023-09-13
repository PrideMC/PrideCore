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

use JaxkDev\DiscordBot\Plugin\Events\DiscordReady;
use pocketmine\event\Listener;
use JaxkDev\DiscordBot\Models\Activity;
use JaxkDev\DiscordBot\Models\Member;
use JaxkDev\DiscordBot\Models\Messages\Embed\Author;
use JaxkDev\DiscordBot\Models\Messages\Embed\Embed;
use JaxkDev\DiscordBot\Models\Messages\Embed\Field;
use JaxkDev\DiscordBot\Models\Messages\Embed\Footer;
use JaxkDev\DiscordBot\Models\Messages\Message;
use JaxkDev\DiscordBot\Models\Messages\Webhook;
use JaxkDev\DiscordBot\Plugin\ApiRejection;
use JaxkDev\DiscordBot\Plugin\Events\DiscordClosed;
use JaxkDev\DiscordBot\Plugin\Events\MemberJoined;
use JaxkDev\DiscordBot\Plugin\Events\MemberLeft;
use JaxkDev\DiscordBot\Plugin\Events\MessageSent;
use JaxkDev\DiscordBot\Plugin\Storage;
use pocketmine\utils\TextFormat as TF;
use PrideCore\Utils\Config;
use pocketmine\utils\Config as PMConfig;

class Bot implements Listener{

	public function __construct(){

	}

    public const PREFIX = TF::GRAY . "(" . TF::YELLOW . "PrideBot" . TF::GRAY . ")" . TF::RESET;

	public function onReady(DiscordReady $event){
        $type = match(strtolower(strval($this->getConfig()->getNested("presence.type", "Playing")))){
            'listening', 'listen' => Activity::TYPE_LISTENING,
            'watching', 'watch' => Activity::TYPE_WATCHING,
            default => Activity::TYPE_PLAYING,
        };
        $status = match(strtolower(strval($this->getConfig()->getNested("presence.status", "Online")))){
            'idle' => Member::STATUS_IDLE,
            'dnd' => Member::STATUS_DND,
            'offline' => Member::STATUS_OFFLINE,
            default => Member::STATUS_ONLINE,
        };
        $activity = new Activity(strval($this->getConfig()->getNested("presence.message", "play.mcpride.tk")), $type);
        $this->plugin->getDiscord()->getApi()->updateBotPresence($activity, $status)->then(function(){
            $this->plugin->getLogger()->debug(PrideBot::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "Presence successfully updated.");
        }, function(ApiRejection $rejection){
            $this->plugin->getLogger()->error(PrideBot::PREFIX . " " . Core::ARROW . " " . TF::RED . "Failed to update presence: ".$rejection->getMessage());
        });
	}

    public function getConfig() : PMConfig{
        return Config::getInstance()->getDiscordConfig();
    }
}
