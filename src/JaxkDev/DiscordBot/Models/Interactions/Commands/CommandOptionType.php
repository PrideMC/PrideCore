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

namespace JaxkDev\DiscordBot\Models\Interactions\Commands;

/** @link https://discord.com/developers/docs/interactions/application-commands#application-command-object-application-command-option-type */
enum CommandOptionType : int{

	case SUB_COMMAND = 1;
	case SUB_COMMAND_GROUP = 2;
	case STRING = 3;
	/** Any integer between -2^53 and 2^53 */
	case INTEGER = 4;
	case BOOLEAN = 5;
	case USER = 6;
	/** Includes all channel types + categories */
	case CHANNEL = 7;
	case ROLE = 8;
	/** Includes users and roles */
	case MENTIONABLE = 9;
	/** Any double between -2^53 and 2^53 */
	case NUMBER = 10;
	/** Attachment object */
	case ATTACHMENT = 11;
}
