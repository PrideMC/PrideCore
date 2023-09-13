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
use function count;
use function in_array;

class AllowedMentions implements JsonSerializable {
	/** @var bool */
	private $parseUsers = true, $parseRoles = true, $mentionEveryone = true, $suppressAll = false;

	/** @var array */
	private $roles = [];

	/** @var array */
	private $users = [];

	private $data = [];

	/**
	 * If following role is given into the messages content, every user of it will be mentioned
	 */
	public function addRole(string ...$roleID) : void {
		foreach ($roleID as $item) {
			if (in_array($item, $this->roles, true)) {
				continue;
			}

			$this->roles[] = $item;
		}
		$this->parseRoles = false;
	}

	/**
	 * If following user is given into the messages content, the user will be mentioned
	 */
	public function addUser(string ...$userID) : void {
		foreach ($userID as $item) {
			if (in_array($item, $this->users, true)) {
				continue;
			}

			$this->users[] = $item;
		}

		$this->parseUsers = false;
	}

	/**
	 * If the message content has whether everyone or here and $mention is set to false, the users won't be mentioned
	 */
	public function mentionEveryone(bool $mention) : void {
		$this->mentionEveryone = $mention;
	}

	/**
	 * If this function is called no mention will be getting showed for anyone
	 */
	public function suppressAll() : void {
		$this->suppressAll = true;
	}

	public function jsonSerialize() : mixed {
		if ($this->suppressAll) {
			return [
				"parse" => []
			];
		}

		$data = ["parse" => []];
		if ($this->mentionEveryone) {
			$data["parse"][] = "everyone";
		}

		if (count($this->users) !== 0) {
			$data["users"] = $this->users;
		} elseif ($this->parseUsers) {
			$data["parse"][] = "users";
		}

		if (count($this->roles) !== 0) {
			$data["roles"] = $this->roles;
		} elseif ($this->parseRoles) {
			$data["parse"][] = "roles";
		}

		return $data;
	}

	public function asArray() : array{
		// Why doesn't PHP have a `__toArray()` magic method??? This would've been better.
		return $this->data;
	}
}
