<?php

declare(strict_types=1);

namespace PrideCore\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as T;
use PrideCore\PrideCore;
use PrideCore\RPlayer;

class InfoCommand extends Command
{
	public function __construct()
	{
		parent::__construct("info", "About Server", "/info", ["about"]);
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		$sender->sendMessage("+==================================+");
		$sender->sendMessage("		    PrideMC Network           ");
		$sender->sendMessage("		Discord: discord.gg/pridemc   ");
		$sender->sendMessage("Thank you for playing in our server!");
		$sender->sendMessage("+==================================+");
	}
}