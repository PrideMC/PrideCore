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

namespace JaxkDev\DiscordBot\Models\Messages\Component;

use AssertionError;
use JaxkDev\DiscordBot\Communication\BinarySerializable;
use JaxkDev\DiscordBot\Communication\BinaryStream;
use JaxkDev\DiscordBot\Models\Emoji;
use function strlen;

/**
 * @implements BinarySerializable<SelectOption>
 * @link https://discord.com/developers/docs/interactions/message-components#select-menu-object-select-option-structure
 */
final class SelectOption implements BinarySerializable{

	/** Max 100 characters */
	private string $label;

	/** Max 100 characters */
	private string $value;

	/** Max 100 characters */
	private ?string $description;

	private ?Emoji $emoji;

	private ?bool $default;

	public function __construct(string $label, string $value, ?string $description = null, ?Emoji $emoji = null,
								?bool $default = null){
		$this->setLabel($label);
		$this->setValue($value);
		$this->setDescription($description);
		$this->setEmoji($emoji);
		$this->setDefault($default);
	}

	public function getLabel() : string{
		return $this->label;
	}

	public function setLabel(string $label) : void{
		if(strlen($label) > 100){
			throw new AssertionError("Label cannot be longer than 100 characters.");
		}
		$this->label = $label;
	}

	public function getValue() : string{
		return $this->value;
	}

	public function setValue(string $value) : void{
		if(strlen($value) > 100){
			throw new AssertionError("Value cannot be longer than 100 characters.");
		}
		$this->value = $value;
	}

	public function getDescription() : ?string{
		return $this->description;
	}

	public function setDescription(?string $description) : void{
		if($description !== null && strlen($description) > 100){
			throw new AssertionError("Description cannot be longer than 100 characters.");
		}
		$this->description = $description;
	}

	public function getEmoji() : ?Emoji{
		return $this->emoji;
	}

	public function setEmoji(?Emoji $emoji) : void{
		$this->emoji = $emoji;
	}

	public function getDefault() : ?bool{
		return $this->default;
	}

	public function setDefault(?bool $default) : void{
		$this->default = $default;
	}

	public function binarySerialize() : BinaryStream{
		$stream = new BinaryStream();
		$stream->putString($this->label);
		$stream->putString($this->value);
		$stream->putNullableString($this->description);
		$stream->putNullableSerializable($this->emoji);
		$stream->putNullableBool($this->default);
		return $stream;
	}

	public static function fromBinary(BinaryStream $stream) : self{
		return new self(
			$stream->getString(),                           // label
			$stream->getString(),                           // value
			$stream->getNullableString(),                   // description
			$stream->getNullableSerializable(Emoji::class), // emoji
			$stream->getNullableBool()                      // default
		);
	}
}
