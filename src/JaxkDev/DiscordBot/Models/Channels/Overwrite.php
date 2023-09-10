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

namespace JaxkDev\DiscordBot\Models\Channels;

use AssertionError;
use JaxkDev\DiscordBot\Communication\BinarySerializable;
use JaxkDev\DiscordBot\Communication\BinaryStream;
use JaxkDev\DiscordBot\Models\Permissions\ChannelPermissions;
use JaxkDev\DiscordBot\Models\Permissions\RolePermissions;
use JaxkDev\DiscordBot\Plugin\Utils;

/**
 * @implements BinarySerializable<Overwrite>
 * @link https://discord.com/developers/docs/resources/channel#overwrite-object-overwrite-structure
 */
final class Overwrite implements BinarySerializable{

	/** Role or user id */
	private string $id;

	private OverwriteType $type;

	private ChannelPermissions|RolePermissions $allow;
	private ChannelPermissions|RolePermissions $deny;

	public function __construct(string $id, OverwriteType $type, ChannelPermissions|RolePermissions $allow,
								ChannelPermissions|RolePermissions $deny){
		$this->id = $id;
		$this->type = $type;
		$this->allow = $allow;
		$this->deny = $deny;
	}

	public function getId() : string{
		return $this->id;
	}

	public function setId(string $id) : void{
		if(!Utils::validDiscordSnowflake($id)){
			throw new AssertionError("Invalid ID provided.");
		}
		$this->id = $id;
	}

	public function getType() : OverwriteType{
		return $this->type;
	}

	public function setType(OverwriteType $type) : void{
		$this->type = $type;
	}

	public function getAllow() : ChannelPermissions|RolePermissions{
		return $this->allow;
	}

	public function setAllow(ChannelPermissions|RolePermissions $allow) : void{
		$this->allow = $allow;
	}

	public function getDeny() : ChannelPermissions|RolePermissions{
		return $this->deny;
	}

	public function setDeny(ChannelPermissions|RolePermissions $deny) : void{
		$this->deny = $deny;
	}

	public function binarySerialize() : BinaryStream{
		$stream = new BinaryStream();
		$stream->putString($this->id);
		$stream->putByte($this->type->value);
		$stream->putSerializable($this->allow);
		$stream->putSerializable($this->deny);
		return $stream;
	}

	public static function fromBinary(BinaryStream $stream) : BinarySerializable{
		return new self(
			$stream->getString(),
			($t = OverwriteType::from($stream->getByte())),
			$stream->getSerializable($t === OverwriteType::ROLE ? RolePermissions::class : ChannelPermissions::class),
			$stream->getSerializable($t === OverwriteType::ROLE ? RolePermissions::class : ChannelPermissions::class)
		);
	}
}
