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
use JaxkDev\DiscordBot\Models\Channels\Channel;
use JaxkDev\DiscordBot\Models\Channels\ChannelType;

final class ThreadDelete extends Packet{

	public const SERIALIZE_ID = 230;

	private ChannelType $type;

	private string $id;

	private string $guild_id;

	private string $parent_id;

	private ?Channel $cached_thread = null;

	public function __construct(ChannelType $type, string $id, string $guild_id, string $parent_id,
								?Channel $cached_thread, ?int $uid = null){
		parent::__construct($uid);
		$this->type = $type;
		$this->id = $id;
		$this->guild_id = $guild_id;
		$this->parent_id = $parent_id;
		$this->cached_thread = $cached_thread;
	}

	public function getType() : ChannelType{
		return $this->type;
	}

	public function getId() : string{
		return $this->id;
	}

	public function getGuildId() : string{
		return $this->guild_id;
	}

	public function getParentId() : string{
		return $this->parent_id;
	}

	public function getCachedThread() : ?Channel{
		return $this->cached_thread;
	}

	public function binarySerialize() : BinaryStream{
		$stream = new BinaryStream();
		$stream->putInt($this->getUID());
		$stream->putByte($this->type->value);
		$stream->putString($this->id);
		$stream->putString($this->guild_id);
		$stream->putString($this->parent_id);
		$stream->putNullableSerializable($this->cached_thread);
		return $stream;
	}

	public static function fromBinary(BinaryStream $stream) : self{
		$uid = $stream->getInt();
		return new self(
			ChannelType::from($stream->getByte()),            // type
			$stream->getString(),                             // id
			$stream->getString(),                             // guild_id
			$stream->getString(),                             // parent_id
			$stream->getNullableSerializable(Channel::class), // cached_thread
			$uid
		);
	}
}
