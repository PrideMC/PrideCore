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

use CortexPE\DiscordWebhookAPI\Component;
use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use pocketmine\utils\SingletonTrait;
use PrideCore\Core;
use PrideCore\Utils\Config;

/**
 * Discord Webhook related functions.
 */
class DiscordWebhook
{
	use SingletonTrait;

	public const RED = 0xFF000;
	public const WHITE = 0xFFFFF;
	public const GREEN = 0x00FF00;
	public const LIGHT_BLUE = 0x87CEFA;
	public const BLUE = 0x0000FF;
	public const BLURPLE = 0x7289DA; // Blurple
	public const LIGHT_PURPLE = 0xFF00FF;

	public function __construct()
	{
		//NOOP
	}

	public function getConfigs() : Config
	{
		return Config::getInstance();
	}

	/**
	 * Sends ban information to discord.
	 */
	public function sendBan(string $name, ?string $reason = "Unspecified", ?string $duration = "Permanent", string $source = "PrideMC") : void
	{
		if(!Core::$connected) return;
		$webHook = new Webhook($this->getConfigs()->getDiscordConfig()->getNested("webhook.url"));
		$msg = new Message();
		$msg->setUsername("PrideMC Network");
		$msg->setAvatarURL($this->getConfigs()->getDiscordConfig()->getNested("webhook.avatar"));
		$embed = new Embed();
		$embed->setTitle("Banned by " . $source);
		$embed->setColor(self::RED);
		$embed->setDescription("```yml\n{$name} was banned by {$source} for {$reason}!\n```");
		$msg->addEmbed($embed);
		$component = new Component();
		$component->addLinkButton("Website", $this->getConfigs()->getDiscordConfig()->getNested("webhook.button-link"));
		$msg->addComponent($component);
		$webHook->send($msg);
	}

	/**
	 * Sends pardon information to discord.
	 */
	public function sendPardon(string $name, string $source = "PrideMC") : void
	{
		if(!Core::$connected) return;
		$webHook = new Webhook($this->getConfigs()->getDiscordConfig()->getNested("webhook.url"));
		$msg = new Message();
		$msg->setUsername("PrideMC Network");
		$msg->setAvatarURL($this->getConfigs()->getDiscordConfig()->getNested("webhook.avatar"));
		$embed = new Embed();
		$embed->setTitle("Unbanned by " . $source);
		$embed->setColor(self::RED);
		$embed->setDescription("```yml\n{$name} was unbanned by {$source}!\n```");
		$msg->addEmbed($embed);
		$component = new Component();
		$component->addLinkButton("Website", $this->getConfigs()->getDiscordConfig()->getNested("webhook.button-link"));
		$msg->addComponent($component);
		$webHook->send($msg);
	}

	/**
	 * Send kick information on discord.
	 */
	public function sendKick(string $name, ?string $reason = "Unspecified", string $source = "PrideMC") : void
	{
		if(!Core::$connected) return;
		$webHook = new Webhook($this->getConfigs()->getDiscordConfig()->getNested("webhook.url"));
		$msg = new Message();
		$msg->setUsername("PrideMC Network");
		$msg->setAvatarURL($this->getConfigs()->getDiscordConfig()->getNested("webhook.avatar"));
		$embed = new Embed();
		$embed->setTitle("Kicked by " . $source);
		$embed->setColor(self::RED);
		$embed->setDescription("```yml\n{$name} was kicked by {$source} for {$reason}!\n```");
		$msg->addEmbed($embed);
		$component = new Component();
		$component->addLinkButton("Website", $this->getConfigs()->getDiscordConfig()->getNested("webhook.button-link"));
		$msg->addComponent($component);
		$webHook->send($msg);
	}

	/**
	 * Send mute information to discord.
	 */
	public function sendMute(string $name, ?string $reason = "Unspecified", string $source = "PrideMC") : void
	{
		if(!Core::$connected) return;
		$webHook = new Webhook($this->getConfigs()->getDiscordConfig()->getNested("webhook.url"));
		$msg = new Message();
		$msg->setUsername("PrideMC Network");
		$msg->setAvatarURL($this->getConfigs()->getDiscordConfig()->getNested("webhook.avatar"));
		$embed = new Embed();
		$embed->setTitle("Muted by " . $source);
		$embed->setColor(self::RED);
		$embed->setDescription("```yml\n{$name} was muted by {$source} for {$reason}!\n```");
		$msg->addEmbed($embed);
		$component = new Component();
		$component->addLinkButton("Website", $this->getConfigs()->getDiscordConfig()->getNested("webhook.button-link"));
		$msg->addComponent($component);
		$webHook->send($msg);
	}

	/**
	 * Send unmute information to discord.
	 */
	public function sendUnmute(string $name, ?string $reason = "Unspecified", string $source = "PrideMC") : void
	{
		if(!Core::$connected) return;
		$webHook = new Webhook($this->getConfigs()->getDiscordConfig()->getNested("webhook.url"));
		$msg = new Message();
		$msg->setUsername("PrideMC Network");
		$msg->setAvatarURL($this->getConfigs()->getDiscordConfig()->getNested("webhook.avatar"));
		$embed = new Embed();
		$embed->setTitle("Unmuted by " . $source);
		$embed->setColor(self::RED);
		$embed->setDescription("```yml\n{$name} was unmuted by {$source} for {$reason}!\n```");
		$msg->addEmbed($embed);
		$component = new Component();
		$component->addLinkButton("Website", $this->getConfigs()->getDiscordConfig()->getNested("webhook.button-link"));
		$msg->addComponent($component);
		$webHook->send($msg);
	}

	/**
	 * Send enabled information on discord.
	 */
	public function sendEnabled() : void
	{
		if(!Core::$connected) return;
		$webHook = new Webhook($this->getConfigs()->getDiscordConfig()->getNested("webhook.url"));
		$msg = new Message();
		$msg->setUsername("PrideMC Network");
		$msg->setAvatarURL($this->getConfigs()->getDiscordConfig()->getNested("webhook.avatar"));
		$embed = new Embed();
		$embed->setTitle("Signal Detected");
		$embed->setColor(self::RED);
		$embed->setDescription("```yml\nThe server is now online!\n```");
		$msg->addEmbed($embed);
		$component = new Component();
		$component->addLinkButton("Website", $this->getConfigs()->getDiscordConfig()->getNested("webhook.button-link"));
		$msg->addComponent($component);
		$webHook->send($msg);
	}

	/**
	 * Send disabled information on discord.
	 */
	public function sendDisabled() : void
	{
		if(!Core::$connected) return;
		$webHook = new Webhook($this->getConfigs()->getDiscordConfig()->getNested("webhook.url"));
		$msg = new Message();
		$msg->setUsername("PrideMC Network");
		$msg->setAvatarURL($this->getConfigs()->getDiscordConfig()->getNested("webhook.avatar"));
		$embed = new Embed();
		$embed->setTitle("Signal Detected");
		$embed->setColor(self::RED);
		$embed->setDescription("```yml\nThe server is now offline!\n```");
		$msg->addEmbed($embed);
		$component = new Component();
		$component->addLinkButton("Website", $this->getConfigs()->getDiscordConfig()->getNested("webhook.button-link"));
		$msg->addComponent($component);
		$webHook->send($msg);
	}

	/**
	 * Send global mute information on discord.
	 */
	public function sendGlobalMute(bool $confirm, string $source, ?string $reason = "Unspecified") : void
	{
		if(!Core::$connected) return;
		$webHook = new Webhook($this->getConfigs()->getDiscordConfig()->getNested("webhook.url"));
		$msg = new Message();
		$msg->setUsername("PrideMC Network");
		$msg->setAvatarURL($this->getConfigs()->getDiscordConfig()->getNested("webhook.avatar"));
		$embed = new Embed();
		$embed->setTitle("Server Global Chat Mute");
		$embed->setColor(self::RED);
		if ($confirm) {
			$embed->setDescription("```yml\nThe server is now global muted by {$source} for {$reason}!\n```");
		} else {
			$embed->setDescription("```yml\nThe server is now global unmuted by {$source} for {$reason}!\n```");
		}
		$msg->addEmbed($embed);
		$component = new Component();
		$component->addLinkButton("Website", $this->getConfigs()->getDiscordConfig()->getNested("webhook.button-link"));
		$msg->addComponent($component);
		$webHook->send($msg);
	}

	/**
	 * Send freeze information on discord.
	 */
	public function sendFreeze(string $name, ?string $source = "PrideMC", ?string $reason = "Unspecified") : void
	{
		if(!Core::$connected) return;
		$webHook = new Webhook($this->getConfigs()->getDiscordConfig()->getNested("webhook.url"));
		$msg = new Message();
		$msg->setUsername("PrideMC Network");
		$msg->setAvatarURL($this->getConfigs()->getDiscordConfig()->getNested("webhook.avatar"));
		$embed = new Embed();
		$embed->setTitle("Frozen by " . $source);
		$embed->setColor(self::RED);
		$embed->setDescription("```yml\n{$name} was frozen by {$source} for {$reason}!\n```");
		$msg->addEmbed($embed);
		$component = new Component();
		$component->addLinkButton("Website", $this->getConfigs()->getDiscordConfig()->getNested("webhook.button-link"));
		$msg->addComponent($component);
		$webHook->send($msg);
	}

	/**
	 * Send unfreeze information to discord.
	 */
	public function sendUnfreeze(string $name, ?string $source = "PrideMC", ?string $reason = "Unspecified") : void
	{
		if(!Core::$connected) return;
		$webHook = new Webhook($this->getConfigs()->getDiscordConfig()->getNested("webhook.url"));
		$msg = new Message();
		$msg->setUsername("PrideMC Network");
		$msg->setAvatarURL($this->getConfigs()->getDiscordConfig()->getNested("webhook.avatar"));
		$embed = new Embed();
		$embed->setTitle("Unfrozen by " . $source);
		$embed->setColor(self::RED);
		$embed->setDescription("```yml\n{$name} was unfrozen by {$source} for {$reason}!\n```");
		$msg->addEmbed($embed);
		$component = new Component();
		$component->addLinkButton("Website", $this->getConfigs()->getDiscordConfig()->getNested("webhook.button-link"));
		$msg->addComponent($component);
		$webHook->send($msg);
	}
}
