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

use pocketmine\utils\Config as PMConfig;
use pocketmine\utils\SingletonTrait;
use PrideCore\Core;

/**
 * A class that manages plugin configurations.
 */
class Config
{
	use SingletonTrait;

	public ?PMConfig $server = null;

	public ?PMConfig $redeem = null;

	public ?PMConfig $database = null;

	public function __construct()
	{
		self::setInstance($this);
	}

	public function getPluginConfig() : PMConfig
	{
		return Core::getInstance()->getConfig();
	}

	public function getServerConfig() : PMConfig
	{
		if($this->server === null){
			$this->server = new PMConfig($this->getDataPath() . "server.yml", PMConfig::YAML);
		}

		return $this->server;
	}

	public function getDatabaseConfig() : PMConfig
	{
		if($this->database === null){
			$this->database = new PMConfig($this->getDataPath() . "database.yml", PMConfig::YAML);
		}

		return $this->database;
	}

	public function getDataPath() : string
	{
		return Core::getInstance()->getDataFolder();
	}

	public function getRedeemConfig() : PMConfig
	{
		if($this->redeem === null){
			$this->redeem = new PMConfig($this->getDataPath() . "redeem_codes.yml", PMConfig::YAML);
		}

		return $this->redeem;
	}
}
