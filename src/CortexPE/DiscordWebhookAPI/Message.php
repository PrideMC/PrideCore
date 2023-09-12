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
 *                     Season #5
 *
 *  www.mcpride.tk                 github.com/PrideMC
 *  twitter.com/PrideMC         youtube.com/c/PrideMC
 *  discord.gg/PrideMC           facebook.com/PrideMC
 *               bit.ly/JoinInPrideMC
 *  #PrideGames                           #PrideMonth
 *
 */

declare(strict_types = 1);

namespace CortexPE\DiscordWebhookAPI;

use JsonSerializable;
use function array_key_exists;

class Message implements JsonSerializable {
	/** @var array */
	protected $data = [];

	public function setContent(string $content) : void{
		$this->data["content"] = $content;
	}

	public function getContent() : ?string{
		return $this->data["content"];
	}

	public function getUsername() : ?string{
		return $this->data["username"];
	}

	public function setUsername(string $username) : void{
		$this->data["username"] = $username;
	}

	public function getAvatarURL() : ?string{
		return $this->data["avatar_url"];
	}

	public function setAvatarURL(string $avatarURL) : void{
		$this->data["avatar_url"] = $avatarURL;
	}

	public function addEmbed(Embed $embed) : void{
		if(!empty(($arr = $embed->asArray()))){
			$this->data["embeds"][] = $arr;
		}
	}

	public function addComponent(Component $component) : void{
		if(!empty(($arr = $component->asArray()))){
			$this->data["components"][] = $arr;
		}
	}

	public function setTextToSpeech(bool $ttsEnabled) : void{
		$this->data["tts"] = $ttsEnabled;
	}

	public function getAllowedMentions() : AllowedMentions {
		if (array_key_exists("allowed_mentions", $this->data)) {
			return $this->data["allowed_mentions"];
		}

		return $this->data["allowed_mentions"] = new AllowedMentions();
	}

	public function jsonSerialize() : mixed{
		return $this->data;
	}
}
