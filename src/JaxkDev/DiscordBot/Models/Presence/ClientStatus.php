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

namespace JaxkDev\DiscordBot\Models\Presence;

use JaxkDev\DiscordBot\Communication\BinarySerializable;
use JaxkDev\DiscordBot\Communication\BinaryStream;

/** @implements BinarySerializable<ClientStatus> */
final class ClientStatus implements BinarySerializable{

	private Status $desktop;
	private Status $mobile;
	private Status $web;

	public function __construct(Status $desktop = Status::OFFLINE, Status $mobile = Status::OFFLINE,
								Status $web = Status::OFFLINE){
		$this->setDesktop($desktop);
		$this->setMobile($mobile);
		$this->setWeb($web);
	}

	public function getDesktop() : Status{
		return $this->desktop;
	}

	public function setDesktop(Status $desktop) : void{
		$this->desktop = $desktop;
	}

	public function getMobile() : Status{
		return $this->mobile;
	}

	public function setMobile(Status $mobile) : void{
		$this->mobile = $mobile;
	}

	public function getWeb() : Status{
		return $this->web;
	}

	public function setWeb(Status $web) : void{
		$this->web = $web;
	}

	//----- Serialization -----//

	public function binarySerialize() : BinaryStream{
		$stream = new BinaryStream();
		$stream->putString($this->desktop->value);
		$stream->putString($this->mobile->value);
		$stream->putString($this->web->value);
		return $stream;
	}

	public static function fromBinary(BinaryStream $stream) : self{
		return new self(
			Status::from($stream->getString()),   // desktop
			Status::from($stream->getString()),   // mobile
			Status::from($stream->getString())    // web
		);
	}
}
