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

namespace JaxkDev\DiscordBot\Models\Interactions;

use AssertionError;
use JaxkDev\DiscordBot\Communication\BinarySerializable;
use JaxkDev\DiscordBot\Communication\BinaryStream;
use JaxkDev\DiscordBot\Models\Messages\Message;
use JaxkDev\DiscordBot\Models\Permissions\ChannelPermissions;
use JaxkDev\DiscordBot\Plugin\Utils;

/**
 * @implements BinarySerializable<Interaction>
 * @link https://discord.com/developers/docs/interactions/receiving-and-responding#interaction-object-interaction-structure
 */
final class Interaction implements BinarySerializable{

	/** ID of the interaction */
	private string $id;

	/** ID of the application this interaction is for */
	private string $application_id;

	/** The type of interaction */
	private InteractionType $type;

	/**
	 * The command data payload, data type dependent on interaction type.
	 * ApplicationCommandData for InteractionType::APPLICATION_COMMAND and InteractionType::APPLICATION_COMMAND_AUTOCOMPLETE
	 * MessageComponentData for InteractionType::MESSAGE_COMPONENT
	 * ModalSubmitData for InteractionType::MODAL_SUBMIT
	 * null for InteractionType::PING
	 */
	private ApplicationCommandData|MessageComponentData|ModalSubmitData|null $data;

	/** The guild it was sent from */
	private ?string $guild_id;

	/** The channel it was sent from */
	private ?string $channel_id;

	/** member/user ID of the invoking user. */
	private ?string $user_id;

	/**
	 * A continuation token for responding to the interaction, internal use only.
	 * @internal
	 */
	private string $token;

	/**
	 * Read-only property, always 1 (from discord gateway)
	 * @internal
	 */
	private int $version;

	/** For MESSAGE_COMPONENT type, the original message the component is attached to */
	private ?Message $message;

	/** Set of permissions the app or bot has within the channel the interaction was sent from */
	private ?ChannelPermissions $permissions;

	/** Selected language of the invoking user, only null on type PING. */
	private ?string $locale;

	/** Guild's preferred locale, if invoked in a guild */
	private ?string $guild_locale;

	/**
	 * Whether the response to this interaction has been deferred/responded to already.
	 * Not serialised / sent over network protocol.
	 */
	private bool $responded = false;

	public function __construct(string $id, string $application_id, InteractionType $type,
								ApplicationCommandData|MessageComponentData|ModalSubmitData|null $data, ?string $guild_id,
								?string $channel_id, ?string $user_id, string $token, int $version, ?Message $message,
								?ChannelPermissions $permissions, ?string $locale, ?string $guild_locale){
		$this->setId($id);
		$this->setApplicationId($application_id);
		$this->setType($type);
		$this->setData($data);
		$this->setGuildId($guild_id);
		$this->setChannelId($channel_id);
		$this->setUserId($user_id);
		$this->token = $token;
		$this->version = $version;
		$this->setMessage($message);
		$this->setPermissions($permissions);
		$this->setLocale($locale);
		$this->setGuildLocale($guild_locale);
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

	public function getApplicationId() : string{
		return $this->application_id;
	}

	public function setApplicationId(string $application_id) : void{
		if(!Utils::validDiscordSnowflake($application_id)){
			throw new AssertionError("Application ID '$application_id' is invalid.");
		}
		$this->application_id = $application_id;
	}

	public function getType() : InteractionType{
		return $this->type;
	}

	public function setType(InteractionType $type) : void{
		$this->type = $type;
	}

	public function getData() : ApplicationCommandData|MessageComponentData|ModalSubmitData|null{
		return $this->data;
	}

	public function setData(ApplicationCommandData|MessageComponentData|ModalSubmitData|null $data) : void{
		if($data instanceof ApplicationCommandData && $this->type !== InteractionType::APPLICATION_COMMAND && $this->type !== InteractionType::APPLICATION_COMMAND_AUTOCOMPLETE){
			throw new AssertionError("Invalid data type ApplicationCommandData for interaction type '" . $this->type->name . "'.");
		}
		if($data instanceof MessageComponentData && $this->type !== InteractionType::MESSAGE_COMPONENT){
			throw new AssertionError("Invalid data type MessageComponentData for interaction type '" . $this->type->name . "'.");
		}
		if($data instanceof ModalSubmitData && $this->type !== InteractionType::MODAL_SUBMIT){
			throw new AssertionError("Invalid data type ModalSubmitData for interaction type '" . $this->type->name . "'.");
		}
		if($data === null && $this->type !== InteractionType::PING){
			throw new AssertionError("Invalid data type null for interaction type '" . $this->type->name . "'.");
		}
		$this->data = $data;
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

	public function getToken() : string{
		return $this->token;
	}

	public function getVersion() : int{
		return $this->version;
	}

	public function getMessage() : ?Message{
		return $this->message;
	}

	public function setMessage(?Message $message) : void{
		$this->message = $message;
	}

	public function getPermissions() : ?ChannelPermissions{
		return $this->permissions;
	}

	public function setPermissions(?ChannelPermissions $permissions) : void{
		$this->permissions = $permissions;
	}

	public function getLocale() : ?string{
		return $this->locale;
	}

	public function setLocale(?string $locale) : void{
		$this->locale = $locale;
	}

	public function getGuildLocale() : ?string{
		return $this->guild_locale;
	}

	public function setGuildLocale(?string $guild_locale) : void{
		$this->guild_locale = $guild_locale;
	}

	public function getResponded() : bool{
		return $this->responded;
	}

	public function setResponded() : void{
		$this->responded = true;
	}

	public function binarySerialize() : BinaryStream{
		$stream = new BinaryStream();
		$stream->putString($this->id);
		$stream->putString($this->application_id);
		$stream->putByte($this->type->value);
		$stream->putNullableSerializable($this->data);
		$stream->putNullableString($this->guild_id);
		$stream->putNullableString($this->channel_id);
		$stream->putNullableString($this->user_id);
		$stream->putString($this->token);
		$stream->putByte($this->version);
		$stream->putNullableSerializable($this->message);
		$stream->putNullableSerializable($this->permissions);
		$stream->putNullableString($this->locale);
		$stream->putNullableString($this->guild_locale);
		return $stream;
	}

	public static function fromBinary(BinaryStream $stream) : self{
		return new self(
			$stream->getString(),
			$stream->getString(),
			($t = InteractionType::from($stream->getByte())),
			//Nullable serializable array (data type dependant on type^)
			match($stream->getBool()) { //null or not
				true => match ($t) { //get correct data type
					InteractionType::APPLICATION_COMMAND, InteractionType::APPLICATION_COMMAND_AUTOCOMPLETE => ApplicationCommandData::fromBinary($stream),
					InteractionType::MESSAGE_COMPONENT => MessageComponentData::fromBinary($stream),
					InteractionType::MODAL_SUBMIT => ModalSubmitData::fromBinary($stream),
					default => throw new AssertionError("Invalid interaction data for type.")
				},
				false => null
			},
			$stream->getNullableString(),
			$stream->getNullableString(),
			$stream->getNullableString(),
			$stream->getString(),
			$stream->getByte(),
			$stream->getNullableSerializable(Message::class),
			$stream->getNullableSerializable(ChannelPermissions::class),
			$stream->getNullableString(),
			$stream->getNullableString()
		);
	}
}
