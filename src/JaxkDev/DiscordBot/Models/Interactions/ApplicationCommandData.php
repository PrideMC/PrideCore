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

namespace JaxkDev\DiscordBot\Models\Interactions;

use AssertionError;
use JaxkDev\DiscordBot\Communication\BinarySerializable;
use JaxkDev\DiscordBot\Communication\BinaryStream;
use JaxkDev\DiscordBot\Models\Channels\Channel;
use JaxkDev\DiscordBot\Models\Interactions\Commands\CommandType;
use JaxkDev\DiscordBot\Models\Member;
use JaxkDev\DiscordBot\Models\Messages\Attachment;
use JaxkDev\DiscordBot\Models\Messages\Message;
use JaxkDev\DiscordBot\Models\Role;

use JaxkDev\DiscordBot\Models\User;
use JaxkDev\DiscordBot\Plugin\Utils;
use function strlen;

/**
 * @implements BinarySerializable<ApplicationCommandData>
 * @link https://discord.com/developers/docs/interactions/receiving-and-responding#interaction-object-application-command-data-structure
 */
final class ApplicationCommandData implements BinarySerializable{

	/** the ID of the invoked command */
	private string $id;

	/** the name of the invoked command */
	private string $name;

	/** the type of the invoked command */
	private CommandType $type;

	/**
	 * Resolved user map (ID => user)
	 * @var array<string, User> $resolved_users
	 */
	private ?array $resolved_users;

	/**
	 * Resolved member map (ID => member)
	 * @var array<string, Member> $resolved_members
	 */
	private ?array $resolved_members;

	/**
	 * Resolved role map (ID => role)
	 * @var array<string, Role> $resolved_roles
	 */
	private ?array $resolved_roles;

	/**
	 * Resolved channel map (ID => channel)
	 * @var array<string, Channel> $resolved_channels
	 */
	private ?array $resolved_channels;

	/**
	 * Resolved message map (ID => message)
	 * @var array<string, Message> $resolved_messages
	 */
	private ?array $resolved_messages;

	/**
	 * Resolved attachment map (ID => attachment)
	 * @var array<string, Attachment> $resolved_attachments
	 */
	private ?array $resolved_attachments;

	/**
	 * the params + values from the user
	 * @var ApplicationCommandDataOption[] $options
	 */
	private array $options;

	/** the id of the guild the command is registered to */
	private ?string $guild_id;

	/** id of the user or message targeted by a user or message command */
	private ?string $target_id;

	/**
	 * @param array<string, User>|null       $resolved_users
	 * @param array<string, Member>|null     $resolved_members
	 * @param array<string, Role>|null       $resolved_roles
	 * @param array<string, Channel>|null    $resolved_channels
	 * @param array<string, Message>|null    $resolved_messages
	 * @param array<string, Attachment>|null $resolved_attachments
	 * @param ApplicationCommandDataOption[] $options
	 */
	public function __construct(string $id, string $name, CommandType $type, ?array $resolved_users,
								?array $resolved_members, ?array $resolved_roles, ?array $resolved_channels,
								?array $resolved_messages, ?array $resolved_attachments, array $options,
								?string $guild_id, ?string $target_id){
		$this->setId($id);
		$this->setName($name);
		$this->setType($type);
		$this->setResolvedUsers($resolved_users);
		$this->setResolvedMembers($resolved_members);
		$this->setResolvedRoles($resolved_roles);
		$this->setResolvedChannels($resolved_channels);
		$this->setResolvedMessages($resolved_messages);
		$this->setResolvedAttachments($resolved_attachments);
		$this->setOptions($options);
		$this->setGuildId($guild_id);
		$this->setTargetId($target_id);
	}

	public function getId() : string{
		return $this->id;
	}

	public function setId(string $id) : void{
		if(!Utils::validDiscordSnowflake($id)){
			throw new AssertionError("Command ID '{$id}' is invalid.");
		}
		$this->id = $id;
	}

	public function getName() : string{
		return $this->name;
	}

	public function setName(string $name) : void{
		if(strlen($name) < 1 || strlen($name) > 32){
			throw new AssertionError("Name must be between 1 and 32 characters.");
		}
		$this->name = $name;
	}

	public function getType() : CommandType{
		return $this->type;
	}

	public function setType(CommandType $type) : void{
		$this->type = $type;
	}

	/** @return array<string, User>|null */
	public function getResolvedUsers() : ?array{
		return $this->resolved_users;
	}

	/** @param array<string, User>|null $resolved_users */
	public function setResolvedUsers(?array $resolved_users) : void{
		foreach(($resolved_users ?? []) as $id => $user){
			if(!Utils::validDiscordSnowflake($id)){
				throw new AssertionError("User ID '{$id}' is invalid.");
			}
			if(!($user instanceof User)){
				throw new AssertionError("User ID '{$id}' is not a User instance.");
			}
		}
		$this->resolved_users = $resolved_users;
	}

	/** @return array<string, Member>|null */
	public function getResolvedMembers() : ?array{
		return $this->resolved_members;
	}

	/** @param array<string, Member>|null $resolved_members */
	public function setResolvedMembers(?array $resolved_members) : void{
		foreach(($resolved_members ?? []) as $id => $member){
			if(!Utils::validDiscordSnowflake($id)){
				throw new AssertionError("Member ID '{$id}' is invalid.");
			}
			if(!($member instanceof Member)){
				throw new AssertionError("Member ID '{$id}' is not a Member instance.");
			}
		}
		$this->resolved_members = $resolved_members;
	}

	/** @return array<string, Role>|null */
	public function getResolvedRoles() : ?array{
		return $this->resolved_roles;
	}

	/** @param array<string, Role>|null $resolved_roles */
	public function setResolvedRoles(?array $resolved_roles) : void{
		foreach(($resolved_roles ?? []) as $id => $role){
			if(!Utils::validDiscordSnowflake($id)){
				throw new AssertionError("Role ID '{$id}' is invalid.");
			}
			if(!($role instanceof Role)){
				throw new AssertionError("Role ID '{$id}' is not a Role instance.");
			}
		}
		$this->resolved_roles = $resolved_roles;
	}

	/** @return array<string, Channel>|null */
	public function getResolvedChannels() : ?array{
		return $this->resolved_channels;
	}

	/** @param array<string, Channel>|null $resolved_channels */
	public function setResolvedChannels(?array $resolved_channels) : void{
		foreach(($resolved_channels ?? []) as $id => $channel){
			if(!Utils::validDiscordSnowflake($id)){
				throw new AssertionError("Channel ID '{$id}' is invalid.");
			}
			if(!($channel instanceof Channel)){
				throw new AssertionError("Channel ID '{$id}' is not a Channel instance.");
			}
		}
		$this->resolved_channels = $resolved_channels;
	}

	/** @return array<string, Message>|null */
	public function getResolvedMessages() : ?array{
		return $this->resolved_messages;
	}

	/** @param array<string, Message>|null $resolved_messages */
	public function setResolvedMessages(?array $resolved_messages) : void{
		foreach(($resolved_messages ?? []) as $id => $message){
			if(!Utils::validDiscordSnowflake($id)){
				throw new AssertionError("Message ID '{$id}' is invalid.");
			}
			if(!($message instanceof Message)){
				throw new AssertionError("Message ID '{$id}' is not a Message instance.");
			}
		}
		$this->resolved_messages = $resolved_messages;
	}

	/** @return array<string, Attachment>|null */
	public function getResolvedAttachments() : ?array{
		return $this->resolved_attachments;
	}

	/** @param array<string, Attachment>|null $resolved_attachments */
	public function setResolvedAttachments(?array $resolved_attachments) : void{
		foreach(($resolved_attachments ?? []) as $id => $attachment){
			if(!Utils::validDiscordSnowflake($id)){
				throw new AssertionError("Attachment ID '{$id}' is invalid.");
			}
			if(!($attachment instanceof Attachment)){
				throw new AssertionError("Attachment ID '{$id}' is not a Attachment instance.");
			}
		}
		$this->resolved_attachments = $resolved_attachments;
	}

	/** @return ApplicationCommandDataOption[] */
	public function getOptions() : array{
		return $this->options;
	}

	/** @param ApplicationCommandDataOption[] $options */
	public function setOptions(array $options) : void{
		foreach($options as $option){
			if(!($option instanceof ApplicationCommandDataOption)){
				throw new AssertionError("Option is not a ApplicationCommandDataOption instance.");
			}
		}
		$this->options = $options;
	}

	public function getGuildId() : ?string{
		return $this->guild_id;
	}

	public function setGuildId(?string $guild_id) : void{
		if($guild_id !== null && !Utils::validDiscordSnowflake($guild_id)){
			throw new AssertionError("Guild ID '{$guild_id}' is invalid.");
		}
		$this->guild_id = $guild_id;
	}

	public function getTargetId() : ?string{
		return $this->target_id;
	}

	public function setTargetId(?string $target_id) : void{
		if($target_id !== null && !Utils::validDiscordSnowflake($target_id)){
			throw new AssertionError("Target ID '{$target_id}' is invalid.");
		}
		$this->target_id = $target_id;
	}

	public function binarySerialize() : BinaryStream{
		$stream = new BinaryStream();
		$stream->putString($this->id);
		$stream->putString($this->name);
		$stream->putByte($this->type->value);
		$stream->putNullableStringSerializableArray($this->resolved_users);
		$stream->putNullableStringSerializableArray($this->resolved_members);
		$stream->putNullableStringSerializableArray($this->resolved_roles);
		$stream->putNullableStringSerializableArray($this->resolved_channels);
		$stream->putNullableStringSerializableArray($this->resolved_messages);
		$stream->putNullableStringSerializableArray($this->resolved_attachments);
		$stream->putSerializableArray($this->options);
		$stream->putNullableString($this->guild_id);
		$stream->putNullableString($this->target_id);
		return $stream;
	}

	public static function fromBinary(BinaryStream $stream) : self{
		return new self(
			$stream->getString(),                                               // id
			$stream->getString(),                                               // name
			CommandType::from($stream->getByte()),                              // type
			$stream->getNullableStringSerializableArray(User::class),           // resolved_users
			$stream->getNullableStringSerializableArray(Member::class),         // resolved_members
			$stream->getNullableStringSerializableArray(Role::class),           // resolved_roles
			$stream->getNullableStringSerializableArray(Channel::class),        // resolved_channels
			$stream->getNullableStringSerializableArray(Message::class),        // resolved_messages
			$stream->getNullableStringSerializableArray(Attachment::class),     // resolved_attachments
			$stream->getSerializableArray(ApplicationCommandDataOption::class), // options
			$stream->getNullableString(),                                       // guild_id
			$stream->getNullableString()                                        // target_id
		);
	}
}
