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

use AssertionError;
use JaxkDev\DiscordBot\Communication\BinarySerializable;
use JaxkDev\DiscordBot\Communication\BinaryStream;
use function count;
use function strlen;

/**
 * @implements BinarySerializable<Embed>
 * @link https://discord.com/developers/docs/resources/channel#embed-object-embed-structure
 */
final class Embed implements BinarySerializable{

	/** 256 characters */
	private ?string $title;

	/** 4096 characters */
	private ?string $description;

	private ?string $url;

	private ?int $timestamp;

	private ?int $colour;

	private ?Footer $footer;

	private ?Image $image;

	private ?Image $thumbnail;

	private ?Video $video;

	private ?Provider $provider;

	private ?Author $author;

	/** @var Field[] 25 max */
	private array $fields = [];

	/**
	 * @param Field[] $fields
	 */
	public function __construct(?string $title = null, ?string $description = null, ?string $url = null,
								?int $timestamp = null, ?int $colour = null, ?Footer $footer = null,
								?Image $image = null, ?Image $thumbnail = null, ?Video $video = null,
								?Provider $provider = null, ?Author $author = null, array $fields = []){
		$this->setTitle($title);
		$this->setDescription($description);
		$this->setUrl($url);
		$this->setTimestamp($timestamp);
		$this->setColour($colour);
		$this->setFooter($footer);
		$this->setImage($image);
		$this->setThumbnail($thumbnail);
		$this->setVideo($video);
		$this->setProvider($provider);
		$this->setAuthor($author);
		$this->setFields($fields);
	}

	public function getTitle() : ?string{
		return $this->title;
	}

	public function setTitle(?string $title) : void{
		if($title !== null && strlen($title) > 256){
			throw new AssertionError("Embed title can only have up to 256 characters.");
		}
		$this->title = $title;
	}

	public function getDescription() : ?string{
		return $this->description;
	}

	public function setDescription(?string $description) : void{
		if($description !== null && strlen($description) > 4096){
			throw new AssertionError("Embed description can only have up to 4096 characters.");
		}
		$this->description = $description;
	}

	public function getUrl() : ?string{
		return $this->url;
	}

	public function setUrl(?string $url) : void{
		$this->url = $url;
	}

	public function getTimestamp() : ?int{
		return $this->timestamp;
	}

	public function setTimestamp(?int $timestamp) : void{
		$this->timestamp = $timestamp;
	}

	public function getColour() : ?int{
		return $this->colour;
	}

	public function setColour(?int $colour) : void{
		$this->colour = $colour;
	}

	public function getFooter() : ?Footer{
		return $this->footer;
	}

	public function setFooter(?Footer $footer) : void{
		$this->footer = $footer;
	}

	public function getImage() : ?Image{
		return $this->image;
	}

	public function setImage(?Image $image) : void{
		$this->image = $image;
	}

	public function getThumbnail() : ?Image{
		return $this->thumbnail;
	}

	public function setThumbnail(?Image $thumbnail) : void{
		$this->thumbnail = $thumbnail;
	}

	public function getVideo() : ?Video{
		return $this->video;
	}

	public function setVideo(?Video $video) : void{
		$this->video = $video;
	}

	public function getProvider() : ?Provider{
		return $this->provider;
	}

	public function setProvider(?Provider $provider) : void{
		$this->provider = $provider;
	}

	public function getAuthor() : ?Author{
		return $this->author;
	}

	public function setAuthor(?Author $author) : void{
		$this->author = $author;
	}

	/** @return Field[] */
	public function getFields() : array{
		return $this->fields;
	}

	/** @param Field[] $fields */
	public function setFields(array $fields) : void{
		if(count($fields) > 25){
			throw new AssertionError("Embed can only have up to 25 fields.");
		}
		foreach($fields as $field){
			if(!($field instanceof Field)){
				throw new AssertionError("Embed fields must be of type Field.");
			}
		}
		$this->fields = $fields;
	}

	public function binarySerialize() : BinaryStream{
		$stream = new BinaryStream();
		$stream->putNullableString($this->title);
		$stream->putNullableString($this->description);
		$stream->putNullableString($this->url);
		$stream->putNullableLong($this->timestamp);
		$stream->putNullableInt($this->colour);
		$stream->putNullableSerializable($this->footer);
		$stream->putNullableSerializable($this->image);
		$stream->putNullableSerializable($this->thumbnail);
		$stream->putNullableSerializable($this->video);
		$stream->putNullableSerializable($this->provider);
		$stream->putNullableSerializable($this->author);
		$stream->putSerializableArray($this->fields);
		return $stream;
	}

	public static function fromBinary(BinaryStream $stream) : self{
		return new self(
			$stream->getNullableString(),                      // title
			$stream->getNullableString(),                      // description
			$stream->getNullableString(),                      // url
			$stream->getNullableLong(),                        // timestamp
			$stream->getNullableInt(),                         // colour
			$stream->getNullableSerializable(Footer::class),   // footer
			$stream->getNullableSerializable(Image::class),    // image
			$stream->getNullableSerializable(Image::class),    // thumbnail
			$stream->getNullableSerializable(Video::class),    // video
			$stream->getNullableSerializable(Provider::class), // provider
			$stream->getNullableSerializable(Author::class),   // author
			$stream->getSerializableArray(Field::class)        // fields
		);
	}
}
