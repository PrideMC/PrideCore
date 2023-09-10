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
use InvalidArgumentException;
use JaxkDev\DiscordBot\Communication\BinaryStream;
use function count;

/**
 * @link https://discord.com/developers/docs/interactions/message-components#action-rows
 */
final class ActionRow extends Component{

	public const SERIALIZE_ID = 14;

	/** @var Button[]|SelectMenu[]|TextInput[] Max 5 buttons or 1 select menu or 1 text input. */
	public array $components = [];

	/**
	 * @param Button[]|SelectMenu[]|TextInput[] $components Max 5 buttons or 1 select menu or 1 text input.
	 */
	public function __construct(array $components = []){
		parent::__construct(ComponentType::ACTION_ROW);
		$this->setComponents($components);
	}

	/**
	 * @return Button[]|SelectMenu[]|TextInput[]
	 */
	public function getComponents() : array{
		return $this->components;
	}

	/**
	 * @param Button[]|SelectMenu[]|TextInput[] $components Max 5 buttons or 1 select menu or 1 text input.
	 */
	public function setComponents(array $components) : void{
		if(count($components) > 5){
			throw new AssertionError("Max 5 components per action row.");
		}
		if(count($components) > 1){
			foreach($components as $component){
				if($component instanceof SelectMenu || $component instanceof TextInput){
					throw new AssertionError("Max 1 select menu or 1 text input or 5 buttons per action row.");
				}
			}
		}
		$this->components = $components;
	}

	public function binarySerialize() : BinaryStream{
		$stream = new BinaryStream();
		$stream->putByte($this->type->value);
		$stream->putSerializableArray($this->components);
		return $stream;
	}

	public static function fromBinary(BinaryStream $stream) : self{
		$type = ComponentType::from($stream->getByte());
		if($type !== ComponentType::ACTION_ROW){
			throw new InvalidArgumentException("Invalid component type {$type->name} ({$type->value}) for action row.");
		}
		$components = [];
		$count = $stream->getInt();
		for($i = 0; $i < $count; $i++){
			$components[] = self::componentFromBinary($stream);
		}
		return new self($components);
	}

	private static function componentFromBinary(BinaryStream $stream) : Button|TextInput|SelectMenu{
		$type = ComponentType::from($stream->getByte());
		switch($type){
			case ComponentType::BUTTON:
				return Button::fromBinary($stream);
			case ComponentType::TEXT_INPUT:
				return TextInput::fromBinary($stream);
			case ComponentType::STRING_SELECT:
			case ComponentType::USER_SELECT:
			case ComponentType::ROLE_SELECT:
			case ComponentType::CHANNEL_SELECT:
			case ComponentType::MENTIONABLE_SELECT:
				$stream->setOffset($stream->getOffset() - 1); //unread first byte so SelectMenu knows what type to build.
				return SelectMenu::fromBinary($stream);
			default:
				throw new InvalidArgumentException("Unknown component type {$type->name} ({$type->value})");
		}
	}
}
