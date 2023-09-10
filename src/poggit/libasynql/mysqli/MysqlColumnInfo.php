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

namespace poggit\libasynql\mysqli;

use poggit\libasynql\result\SqlColumnInfo;

class MysqlColumnInfo extends SqlColumnInfo{
	private $flags;
	private $mysqlType;

	public function __construct(string $name, string $type, int $flags, int $mysqlType){
		parent::__construct($name, $type);
		$this->flags = $flags;
		$this->mysqlType = $mysqlType;
	}

	public function getFlags() : int{
		return $this->flags;
	}

	public function hasFlag(int $flag) : bool{
		return ($this->flags & $flag) > 0;
	}

	public function getMysqlType() : int{
		return $this->mysqlType;
	}
}
