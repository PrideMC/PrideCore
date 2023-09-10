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
 * Emitted when a message has been deleted.
 *
 * If message was made/updated before bot started it will only have message id, channel id and guild id.
 * If it was made/updated after bot started it may have the full message model (if cached).
 *
 * @see MessageUpdated
 * @see MessageSent
 */
final class MessageDeleted extends DiscordBotEvent{

	private ?string $guild_id;

	private string $channel_id;

	private string $message_id;

	private ?Message $cached_message;

	public function __construct(Plugin $plugin, ?string $guild_id, string $channel_id, string $message_id,
								?Message $cached_message){
		parent::__construct($plugin);
		if($guild_id !== null && !Utils::validDiscordSnowflake($guild_id)){
			throw new AssertionError("Invalid guild_id given.");
		}
		if(!Utils::validDiscordSnowflake($channel_id)){
			throw new AssertionError("Invalid channel_id given.");
		}
		if(!Utils::validDiscordSnowflake($message_id)){
			throw new AssertionError("Invalid message_id given.");
		}
		$this->guild_id = $guild_id;
		$this->channel_id = $channel_id;
		$this->message_id = $message_id;
		$this->cached_message = $cached_message;
	}

	public function getGuildId() : ?string{
		return $this->guild_id;
	}

	public function getChannelId() : string{
		return $this->channel_id;
	}

	public function getMessageId() : string{
		return $this->message_id;
	}

	public function getCachedMessage() : ?Message{
		return $this->cached_message;
	}
}
