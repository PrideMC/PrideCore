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

use JaxkDev\DiscordBot\Models\Guild\Guild;
use pocketmine\plugin\Plugin;

/**
 * Emitted when a guild the bot is in has been updated, eg Changed icon, name, region etc.
 *
 * @see GuildDeleted Emitted when the bot leaves a guild
 * @see GuildJoined Emitted when the bot joins a guild.
 */
final class GuildUpdated extends DiscordBotEvent{

	private Guild $guild;

	/** Old guild if cached. */
	private ?Guild $old_guild;

	public function __construct(Plugin $plugin, Guild $guild, ?Guild $old_guild){
		parent::__construct($plugin);
		$this->guild = $guild;
		$this->old_guild = $old_guild;
	}

	public function getGuild() : Guild{
		return $this->guild;
	}

	public function getOldGuild() : ?Guild{
		return $this->old_guild;
	}
}
