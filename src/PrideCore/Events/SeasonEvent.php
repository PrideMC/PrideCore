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

namespace PrideCore\Events;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat as TF;
use function date;

class SeasonEvent implements Listener{

	use SingletonTrait;

	private array $notified = [];

	public function checkSeasonOnJoin(PlayerJoinEvent $event) : void{
		$player = $event->getPlayer();

		if(!isset($this->notified[$player->getUniqueId()->getBytes()])){
			$this->notified[$player->getUniqueId()->getBytes()] = false;
		}

		switch(date("m")){
			case 01:
				if(date("d") === 1){
					$player->sendToastNotification(TF::AQUA . "New year, more memories! Season Finale!!", TF::GREEN . "Grab your new friends, get an exclusive rewards on finale of the season!");
					$player->sendTitle(TF::GREEN . TF::BOLD . "HAPPY NEW YEAR!");
					$player->sendSubTitle(TF::YELLOW . "Season Finale!");
					$player->playSound("random.levelup");
					$this->notified[$player->getUniqueId()->getBytes()] = true;
				}

				if(date("d") === 14){
					$player->sendToastNotification(TF::RED . "New year season finale has been ended!", TF::YELLOW . "Thank you for playing in our server!");
				}
				break;
			case 02:
				if(date("d") === 7){
					$player->sendToastNotification(TF::RED . "Feb" . TF::DARK_RED . "Hearties" . TF::AQUA . " Season!!", TF::GREEN . "Grab your new friends, get an exclusive rewards of cosmetics from the season!");
					$player->sendTitle(TF::RED . "Feb" . TF::DARK_RED . "Hearties" . TF::AQUA . "!!");
					$player->sendSubTitle(TF::YELLOW . "7 days left!");
					$player->playSound("random.levelup");
					$this->notified[$player->getUniqueId()->getBytes()] = true;
				}

				if(date("d") === 10){
					$player->sendToastNotification(TF::RED . "Feb" . TF::DARK_RED . "Hearties" . TF::AQUA . " Season!!", TF::GREEN . "Grab your new friends, get an exclusive rewards of cosmetics from the season!");
					$player->sendTitle(TF::RED . "Feb" . TF::DARK_RED . "Hearties" . TF::AQUA . "!!");
					$player->sendSubTitle(TF::YELLOW . "4 days left!");
					$player->playSound("random.levelup");
					$this->notified[$player->getUniqueId()->getBytes()] = true;
				}

				if(date("d") === 14){
					$player->sendToastNotification(TF::RED . "Feb" . TF::DARK_RED . "Hearties" . TF::AQUA . " Season!!", TF::GREEN . "Grab your new friends, get an exclusive rewards of cosmetics from the season!");
					$player->sendTitle(TF::RED . "Feb" . TF::DARK_RED . "Hearties" . TF::AQUA . "!!");
					$player->sendSubTitle(TF::YELLOW . "Season #1!");
					$player->playSound("random.levelup");
					$this->notified[$player->getUniqueId()->getBytes()] = true;
				}

				if(date("d") === 15){
					$player->sendToastNotification(TF::RED . "Feb" . TF::DARK_RED . "Hearties" . TF::AQUA . " has been ended!", TF::GREEN . "Thank you for joining in our server!");
					$player->sendTitle(TF::RED . "Feb" . TF::DARK_RED . "Hearties" . TF::AQUA . "!!");
					$player->sendSubTitle(TF::RED . "Season #1 is Ended!");
					$player->playSound("random.levelup");
					$this->notified[$player->getUniqueId()->getBytes()] = true;
				}
				break;
		}
	}
}
