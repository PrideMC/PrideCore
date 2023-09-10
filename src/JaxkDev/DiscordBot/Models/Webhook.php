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

/*
 * DiscordBot, PocketMine-MP Plugin.
 *
 * Licensed under the Open Software License version 3.0 (OSL-3.0)
 * Copyright (C) 2020-present JaxkDev
 *
 * Discord :: JaxkDev
 * Email   :: JaxkDev@gmail.com
 */

namespace JaxkDev\DiscordBot\Models;

use AssertionError;
use JaxkDev\DiscordBot\Communication\BinarySerializable;
use JaxkDev\DiscordBot\Communication\BinaryStream;

use JaxkDev\DiscordBot\Plugin\Api;
use JaxkDev\DiscordBot\Plugin\Utils;
use function stripos;
use function strlen;

/**
 * @implements BinarySerializable<Webhook>
 * @link https://discord.com/developers/docs/resources/webhook#webhook-object
 */
final class Webhook implements BinarySerializable{

	public const SERIALIZE_ID = 11;

	/**
	 * The type of the webhook
	 * @see WebhookType
	 */
	private WebhookType $type;

	/** The ID of the webhook */
	private string $id;

	/** The guild ID this webhook is for, if any */
	private ?string $guild_id;

	/** The channel ID this webhook is for, if any */
	private ?string $channel_id;

	/** the user this webhook was created by */
	private ?string $user_id;

	/**
	 * The name of the webhook.
	 * Cannot contain the substrings 'clyde' or 'discord' (case-insensitive), limit 80 characters
	 */
	private ?string $name;

	/** The user avatar of the webhook */
	private ?string $avatar;

	/** The secure token of the webhook (only for Incoming Webhooks) */
	private ?string $token;

	/** The bot/OAuth2 application that created this webhook */
	private ?string $application_id;

	/** The guild ID of the channel that this webhook is following (only for Channel Follower Webhooks) */
	private ?string $source_guild_id;

	/** The channel ID of the channel that this webhook is following (only for Channel Follower Webhooks) */
	private ?string $source_channel_id;

	/**
	 * @internal
	 * @see Api::createWebhook()
	 */
	public function __construct(WebhookType $type, string $id, ?string $guild_id = null, ?string $channel_id = null,
								?string $user_id = null, ?string $name = null, ?string $avatar = null, ?string $token = null,
								?string $application_id = null, ?string $source_guild_id = null, ?string $source_channel_id = null){
		$this->setType($type);
		$this->setId($id);
		$this->setGuildId($guild_id);
		$this->setChannelId($channel_id);
		$this->setUserId($user_id);
		$this->setName($name);
		$this->setAvatar($avatar);
		$this->setToken($token);
		$this->setApplicationId($application_id);
		$this->setSourceGuildId($source_guild_id);
		$this->setSourceChannelId($source_channel_id);
	}

	public function getType() : WebhookType{
		return $this->type;
	}

	public function setType(WebhookType $type) : void{
		$this->type = $type;
	}

	public function getId() : string{
		return $this->id;
	}

	public function setId(string $id) : void{
		if(!Utils::validDiscordSnowflake($id)){
			throw new AssertionError("ID '$id' is invalid.");
		}
		$this->id = $id;
	}

	public function getGuildId() : ?string{
		return $this->guild_id;
	}

	public function setGuildId(?string $guild_id) : void{
		if($guild_id !== null && !Utils::validDiscordSnowflake($guild_id)){
			throw new AssertionError("Guild ID '$guild_id' is invalid.");
		}
		$this->guild_id = $guild_id;
	}

	public function getChannelId() : ?string{
		return $this->channel_id;
	}

	public function setChannelId(?string $channel_id) : void{
		if($channel_id !== null && !Utils::validDiscordSnowflake($channel_id)){
			throw new AssertionError("Channel ID '$channel_id' is invalid.");
		}
		$this->channel_id = $channel_id;
	}

	public function getUserId() : ?string{
		return $this->user_id;
	}

	public function setUserId(?string $user_id) : void{
		if($user_id !== null && !Utils::validDiscordSnowflake($user_id)){
			throw new AssertionError("User ID '$user_id' is invalid.");
		}
		$this->user_id = $user_id;
	}

	public function getName() : ?string{
		return $this->name;
	}

	public function setName(?string $name) : void{
		if($name === null){
			$this->name = null;
			return;
		}
		if(strlen($name) > 80){
			throw new AssertionError("Name '$name' is too long, max 80 characters.");
		}
		if(stripos($name, 'clyde') !== false || stripos($name, 'discord') !== false){
			throw new AssertionError("Name '$name' is not allowed, cannot contain 'clyde' or 'discord'.");
		}
		$this->name = $name;
	}

	public function getAvatarUrl() : ?string{
		if($this->avatar === null){
			return null;
		}
		return "https://cdn.discordapp.com/avatars/{$this->id}/{$this->avatar}.png";
	}

	public function getAvatar() : ?string{
		return $this->avatar;
	}

	public function setAvatar(?string $avatar) : void{
		$this->avatar = $avatar;
	}

	public function getToken() : ?string{
		return $this->token;
	}

	public function setToken(?string $token) : void{
		$this->token = $token;
	}

	public function getApplicationId() : ?string{
		return $this->application_id;
	}

	public function setApplicationId(?string $application_id) : void{
		if($application_id !== null && !Utils::validDiscordSnowflake($application_id)){
			throw new AssertionError("Application ID '$application_id' is invalid.");
		}
		$this->application_id = $application_id;
	}

	public function getSourceGuildId() : ?string{
		return $this->source_guild_id;
	}

	public function setSourceGuildId(?string $source_guild_id) : void{
		if($source_guild_id !== null && !Utils::validDiscordSnowflake($source_guild_id)){
			throw new AssertionError("Source Guild ID '$source_guild_id' is invalid.");
		}
		$this->source_guild_id = $source_guild_id;
	}

	public function getSourceChannelId() : ?string{
		return $this->source_channel_id;
	}

	public function setSourceChannelId(?string $source_channel_id) : void{
		if($source_channel_id !== null && !Utils::validDiscordSnowflake($source_channel_id)){
			throw new AssertionError("Source Channel ID '$source_channel_id' is invalid.");
		}
		$this->source_channel_id = $source_channel_id;
	}

	public function getURL() : ?string{
		if($this->token === null){
			return null;
		}
		return "https://discord.com/api/webhooks/{$this->id}/{$this->token}";
	}

	//----- Serialization -----//

	public function binarySerialize() : BinaryStream{
		$stream = new BinaryStream();
		$stream->putByte($this->type->value);
		$stream->putString($this->id);
		$stream->putNullableString($this->guild_id);
		$stream->putNullableString($this->channel_id);
		$stream->putNullableString($this->user_id);
		$stream->putNullableString($this->name);
		$stream->putNullableString($this->avatar);
		$stream->putNullableString($this->token);
		$stream->putNullableString($this->application_id);
		$stream->putNullableString($this->source_guild_id);
		$stream->putNullableString($this->source_channel_id);
		return $stream;
	}

	public static function fromBinary(BinaryStream $stream) : self{
		return new self(
			WebhookType::from($stream->getByte()),
			$stream->getString(),               // id
			$stream->getNullableString(),       // guild_id
			$stream->getNullableString(),       // channel_id
			$stream->getNullableString(),       // user_id
			$stream->getNullableString(),       // name
			$stream->getNullableString(),       // avatar
			$stream->getNullableString(),       // token
			$stream->getNullableString(),       // application_id
			$stream->getNullableString(),       // source_guild_id
			$stream->getNullableString()        // source_channel_id
		);
	}
}
