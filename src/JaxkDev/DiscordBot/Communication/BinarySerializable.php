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

namespace JaxkDev\DiscordBot\Communication;

use pocketmine\utils\BinaryDataException;

/** @template-covariant T */
interface BinarySerializable{

	/**
	 * All serializable CLASSES (not enums) must have a unique ID to identify them.
	 * IDs must be unique, and must not be changed.
	 * Modifying this value will break compatibility with other versions.
	 * @var int<0, 65535>
	 * @internal
	 */
	public const SERIALIZE_ID = 0;

	/**
	 * @internal
	 * @throws BinaryDataException If the packet data is invalid, should never happen.
	 */
	public function binarySerialize() : BinaryStream;

	/**
	 * @internal
	 * @return BinarySerializable<T>
	 * @throws BinaryDataException If the packet data is invalid, may happen on external thread inbound.
	 */
	public static function fromBinary(BinaryStream $stream) : self;
}
