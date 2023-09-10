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

namespace PrideCore\Events;

use libasynCurl\Curl;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\network\mcpe\protocol\types\DeviceOS;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\InternetRequestResult;
use pocketmine\utils\TextFormat as TF;
use PrideCore\Core;
use PrideCore\Utils\TimeUtils;

use function count;
use function explode;
use function in_array;
use function json_decode;
use function str_contains;
use function strtoupper;

/**
 * ServerListener Event
 */
class ServerListener implements Listener
{

	public function onQuery(QueryRegenerateEvent $event) : void
	{
		$event->getQueryInfo()->setServerName(Core::PREFIX);
		$event->getQueryInfo()->setListPlugins(false);
		$event->getQueryInfo()->setPlayerCount(Core::getInstance()->getCache()->getPlayerCount() + count(Core::getInstance()->getServer()->getOnlinePlayers()));
		$event->getQueryInfo()->setMaxPlayerCount(Core::getInstance()->getCache()->getPlayerCount() + count(Core::getInstance()->getServer()->getOnlinePlayers()) + 1);
	}

	public function onLogin(PlayerPreLoginEvent $ev) : void
	{
		$player = $ev->getPlayerInfo();

		/// BAN CHECK ///
		$entry = Core::getInstance()->getServer()->getNameBans()->getEntry($player->getUsername());
		if (Core::getInstance()->getServer()->getNameBans()->isBanned($player->getUserName())) {
			if ($entry->getReason() === "Banned by an operator.") {
				$reason = "Unspecified";
			} else {
				$reason = $entry->getReason();
			}
			if ($entry->getExpires() === null) {
				$date = "Permanent";
			} else {
				$date = TimeUtils::toPrettyFormat($entry->getExpires());
			}
			$ev->setKickFlag(PlayerPreLoginEvent::KICK_FLAG_BANNED, "Banned from Network", Core::PREFIX . TF::GRAY . "\nYou have been banned from our network." . "\n\n" . Core::ARROW . TF::RESET . TF::GRAY . " Reason: " . TF::YELLOW . $reason . TF::RESET . "\n" . Core::ARROW . TF::RESET . TF::GRAY . " Expires: " . TF::YELLOW . $date);
		}

		/// MAINTENANCE CHECK ///
		if (Core::$maintenance === true) {
			if (in_array($player->getUserName(), Core::getInstance()->getConfigs()->getServerConfig()->get("maintenanceBypass"), true) || Core::getInstance()->getServer()->isOp($player->getUserName())) {
			} else {
				$ev->setKickFlag(PlayerPreLoginEvent::KICK_FLAG_PLUGIN, "Network Maintenance", Core::PREFIX . TF::GRAY . "\nSorry, We're going on the maintenance.\n\nCheck back later if available soon to join.");
			}
		}

		/// TOOLBOX ANDROID HACKS CHECK ///
		$clientInfo = $player->getExtraData();
		if ($clientInfo["DeviceOS"] === DeviceOS::ANDROID) {
			$first = explode(" ", $clientInfo["DeviceModel"])[0];
			if ($first !== strtoupper($first)) {
				Core::getInstance()->getServer()->getLogger()->warning(Core::PREFIX . Core::ARROW . TF::RED . "ALERT! {$player->getUserName()} is using toolbox on android!");
				$ev->setKickFlag(PlayerPreLoginEvent::KICK_FLAG_PLUGIN, "Using Modified Client or Hack Client/Toolbox", Core::PREFIX . TF::GRAY . "\nYou have been kicked from our network." . TF::RESET . Core::ARROW . TF::GRAY . "\n\nReason:" . TF::YELLOW . "Toolbox Application is not allowed." . TF::RESET);
			}
		}

		$this->checkVPN($ev->getPlayerInfo()->getUsername(), $ev->getIp());
	}

	public function checkVPN(string $username, string $address) : void{
		if(Core::getInstance()->getServer()->isOp($username)) return;
		$api = Core::getInstance()->getConfigs()->getServerConfig()->getNested("vpn.api-key");
		Curl::getRequest("https://vpnapi.io/api/$address?key=$api", 10, ["Content-Type: application/json"], function(?InternetRequestResult $result) use ($username, $address) : void {
			if($result !== null){
				if(($response = json_decode($result->getBody(), true)) !== null){

					if(isset($response["message"]) && $response["message"] !== ""){
						if($address === "127.0.0.1" || $address === "::" || $address === "0.0.0.0" || $address === "localhost" || str_contains($address, "192.168")){
							Core::getInstance()->getServer()->getLogger()->info(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "Unable to check ip: " . TF::AQUA . $address . TF::RED . " Error: " . TF::DARK_RED . $response["message"] . TF::RED . ", is using local ip?");
							return;
						} else {
							Core::getInstance()->getServer()->getLogger()->info(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "Unable to check ip: " . TF::AQUA . $address . TF::RED . " Error: " . TF::DARK_RED . $response["message"]);
						}
						$this->checkVPN($username, $address);
						return;
					}

					if(isset($response["security"]["vpn"]) && isset($response["security"]["proxy"]) && isset($response["security"]["tor"]) && isset($response["security"]["relay"])){
						if($response["security"]["vpn"] === true || $response["security"]["proxy"] === true || $response["security"]["tor"] === true || $response["security"]["relay"] === true){
							if(($player = Core::getInstance()->getServer()->getPlayerExact($username)) !== null && $player->isOnline() && $player->spawned){
								Core::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player){
									$player->kick("Using Tor, VPN or other Proxy Services.", "", Core::PREFIX . TF::GRAY . "\nYou have been kicked from our network." . TF::RESET . Core::ARROW . TF::GRAY . "\n\nReason:" . TF::YELLOW . "Toolbox Application is not allowed." . TF::RESET);
								}), 2);
								return;
							}
						}
					}
				}
			}
			$this->checkVPN($username, $address); // loop
		});
	}
}
