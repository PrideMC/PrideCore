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
use JaxkDev\DiscordBot\Models\Interactions\Interaction;
use JaxkDev\DiscordBot\Models\Messages\Component\ActionRow;

final class RequestInteractionRespondWithModal extends Packet{

	public const SERIALIZE_ID = 434;

	private Interaction $interaction;

	/** Cannot be longer than 45 characters. */
	private string $title;

	/** Cannot be longer than 100 characters. */
	private string $custom_id;

	/** @var ActionRow[] Array of attached components (ActionRows) (MIN 1, MAX 5 Action Rows, ONLY TextInput components are allowed) */
	private array $components;

	/** @param ActionRow[] $components */
	public function __construct(Interaction $interaction, string $title, string $custom_id, array $components, ?int $uid = null){
		parent::__construct($uid);
		$this->interaction = $interaction;
		$this->title = $title;
		$this->custom_id = $custom_id;
		$this->components = $components;
	}

	public function getInteraction() : Interaction{
		return $this->interaction;
	}

	public function getTitle() : string{
		return $this->title;
	}

	public function getCustomId() : string{
		return $this->custom_id;
	}

	/** @return ActionRow[] */
	public function getComponents() : array{
		return $this->components;
	}

	public function binarySerialize() : BinaryStream{
		$stream = new BinaryStream();
		$stream->putInt($this->getUID());
		$stream->putSerializable($this->interaction);
		$stream->putString($this->title);
		$stream->putString($this->custom_id);
		$stream->putSerializableArray($this->components);
		return $stream;
	}

	public static function fromBinary(BinaryStream $stream) : self{
		$uid = $stream->getInt();
		return new self(
			$stream->getSerializable(Interaction::class),    // interaction
			$stream->getString(),                            // title
			$stream->getString(),                            // custom_id
			$stream->getSerializableArray(ActionRow::class), // components
			$uid
		);
	}
}
