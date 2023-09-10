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

namespace JaxkDev\DiscordBot\Communication\Packets\Discord;

use JaxkDev\DiscordBot\Communication\BinaryStream;
use JaxkDev\DiscordBot\Communication\Packets\Packet;

final class InviteDelete extends Packet{

	public const SERIALIZE_ID = 213;

	private ?string $guild_id;

	private ?string $channel_id;

	private string $invite_code;

	public function __construct(?string $guild_id, ?string $channel_id, string $invite_code, ?int $uid = null){
		parent::__construct($uid);
		$this->guild_id = $guild_id;
		$this->channel_id = $channel_id;
		$this->invite_code = $invite_code;
	}

	public function getGuildId() : ?string{
		return $this->guild_id;
	}

	public function getChannelId() : ?string{
		return $this->channel_id;
	}

	public function getInviteCode() : string{
		return $this->invite_code;
	}

	public function binarySerialize() : BinaryStream{
		$stream = new BinaryStream();
		$stream->putInt($this->getUID());
		$stream->putNullableString($this->guild_id);
		$stream->putNullableString($this->channel_id);
		$stream->putString($this->invite_code);
		return $stream;
	}

	public static function fromBinary(BinaryStream $stream) : self{
		$uid = $stream->getInt();
		return new self(
			$stream->getNullableString(), // guild_id
			$stream->getNullableString(), // channel_id
			$stream->getString(),         // invite_code
			$uid
		);
	}
}
