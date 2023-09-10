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

declare(strict_types = 1);

namespace jojoe77777\FormAPI;

use pocketmine\form\FormValidationException;
use function gettype;
use function is_bool;

class ModalForm extends Form {

	/** @var string */
	private $content = "";

	public function __construct(?callable $callable) {
		parent::__construct($callable);
		$this->data["type"] = "modal";
		$this->data["title"] = "";
		$this->data["content"] = $this->content;
		$this->data["button1"] = "";
		$this->data["button2"] = "";
	}

	public function processData(&$data) : void {
		if(!is_bool($data)) {
			throw new FormValidationException("Expected a boolean response, got " . gettype($data));
		}
	}

	public function setTitle(string $title) : void {
		$this->data["title"] = $title;
	}

	public function getTitle() : string {
		return $this->data["title"];
	}

	public function getContent() : string {
		return $this->data["content"];
	}

	public function setContent(string $content) : void {
		$this->data["content"] = $content;
	}

	public function setButton1(string $text) : void {
		$this->data["button1"] = $text;
	}

	public function getButton1() : string {
		return $this->data["button1"];
	}

	public function setButton2(string $text) : void {
		$this->data["button2"] = $text;
	}

	public function getButton2() : string {
		return $this->data["button2"];
	}
}
