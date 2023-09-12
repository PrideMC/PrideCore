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

namespace PrideCore\Utils;

use ErrorException;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;
use PrideCore\Core;

/**
 * Database related data storing...
 */
class Database
{
	use SingletonTrait;

	public function getDatabase() : DataConnector
	{
		return libasynql::create(Core::getInstance(), $this->getConfig()->get("database"), [
			"sqlite" => "sqlite.sql",
			"mysql" => "mysql.sql",
		], true);
	}

	public function getConfig() : Config
	{
		return new Config(Core::getInstance()->getDataFolder() . "database.yml", Config::YAML);
	}

	public function close() : ?bool
	{
		return $this->getDatabase()->close();
	}

	public function init() : void
	{
		$this->getDatabase()->executeGeneric("init", [], null, fn (ErrorException $err) => Server::getInstance()->getLogger()->error(Core::PREFIX . Core::ARROW . $err->getMessage()));
		$this->getDatabase()->waitAll();
	}
}
