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
use JaxkDev\DiscordBot\Models\Presence\Presence;
use JaxkDev\DiscordBot\Plugin\Utils;
use pocketmine\plugin\Plugin;

/**
 * Emitted when a members presence is updated.
 */
final class PresenceUpdated extends DiscordBotEvent{

	private string $guild_id;

	private string $user_id;

	private Presence $new_presence;

	public function __construct(Plugin $plugin, string $guild_id, string $user_id, Presence $new_presence){
		parent::__construct($plugin);
		if(!Utils::validDiscordSnowflake($guild_id)){
			throw new AssertionError("Invalid guild ID given.");
		}
		if(!Utils::validDiscordSnowflake($user_id)){
			throw new AssertionError("Invalid user ID given.");
		}
		$this->guild_id = $guild_id;
		$this->user_id = $user_id;
		$this->new_presence = $new_presence;
	}

	public function getGuildId() : string{
		return $this->guild_id;
	}

	public function getUserId() : string{
		return $this->user_id;
	}

	public function getNewPresence() : Presence{
		return $this->new_presence;
	}
}
