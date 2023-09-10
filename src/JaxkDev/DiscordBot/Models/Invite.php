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
 * @implements BinarySerializable<Invite>
 * @link https://discord.com/developers/docs/resources/invite#invite-object
 */
final class Invite implements BinarySerializable{

	public const SERIALIZE_ID = 6;

	/** Also used as ID internally. */
	private string $code;

	/** The guild this invite is for (if any) */
	private ?string $guild_id;

	/** The channel this invite is for */
	private string $channel_id;

	/** The user (ID) who created the invite */
	private ?string $inviter;

	/** The type of target for this voice channel invite */
	private ?InviteTargetType $target_type;

	/** The user (ID) whose stream to display for this voice channel stream invite */
	private ?string $target_user;

	/** The expiration date of this invite. (UNIX Timestamp) */
	private ?int $expires_at;

	public function __construct(string $code, ?string $guild_id, string $channel_id, ?string $inviter,
								?InviteTargetType $target_type, ?string $target_user, ?int $expires_at){
		$this->setCode($code);
		$this->setGuildId($guild_id);
		$this->setChannelId($channel_id);
		$this->setInviter($inviter);
		$this->setTargetType($target_type);
		$this->setTargetUser($target_user);
		$this->setExpiresAt($expires_at);
	}

	public function getCode() : string{
		return $this->code;
	}

	public function setCode(string $code) : void{
		$this->code = $code;
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

	public function getChannelId() : string{
		return $this->channel_id;
	}

	public function setChannelId(string $channel_id) : void{
		if(!Utils::validDiscordSnowflake($channel_id)){
			throw new AssertionError("Channel ID '$channel_id' is invalid.");
		}
		$this->channel_id = $channel_id;
	}

	public function getInviter() : ?string{
		return $this->inviter;
	}

	public function setInviter(?string $inviter) : void{
		if($inviter !== null && !Utils::validDiscordSnowflake($inviter)){
			throw new AssertionError("Inviter ID '$inviter' is invalid.");
		}
		$this->inviter = $inviter;
	}

	public function getTargetType() : ?InviteTargetType{
		return $this->target_type;
	}

	public function setTargetType(?InviteTargetType $target_type) : void{
		$this->target_type = $target_type;
	}

	public function getTargetUser() : ?string{
		return $this->target_user;
	}

	public function setTargetUser(?string $target_user) : void{
		if($target_user !== null && !Utils::validDiscordSnowflake($target_user)){
			throw new AssertionError("Target user ID '$target_user' is invalid.");
		}
		$this->target_user = $target_user;
	}

	public function getExpiresAt() : ?int{
		return $this->expires_at;
	}

	public function setExpiresAt(?int $expires_at) : void{
		$this->expires_at = $expires_at;
	}

	public function getUrl() : string{
		return "https://discord.gg/{$this->code}";
	}

	//----- Serialization -----//

	public function binarySerialize() : BinaryStream{
		$stream = new BinaryStream();
		$stream->putString($this->code);
		$stream->putNullableString($this->guild_id);
		$stream->putString($this->channel_id);
		$stream->putNullableString($this->inviter);
		$stream->putNullableByte($this->target_type?->value);
		$stream->putNullableString($this->target_user);
		$stream->putNullableLong($this->expires_at);
		return $stream;
	}

	public static function fromBinary(BinaryStream $stream) : self{
		return new self(
			$stream->getString(),                                       // code
			$stream->getNullableString(),                               // guild_id
			$stream->getString(),                                       // channel_id
			$stream->getNullableString(),                               // inviter
			InviteTargetType::tryFrom($stream->getNullableByte() ?? -1),// target_type
			$stream->getNullableString(),                               // target_user
			$stream->getNullableLong()                                  // expires_at
		);
	}
}
