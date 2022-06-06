<?php

declare(strict_types=1);

namespace PrideCore\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as T;
use PrideCore\PrideCore;
use PrideCore\RPlayer;

class LinksCommand extends Command
{
	public function __construct()
	{
		parent::__construct("links", "Links", "/links", ["link"]);
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		$sender->sendMessage(T::RED . "Please open these following " . T::DARK_RED . "IN BROWSER!");
		$sender->sendMessage("+==================================+");
		$sender->sendMessage("		    PrideMC Network           ");
		$sender->sendMessage("+==================================+");
		$sender->sendMessage("		Discord: discord.gg/pridemc   ");
		$sender->sendMessage("		Facebook: @PrideMC            ");
		$sender->sendMessage("		Youtube: @PrideMC             ");
		$sender->sendMessage("		Website: https://pridemc.ml/  ");
		$sender->sendMessage("+==================================+");
	}
}