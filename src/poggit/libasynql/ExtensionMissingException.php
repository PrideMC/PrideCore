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

namespace poggit\libasynql;

use pocketmine\utils\TextFormat;
use RuntimeException;
use function file;
use function is_file;
use function php_ini_loaded_file;
use function stripos;
use function strpos;

class ExtensionMissingException extends RuntimeException{
	public function __construct(string $extensionName){
		$instructions = "Please install PHP according to the instructions from http://pmmp.readthedocs.io/en/rtfd/installation.html which provides the $extensionName extension.";

		$ini = php_ini_loaded_file();
		if($ini && is_file($ini)){
			foreach(file($ini) as $i => $line){
				if(strpos($line, ";extension=") !== false && stripos($line, $extensionName) !== false){
					$instructions = TextFormat::GOLD . "Please remove the leading semicolon on line " . ($i + 1) . " of $ini and restart the server " . TextFormat::RED . "so that the $extensionName extension can be loaded.";
				}
			}
		}

		parent::__construct("The $extensionName extension is missing. $instructions");
	}
}
