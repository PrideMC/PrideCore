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
 * @implements BinarySerializable<Ban>
 * @link https://discord.com/developers/docs/resources/guild#ban-object
 */
final class Ban implements BinarySerializable{

	public const SERIALIZE_ID = 4;

	/** Guild the user is banned from */
	private string $guild_id;

	/** The banned user */
	private string $user_id;

	/** The reason for the ban */
	private ?string $reason;

	/**
	 * @internal See API::banMember()
	 * @see API::banMember()
	 */
	public function __construct(string $guild_id, string $user_id, ?string $reason = null){
		$this->setGuildId($guild_id);
		$this->setUserId($user_id);
		$this->setReason($reason);
	}

	public function getId() : string{
		return $this->guild_id . "." . $this->user_id;
	}

	public function getGuildId() : string{
		return $this->guild_id;
	}

	public function setGuildId(string $guild_id) : void{
		if(!Utils::validDiscordSnowflake($guild_id)){
			throw new AssertionError("Guild ID '$guild_id' is invalid.");
		}
		$this->guild_id = $guild_id;
	}

	public function getUserId() : string{
		return $this->user_id;
	}

	public function setUserId(string $user_id) : void{
		if(!Utils::validDiscordSnowflake($user_id)){
			throw new AssertionError("User ID '$user_id' is invalid.");
		}
		$this->user_id = $user_id;
	}

	public function getReason() : ?string{
		return $this->reason;
	}

	public function setReason(?string $reason) : void{
		$this->reason = $reason;
	}

	//----- Serialization -----//

	public function binarySerialize() : BinaryStream{
		$stream = new BinaryStream();
		$stream->putString($this->guild_id);
		$stream->putString($this->user_id);
		$stream->putNullableString($this->reason);
		return $stream;
	}

	public static function fromBinary(BinaryStream $stream) : self{
		return new self(
			$stream->getString(),           // guild_id
			$stream->getString(),           // user_id
			$stream->getNullableString()    // reason
		);
	}
}
