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

namespace JaxkDev\DiscordBot\Models\Messages;

use JaxkDev\DiscordBot\Communication\BinarySerializable;
use JaxkDev\DiscordBot\Communication\BinaryStream;

/**
 * @implements BinarySerializable<Activity>
 * @link https://discord.com/developers/docs/resources/channel#message-object-message-activity-structure
 */
final class Activity implements BinarySerializable{

	private ActivityType $type;

	private ?string $party_id;

	public function __construct(ActivityType $type, ?string $party_id = null){
		$this->setType($type);
		$this->setPartyId($party_id);
	}

	public function getType() : ActivityType{
		return $this->type;
	}

	public function setType(ActivityType $type) : void{
		$this->type = $type;
	}

	public function getPartyId() : ?string{
		return $this->party_id;
	}

	public function setPartyId(?string $party_id) : void{
		$this->party_id = $party_id;
	}

	public function binarySerialize() : BinaryStream{
		$stream = new BinaryStream();
		$stream->putByte($this->type->value);
		$stream->putNullableString($this->party_id);
		return $stream;
	}

	public static function fromBinary(BinaryStream $stream) : BinarySerializable{
		return new self(
			ActivityType::from($stream->getByte()), // type
			$stream->getNullableString()            // party_id
		);
	}
}
