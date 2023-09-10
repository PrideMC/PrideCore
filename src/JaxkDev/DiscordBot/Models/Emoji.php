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
use JaxkDev\DiscordBot\Plugin\Utils;

/**
 * @implements BinarySerializable<Emoji>
 * @link https://discord.com/developers/docs/resources/emoji#emoji-object
 */
final class Emoji implements BinarySerializable{

	public const SERIALIZE_ID = 5;

	/** Emoji ID */
	private ?string $id;

	/**
	 * Emoji name.
	 *
	 * Can only be null for reaction events:
	 * "In MESSAGE_REACTION_ADD and MESSAGE_REACTION_REMOVE events name may be null when custom emoji data is
	 * not available (for example, if it was deleted from the guild)."
	 */
	private ?string $name;

	/**
	 * Roles allowed to use this emoji, array of IDs
	 * @var string[]
	 */
	private ?array $role_ids;

	/** User that created this emoji */
	private ?string $user_id;

	/** Whether this emoji must be wrapped in colons */
	private ?bool $require_colons;

	/** Whether this emoji is managed */
	private ?bool $managed;

	/** Whether this emoji is animated */
	private ?bool $animated;

	/** Whether this emoji can be used, may be false due to loss of Server Boosts */
	private ?bool $available;

	//TODO-Next-Minor support for create/update/delete emoji.

	/**
	 * Use this for creating standard Emoji objects to use with the API.
	 *
	 * @param string $unicode Raw emoji string eg ✅ or 🤔 or 🤖 etc etc.
	 */
	public static function fromUnicode(string $unicode) : self{
		return new self(null, $unicode, null, null, null, null, null, null);
	}

	/**
	 * Use this for creating an Emoji object from a custom emoji ID and Name inside a guild.
	 */
	public static function fromPrivate(string $id, string $name, ?bool $animated = null) : self{
		return new self($id, $name, null, null, null, null, $animated, null);
	}

	/** @param ?string[] $role_ids Role IDs */
	public function __construct(?string $id, ?string $name, ?array $role_ids, ?string $user_id, ?bool $require_colons,
								?bool $managed, ?bool $animated, ?bool $available){
		$this->setId($id);
		$this->setName($name);
		$this->setRoleIds($role_ids);
		$this->setUserId($user_id);
		$this->setRequireColons($require_colons);
		$this->setManaged($managed);
		$this->setAnimated($animated);
		$this->setAvailable($available);
	}

	public function getUrl() : ?string{
		if($this->id === null){
			return null;
		}
		return "https://cdn.discordapp.com/emojis/{$this->id}." . ($this->animated ?? false ? "gif" : "png");
	}

	public function getId() : ?string{
		return $this->id;
	}

	public function setId(?string $id) : void{
		if($id !== null && !Utils::validDiscordSnowflake($id)){
			throw new AssertionError("Emoji ID '$id' is invalid.");
		}
		$this->id = $id;
	}

	public function getName() : ?string{
		return $this->name;
	}

	public function setName(?string $name) : void{
		$this->name = $name;
	}

	/** @return ?string[] */
	public function getRoleIds() : ?array{
		return $this->role_ids;
	}

	/** @param ?string[] $role_ids */
	public function setRoleIds(?array $role_ids) : void{
		if($role_ids !== null){
			foreach($role_ids as $role_id){
				if(!Utils::validDiscordSnowflake($role_id)){
					throw new AssertionError("Emoji role ID '$role_id' is invalid.");
				}
			}
		}
		$this->role_ids = $role_ids;
	}

	public function getUserId() : ?string{
		return $this->user_id;
	}

	public function setUserId(?string $user_id) : void{
		if($user_id !== null && !Utils::validDiscordSnowflake($user_id)){
			throw new AssertionError("Emoji user ID '$user_id' is invalid.");
		}
		$this->user_id = $user_id;
	}

	public function getRequireColons() : ?bool{
		return $this->require_colons;
	}

	public function setRequireColons(?bool $require_colons) : void{
		$this->require_colons = $require_colons;
	}

	public function getManaged() : ?bool{
		return $this->managed;
	}

	public function setManaged(?bool $managed) : void{
		$this->managed = $managed;
	}

	public function getAnimated() : ?bool{
		return $this->animated;
	}

	public function setAnimated(?bool $animated) : void{
		$this->animated = $animated;
	}

	public function getAvailable() : ?bool{
		return $this->available;
	}

	public function setAvailable(?bool $available) : void{
		$this->available = $available;
	}

	/** @internal Used by the network protocol to send emoji. */
	public function toApiString() : string{
		if($this->id === null){
			return $this->name ?? "";
		}
		return (($this->animated ?? false) ? "a" : "") . ":{$this->name}:{$this->id}";
	}

	public function __toString() : string{
		if($this->id === null){
			return $this->name ?? "";
		}
		return "<" . (($this->animated ?? false) ? "a" : "") . ":{$this->name}:{$this->id}>";
	}

	//----- Serialization -----//

	public function binarySerialize() : BinaryStream{
		$stream = new BinaryStream();
		$stream->putNullableString($this->id);
		$stream->putNullableString($this->name);
		$stream->putNullableStringArray($this->role_ids);
		$stream->putNullableString($this->user_id);
		$stream->putNullableBool($this->require_colons);
		$stream->putNullableBool($this->managed);
		$stream->putNullableBool($this->animated);
		$stream->putNullableBool($this->available);
		return $stream;
	}

	public static function fromBinary(BinaryStream $stream) : self{
		return new self(
			$stream->getNullableString(),      // id
			$stream->getNullableString(),      // name
			$stream->getNullableStringArray(), // role_ids
			$stream->getNullableString(),      // user_id
			$stream->getNullableBool(),        // require_colons
			$stream->getNullableBool(),        // managed
			$stream->getNullableBool(),        // animated
			$stream->getNullableBool()         // available
		);
	}
}
