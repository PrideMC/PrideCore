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

declare(strict_types=1);

namespace poggit\libasynql\result;

class SqlColumnInfo{
	public const TYPE_STRING = "string";
	public const TYPE_INT = "int";
	public const TYPE_FLOAT = "float";
	public const TYPE_TIMESTAMP = "timestamp";
	public const TYPE_BOOL = "bool";
	public const TYPE_NULL = "null";
	public const TYPE_OTHER = "unknown";

	private $name;
	private $type;

	public function __construct(string $name, string $type){
		$this->name = $name;
		$this->type = $type;
	}

	public function getName() : string{
		return $this->name;
	}

	public function getType() : string{
		return $this->type;
	}
}
