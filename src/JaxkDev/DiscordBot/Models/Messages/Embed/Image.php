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

namespace JaxkDev\DiscordBot\Models\Messages\Embed;

use JaxkDev\DiscordBot\Communication\BinarySerializable;
use JaxkDev\DiscordBot\Communication\BinaryStream;

/**
 * @implements BinarySerializable<Image>
 * @link https://discord.com/developers/docs/resources/channel#embed-object-embed-image-structure
 */
final class Image implements BinarySerializable{

	private string $url;

	private ?string $proxy_url;

	private ?int $width;

	private ?int $height;

	public function __construct(string $url, ?string $proxy_url = null, ?int $width = null, ?int $height = null){
		$this->setUrl($url);
		$this->setProxyUrl($proxy_url);
		$this->setWidth($width);
		$this->setHeight($height);
	}

	public function getUrl() : string{
		return $this->url;
	}

	public function setUrl(string $url) : void{
		$this->url = $url;
	}

	public function getProxyUrl() : ?string{
		return $this->proxy_url;
	}

	public function setProxyUrl(?string $proxy_url) : void{
		$this->proxy_url = $proxy_url;
	}

	public function getWidth() : ?int{
		return $this->width;
	}

	public function setWidth(?int $width) : void{
		$this->width = $width;
	}

	public function getHeight() : ?int{
		return $this->height;
	}

	public function setHeight(?int $height) : void{
		$this->height = $height;
	}

	public function binarySerialize() : BinaryStream{
		$stream = new BinaryStream();
		$stream->putString($this->getUrl());
		$stream->putNullableString($this->getProxyUrl());
		$stream->putNullableInt($this->getWidth());
		$stream->putNullableInt($this->getHeight());
		return $stream;
	}

	public static function fromBinary(BinaryStream $stream) : self{
		return new self(
			$stream->getString(),         // url
			$stream->getNullableString(), // proxy_url
			$stream->getNullableInt(),    // width
			$stream->getNullableInt()     // height
		);
	}
}
