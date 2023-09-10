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

namespace JaxkDev\DiscordBot\Plugin\Events;

use AssertionError;
use JaxkDev\DiscordBot\Models\Messages\Message;
use JaxkDev\DiscordBot\Plugin\Utils;
use pocketmine\plugin\Plugin;

/**
 * Emitted when multiple messages have been deleted at once (in bulk).
 *
 * If message was made/updated before bot started it will only have message id listed in $message_ids.
 * If it was made/updated after bot started it may have the full message model (if cached) in $messages.
 *
 * @see MessageUpdated
 * @see MessageSent
 * @see MessageDeletd
 */
final class MessagesBulkDeleted extends DiscordBotEvent{

	private ?string $guild_id;

	private string $channel_id;

	/** @var string[] Message deleted will either be just its ID here, or model in $messages, never both. */
	private array $message_ids;

	/** @var Message[] */
	private array $messages;

	/**
	 * @param string[]  $message_ids
	 * @param Message[] $messages
	 */
	public function __construct(Plugin $plugin, ?string $guild_id, string $channel_id, array $message_ids, array $messages){
		parent::__construct($plugin);
		if($guild_id !== null && !Utils::validDiscordSnowflake($guild_id)){
			throw new AssertionError("Invalid guild ID given.");
		}
		if(!Utils::validDiscordSnowflake($channel_id)){
			throw new AssertionError("Invalid channel ID given.");
		}
		foreach($message_ids as $message_id){
			if(!Utils::validDiscordSnowflake($message_id)){
				throw new AssertionError("Invalid message ID given.");
			}
		}
		foreach($messages as $message){
			if(!$message instanceof Message){
				throw new AssertionError("Invalid message model given.");
			}
		}
		$this->guild_id = $guild_id;
		$this->channel_id = $channel_id;
		$this->message_ids = $message_ids;
		$this->messages = $messages;
	}

	public function getGuildId() : ?string{
		return $this->guild_id;
	}

	public function getChannelId() : string{
		return $this->channel_id;
	}

	/** @return string[] */
	public function getMessageIds() : array{
		return $this->message_ids;
	}

	/** @return Message[] */
	public function getMessages() : array{
		return $this->messages;
	}
}
