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
use pocketmine\plugin\Plugin;

/**
 * Emitted when a channel gets updated.
 *
 * @see ChannelDeleted
 * @see ChannelCreated
 */
final class ChannelUpdated extends DiscordBotEvent{

	private Channel $channel;

	/** Old channel if cached. */
	private ?Channel $old_channel;

	public function __construct(Plugin $plugin, Channel $channel, ?Channel $old_channel){
		parent::__construct($plugin);
		if($channel->getType()->isThread()){
			throw new AssertionError("Channel cannot be a thread.");
		}
		if($old_channel !== null && $old_channel->getType()->isThread()){
			throw new AssertionError("Old channel cannot be a thread.");
		}
		$this->channel = $channel;
		$this->old_channel = $old_channel;
	}

	public function getChannel() : Channel{
		return $this->channel;
	}

	public function getOldChannel() : ?Channel{
		return $this->old_channel;
	}
}
