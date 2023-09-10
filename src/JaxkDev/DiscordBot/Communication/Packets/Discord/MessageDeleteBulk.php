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
use JaxkDev\DiscordBot\Models\Messages\Message;

/*
 * Sent when messages are deleted in bulk.
 * The message deleted will either be an ID in message_ids or a model in messages. (exclusive properties, won't be in both.)
 */
final class MessageDeleteBulk extends Packet{

	public const SERIALIZE_ID = 218;

	/** @var string[] IDs of remaining message that we don't have cached. */
	private array $message_ids;

	/** @var Message[] Messages deleted (models from cache otherwise it will be in message_ids). */
	private array $messages;

	/** ID of the channel */
	private string $channel_id;

	/** ID of the guild */
	private ?string $guild_id;

	/**
	 * @param string[]  $message_ids
	 * @param Message[] $messages
	 */
	public function __construct(array $message_ids, array $messages, string $channel_id, ?string $guild_id, ?int $uid = null){
		parent::__construct($uid);
		$this->message_ids = $message_ids;
		$this->messages = $messages;
		$this->channel_id = $channel_id;
		$this->guild_id = $guild_id;
	}

	/** @return string[] */
	public function getMessageIds() : array{
		return $this->message_ids;
	}

	/** @return Message[] */
	public function getMessages() : array{
		return $this->messages;
	}

	public function getChannelId() : string{
		return $this->channel_id;
	}

	public function getGuildId() : ?string{
		return $this->guild_id;
	}

	public function binarySerialize() : BinaryStream{
		$stream = new BinaryStream();
		$stream->putInt($this->getUID());
		$stream->putStringArray($this->message_ids);
		$stream->putSerializableArray($this->messages);
		$stream->putString($this->channel_id);
		$stream->putNullableString($this->guild_id);
		return $stream;
	}

	public static function fromBinary(BinaryStream $stream) : self{
		$uid = $stream->getInt();
		return new self(
			$stream->getStringArray(),                     // message_ids
			$stream->getSerializableArray(Message::class), // messages
			$stream->getString(),                          // channel_id
			$stream->getNullableString(),                  // guild_id
			$uid
		);
	}
}
