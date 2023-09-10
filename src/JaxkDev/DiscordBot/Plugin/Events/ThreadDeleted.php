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
use JaxkDev\DiscordBot\Models\Channels\Channel;
use JaxkDev\DiscordBot\Models\Channels\ChannelType;
use JaxkDev\DiscordBot\Plugin\Utils;
use pocketmine\plugin\Plugin;

/**
 * Emitted when a Thread gets created.
 *
 * @see ThreadCreatedEvent
 * @see ThreadUpdated
 */
final class ThreadDeleted extends DiscordBotEvent{

	private ChannelType $type;

	private string $id;

	private string $guild_id;

	private string $parent_id;

	private ?Channel $cached_thread;

	public function __construct(Plugin $plugin, ChannelType $type, string $id, string $guild_id, string $parent_id, ?Channel $cached_thread){
		parent::__construct($plugin);
		if(!$type->isThread()){
			throw new AssertionError("Channel must be a thread.");
		}
		if(!Utils::validDiscordSnowflake($id)){
			throw new AssertionError("Invalid id given.");
		}
		if(!Utils::validDiscordSnowflake($guild_id)){
			throw new AssertionError("Invalid guild_id given.");
		}
		if(!Utils::validDiscordSnowflake($parent_id)){
			throw new AssertionError("Invalid parent_id given.");
		}
		if($cached_thread !== null && !$cached_thread->getType()->isThread()){
			throw new AssertionError("Cached thread must be a thread or null.");
		}
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
}
