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

namespace poggit\libasynql\generic;

use InvalidArgumentException;

class GenericStatementFileParseException extends InvalidArgumentException{
	private $problem;
	private $lineNo;
	private $queryFile;

	public function __construct(string $problem, int $lineNo, string $file = null){
		$this->problem = $problem;
		$this->lineNo = $lineNo;
		$this->queryFile = $file ?? "SQL file";

		parent::__construct("Error parsing prepared statement file: $problem on line $lineNo in $file");
	}

	public function getProblem() : string{
		return $this->problem;
	}

	public function getLineNo() : int{
		return $this->lineNo;
	}

	public function getQueryFile() : string{
		return $this->queryFile;
	}
}
