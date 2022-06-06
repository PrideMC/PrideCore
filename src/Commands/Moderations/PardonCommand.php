<?php

declare(strict_types=1);

namespace PrideCore\Commands\Moderations;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use PrideCore\PrideCore;
use pocketmine\utils\TextFormat as T;
use PrideCore\RPlayer;
use PrideCore\Utils\Interfaces\IMessages;
use PrideCore\Utils\Interfaces\IPermissions;

class PardonCommand extends Command implements IMessages, IPermissions 
{
	public function __construct()
	{
		$this->plugin = PrideCore::getInstance();
		parent::__construct("pardon", "Pardon a Player", "/pardon <player_name>", ["unban"]);
		$this->setPermission(IPermissions::PARDON);
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		
		if($sender instanceof ConsoleCommandSender)
		{
			$sender->sendMessage(vsprintf(IMessages::PARDON_UNBANNED, [$args[0]]));
			$sender->getServer()->getNameBans()->remove($args[0]);
			return;
		}
		
		if(!$sender->hasPermission(IPermissions::PARDON))
		{
			$sender->sendMessage(IMessages::NO_PERMISSION);
			return;
		}
		
		if(!isset($args[0]))
		{
			$sender->sendMessage(vsprintf(IMessages::INVALID_ARGS, [$this->usageMessage]));
			return;
		}
		$sender->sendMessage(vsprintf(IMessages::PARDON_UNBANNED, [$args[0]]));
		$sender->getServer()->getNameBans()->remove($args[0]);
	}
}