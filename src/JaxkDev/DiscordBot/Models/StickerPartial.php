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

namespace JaxkDev\DiscordBot\Models;

use AssertionError;
use JaxkDev\DiscordBot\Communication\BinarySerializable;
use JaxkDev\DiscordBot\Communication\BinaryStream;
use JaxkDev\DiscordBot\Plugin\Utils;

/**
 * Partial sticker object.
 *
 * @implements BinarySerializable<StickerPartial>
 * @link https://discord.com/developers/docs/resources/sticker#sticker-item-object-sticker-item-structure
 */
final class StickerPartial implements BinarySerializable{

	private string $id;

	private string $name;

	private StickerFormatType $format_type;

	public function __construct(string $id, string $name, StickerFormatType $format_type){
		$this->setId($id);
		$this->setName($name);
		$this->setFormatType($format_type);
	}

	public function getId() : string{
		return $this->id;
	}

	public function setId(string $id) : void{
		if(!Utils::validDiscordSnowflake($id)){
			throw new AssertionError("StickerPartial ID '$id' is invalid.");
		}
		$this->id = $id;
	}

	public function getName() : string{
		return $this->name;
	}

	public function setName(string $name) : void{
		$this->name = $name;
	}

	public function getFormatType() : StickerFormatType{
		return $this->format_type;
	}

	public function setFormatType(StickerFormatType $format_type) : void{
		$this->format_type = $format_type;
	}

	public function binarySerialize() : BinaryStream{
		$stream = new BinaryStream();
		$stream->putString($this->id);
		$stream->putString($this->name);
		$stream->putByte($this->format_type->value);
		return $stream;
	}

	public static function fromBinary(BinaryStream $stream) : self{
		return new self(
			$stream->getString(),                       // id
			$stream->getString(),                       // name
			StickerFormatType::from($stream->getByte()) // format_type
		);
	}
}
