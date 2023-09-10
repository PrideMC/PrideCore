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

namespace poggit\libasynql\generic;

use AssertionError;
use InvalidArgumentException;
use JsonSerializable;
use poggit\libasynql\GenericStatement;
use poggit\libasynql\SqlDialect;
use function array_key_exists;
use function get_class;
use function gettype;
use function in_array;
use function is_object;
use function mb_strlen;
use function mb_strpos;
use function mb_substr;
use function str_replace;
use function uksort;

abstract class GenericStatementImpl implements GenericStatement, JsonSerializable{
	/** @var string */
	protected $name;
	/** @var string[] */
	protected $query;
	/** @var string */
	protected $doc;
	/** @var GenericVariable[] */
	protected $orderedVariables;
	/** @var GenericVariable[] */
	protected $variables;
	/** @var string|null */
	protected $file;
	/** @var int */
	protected $lineNo;

	/** @var string[][] */
	protected $varPositions = [];

	public function getName() : string{
		return $this->name;
	}

	public function getQuery() : array{
		return $this->query;
	}

	public function getDoc() : string{
		return $this->doc;
	}

	public function getOrderedVariables() : array{
		return $this->orderedVariables;
	}

	public function getVariables() : array{
		return $this->variables;
	}

	public function getFile() : ?string{
		return $this->file;
	}

	public function getLineNumber() : int{
		return $this->lineNo;
	}

	/**
	 * @param string[]          $query
	 * @param GenericVariable[] $variables
	 */
	public static function forDialect(string $dialect, string $name, array $query, string $doc, array $variables, ?string $file, int $lineNo) : GenericStatementImpl{
		static $classMap = [
			SqlDialect::MYSQL => MysqlStatementImpl::class,
			SqlDialect::SQLITE => SqliteStatementImpl::class,
		];
		$className = $classMap[$dialect];
		return new $className($name, $query, $doc, $variables, $file, $lineNo);
	}

	public function __construct(string $name, array $query, string $doc, array $variables, ?string $file, int $lineNo){
		$this->name = $name;
		$this->query = $query;
		$this->doc = $doc;
		$this->orderedVariables = $variables;
		$this->variables = $variables;
		$this->file = $file !== null ? str_replace("\\", "/", $file) : null;
		$this->lineNo = $lineNo;

		$this->compilePositions();
	}

	protected function compilePositions() : void{
		uksort($this->variables, static function($s1, $s2){
			return mb_strlen($s2) <=> mb_strlen($s1);
		});

		$usedNames = [];

		$newQuery = [];

		foreach($this->query as $bufferId => $buffer) {
			$positions = [];
			$quotesState = null;

			$this->varPositions[$bufferId] = [];

			for($i = 1, $iMax = mb_strlen($buffer); $i < $iMax; ++$i){
				$thisChar = mb_substr($buffer, $i, 1);

				if($quotesState !== null){
					if($thisChar === "\\"){
						++$i; // skip one character
						continue;
					}
					if($thisChar === $quotesState){
						$quotesState = null;
						continue;
					}
					continue;
				}
				if(in_array($thisChar, ["'", "\"", "`"], true)){
					$quotesState = $thisChar;
					continue;
				}

				if($thisChar === ":"){
					$name = null;

					foreach($this->variables as $variable){
						if(mb_strpos($buffer, $variable->getName(), $i + 1) === $i + 1){
							$positions[$i] = $name = $variable->getName();
							break;
							// if multiple variables match, the first one i.e. the longest one wins
						}
					}

					if($name !== null){
						$usedNames[$name] = true;
						$i += mb_strlen($name); // skip the name
					}
				}
			}

			$newBuffer = "";
			$lastPos = 0;
			foreach($positions as $pos => $name){
				$newBuffer .= mb_substr($buffer, $lastPos, $pos - $lastPos);
				$this->varPositions[$bufferId][mb_strlen($newBuffer)] = $name; // we aren't using $pos here, because we want the position in the cleaned string, not the position in the original query string
				$lastPos = $pos + mb_strlen($name) + 1;
			}
			$newBuffer .= mb_substr($buffer, $lastPos);

			$newQuery[$bufferId] = $newBuffer;
		}

		$this->query = $newQuery;

		foreach($this->variables as $variable){
			if(!isset($usedNames[$variable->getName()])){
				throw new InvalidArgumentException("The variable {$variable->getName()} is not used anywhere in the query \"{$this->name}\"! Check for typos.");
			}
		}
	}

	public function format(array $vars, ?string $placeHolder, ?array &$outArgs) : array{
		$outArgs = [];
		$queries = [];

		foreach($this->query as $bufferId => $buffer) {
			$outArgs[$bufferId] = [];

			foreach($this->variables as $variable){
				if(!$variable->isOptional() && !array_key_exists($variable->getName(), $vars)){
					throw new InvalidArgumentException("Missing required variable {$variable->getName()}");
				}
			}

			$query = "";

			$lastPos = 0;
			foreach($this->varPositions[$bufferId] as $pos => $name){
				$query .= mb_substr($buffer, $lastPos, $pos - $lastPos);
				$value = $vars[$name] ?? $this->variables[$name]->getDefault();
				try{
					$query .= $this->formatVariable($this->variables[$name], $value, $placeHolder, $outArgs[$bufferId]);
				}catch(AssertionError $e){
					throw new InvalidArgumentException("Invalid value for :$name - " . $e->getMessage() . ",  " . self::getType($value) . " given", 0, $e);
				}
				$lastPos = $pos;
			}

			$query .= mb_substr($buffer, $lastPos);

			$queries[] = $query;
		}

		return $queries;
	}

	private static function getType($value){
		return is_object($value) ? get_class($value) : gettype($value);
	}

	protected abstract function formatVariable(GenericVariable $variable, $value, ?string $placeHolder, array &$outArgs) : string;

	public function jsonSerialize() : array {
		return [
			"name" => $this->name,
			"query" => $this->query,
			"doc" => $this->doc,
			"variables" => $this->variables,
			"file" => $this->file,
			"lineNo" => $this->lineNo,
		];
	}
}
