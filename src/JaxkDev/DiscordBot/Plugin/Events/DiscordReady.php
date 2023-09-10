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

use JaxkDev\DiscordBot\Models\Presence\Activity\Activity;
use JaxkDev\DiscordBot\Models\Presence\Status;
use JaxkDev\DiscordBot\Models\User;
use pocketmine\plugin\Plugin;

/**
 * DiscordBot has connected, and we are now in contact with discord.
 * You can now use the API.
 *
 * @see DiscordClosed Emitted when DiscordBot disconnects.
 */
final class DiscordReady extends DiscordBotEvent{

	private User $bot_user;

	private Activity $activity;

	private Status $status;

	public function __construct(Plugin $plugin, User $bot_user, Activity $activity, Status $status){
		parent::__construct($plugin);
		$this->bot_user = $bot_user;
		$this->activity = $activity;
		$this->status = $status;
	}

	public function getBotUser() : User{
		return $this->bot_user;
	}

	public function getActivity() : Activity{
		return $this->activity;
	}

	public function setActivity(Activity $activity) : void{
		$this->activity = $activity;
	}

	public function getStatus() : Status{
		return $this->status;
	}

	public function setStatus(Status $status) : void{
		$this->status = $status;
	}
}
