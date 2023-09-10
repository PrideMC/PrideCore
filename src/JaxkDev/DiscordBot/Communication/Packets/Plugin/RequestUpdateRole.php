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

namespace JaxkDev\DiscordBot\Communication\Packets\Plugin;

use JaxkDev\DiscordBot\Communication\BinaryStream;
use JaxkDev\DiscordBot\Communication\Packets\Packet;
use JaxkDev\DiscordBot\Models\Role;

final class RequestUpdateRole extends Packet{

	public const SERIALIZE_ID = 447;

	private Role $role;

	/** @var string|null If changing icon, set this to validImageData. */
	private ?string $new_icon_data;

	private ?string $reason;

	public function __construct(Role $role, ?string $new_icon_data, ?string $reason = null, ?int $uid = null){
		parent::__construct($uid);
		$this->role = $role;
		$this->new_icon_data = $new_icon_data;
		$this->reason = $reason;
	}

	public function getRole() : Role{
		return $this->role;
	}

	public function getNewIconData() : ?string{
		return $this->new_icon_data;
	}

	public function getReason() : ?string{
		return $this->reason;
	}

	public function binarySerialize() : BinaryStream{
		$stream = new BinaryStream();
		$stream->putInt($this->getUID());
		$stream->putSerializable($this->role);
		$stream->putNullableString($this->new_icon_data);
		$stream->putNullableString($this->reason);
		return $stream;
	}

	public static function fromBinary(BinaryStream $stream) : self{
		$uid = $stream->getInt();
		return new self(
			$stream->getSerializable(Role::class), // role
			$stream->getNullableString(),          // new_icon_data
			$stream->getNullableString(),          // reason
			$uid
		);
	}
}
