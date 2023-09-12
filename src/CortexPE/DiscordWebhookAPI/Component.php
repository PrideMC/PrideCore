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

use JsonSerializer;

/**
 * TODO:
 * - Add Buttons instead only for links.
 * - Discord Button Interact -> Plugin Action if needed.
 * - Button Colors for not discord button link.
 */
class Component extends JsonSerializer {

	/** @var array **/
	protected $data = [];

	public function asArray() : array{
		// Why doesn't PHP have a `__toArray()` magic method??? This would've been better.
		return $this->data;
	}

	/**
	 * Add Link Button from the message.
	 */
	public function addLinkButton(string $text, string $link){
		if(!isset($this->data["type"])){
			$this->data["type"] = 1; // container for other components
		}
		if(!isset($this->data["components"])){
			$this->data["components"] = [];
		}

		$this->data["components"]["type"] = 5;
		$this->data["components"]["label"] = $text;
		$this->data["components"]["url"] = $link;
	}
}
