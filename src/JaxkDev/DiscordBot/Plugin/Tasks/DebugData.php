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

/*
 * DiscordBot, PocketMine-MP Plugin.
 *
 * Licensed under the Open Software License version 3.0 (OSL-3.0)
 * Copyright (C) 2020-present JaxkDev
 *
 * Discord :: JaxkDev
 * Email   :: JaxkDev@gmail.com
 */

namespace JaxkDev\DiscordBot\Plugin\Tasks;

use JaxkDev\DiscordBot\Plugin\Main;
use pocketmine\command\CommandSender;
use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\TextFormat;
use RuntimeException;
use ZipArchive;
use function is_dir;
use function microtime;
use function mkdir;
use function php_uname;
use function round;
use function scandir;
use function time;
use function yaml_emit;
use const PHP_VERSION;

final class DebugData extends AsyncTask{

	private string $serverFolder;
	private string $pluginFolder;
	private string $config;
	private string $version;
	private string $pocketmineVersion;
	private string $serverVersion;

	public function __construct(Main $plugin, CommandSender $sender){
		$this->storeLocal("sender", $sender);
		$this->serverFolder = $plugin->getServer()->getDataPath();
		$this->pluginFolder = $plugin->getDataFolder();
		$this->config = yaml_emit($plugin->getPluginConfig());
		$this->version = $plugin->getDescription()->getVersion();
		$this->pocketmineVersion = $plugin->getServer()->getPocketMineVersion();
		$this->serverVersion = $plugin->getServer()->getVersion();
	}

	public function onRun() : void{
		$startTime = microtime(true);

		if(!is_dir($this->pluginFolder . "debug")){
			if(!mkdir($this->pluginFolder . "debug")){
				throw new RuntimeException("Failed to create debug folder.");
			}
		}

		$path = $this->pluginFolder . "debug/" . "discordbot_" . time() . ".zip";
		$z = new ZipArchive();
		$z->open($path, ZIPARCHIVE::CREATE);

		//Config file, (USE $plugin->config, token is redacted in this but not on file.) (yaml_emit to avoid any comments that include sensitive data)
		$z->addFromString("config.yml", $this->config);

		//Server log.
		$z->addFile($this->serverFolder . "server.log", "server.log");

		//Add Discord thread logs.
		$dir = scandir($this->pluginFolder . "logs");
		if($dir !== false){
			foreach($dir as $file){
				if($file !== "." && $file !== ".."){
					$z->addFile($this->pluginFolder . "logs/" . $file, "thread_logs/" . $file);
				}
			}
		}

		//Some metadata, instead of users having no clue of anything I ask, therefore generate this information beforehand.
		$time = time();
		$ver = $this->version;
		$pmmp = $this->pocketmineVersion . " | " . $this->serverVersion;
		$os = php_uname();
		$php = PHP_VERSION;
		$z->addFromString("metadata.txt", <<<META
/*
 * DiscordBot, PocketMine-MP Plugin.
 *
 * Licensed under the Open Software License version 3.0 (OSL-3.0)
 * Copyright (C) 2020-present JaxkDev
 *
 * Discord :: JaxkDev
 * Email   :: JaxkDev@gmail.com
 */
 
Version    | {$ver}
Timestamp  | {$time}

PocketMine | {$pmmp}
PHP        | {$php}
OS         | {$os}
META);
		$z->close();
		$time = round(microtime(true) - $startTime, 3);
		$this->setResult(TextFormat::GREEN . "Successfully generated debug data in {$time} seconds, saved file to '$path'");
	}

	public function onError() : void{
		/** @var CommandSender $sender */
		$sender = $this->fetchLocal("sender");
		$sender->sendMessage(TextFormat::RED . "Unable to generate debug data, internal error occurred.");
	}

	public function onCompletion() : void{
		/** @var CommandSender $sender */
		$sender = $this->fetchLocal("sender");
		/** @var string $res */
		$res = $this->getResult() ?? (TextFormat::RED . "Internal error occurred");
		$sender->sendMessage($res);
	}
}
