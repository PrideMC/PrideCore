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
use RuntimeException;
use SQLite3;
use function array_map;
use function assert;
use function bin2hex;
use function implode;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_string;
use function strpos;

class SqliteStatementImpl extends GenericStatementImpl{
	public function getDialect() : string{
		return "sqlite";
	}

	protected function formatVariable(GenericVariable $variable, $value, ?string $placeHolder, array &$outArgs) : string{
		if($variable->isList()){
			assert(is_array($value));

			// IN () works with SQLite3.
			$unlist = $variable->unlist();
			return "(" . implode(",", array_map(function($value) use ($placeHolder, $unlist, &$outArgs){
					return $this->formatVariable($unlist, $value, $placeHolder, $outArgs);
				}, $value)) . ")";
		}

		if($value === null){
			if(!$variable->isNullable()){
				throw new InvalidArgumentException("The variable :{$variable->getName()} is not nullable");
			}

			return "NULL";
		}

		switch($variable->getType()){
			case GenericVariable::TYPE_BOOL:
				assert(is_bool($value));
				return $value ? "1" : "0";

			case GenericVariable::TYPE_INT:
				assert(is_int($value));
				return (string) $value;

			case GenericVariable::TYPE_FLOAT:
				assert(is_int($value) || is_float($value));
				return (string) $value;

			case GenericVariable::TYPE_STRING:
				assert(is_string($value));
				if(strpos($value, "\0") !== false){
					return "X'" . bin2hex($value) . "'";
				}
				return "'" . SQLite3::escapeString($value) . "'";
		}

		throw new RuntimeException("Unsupported variable type");
	}
}
