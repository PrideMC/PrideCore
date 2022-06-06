<?php

declare(strict_types=1);

namespace PrideCore\Commands\Moderations;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use PrideCore\RPlayer;
use PrideCore\PrideCore;
use PrideCore\Utils\Interfaces\IMessages;
use PrideCore\Utils\Interfaces\IPermissions;

class KickCommand extends Command implements IMessages, IPermissions
{
	public function __construct()
	{
		$this->plugin = PrideCore::getInstance();
		parent::__construct("kick", "Kick a Player", "/kick <name> [reason: ...]");
		$this->setPermission(IPermissions::KICK);
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if(!$sender instanceof Player)
		{
			$sender->sendMessage(IMessages::NOT_PLAYER);
			return;
		}
		if(!$sender->hasPermission(IPermissions::KICK))
		{
			$sender->sendMessage(IMessages::NO_PERMISSION);
			return;
		}
		
		if(!isset($args[0]))
		{
			$sender->sendMessage(vsprintf(IMessages::INVALID_ARGS, [$this->usageMessage]));
			return;
		}
		
		$target = $this->plugin->getServer()->getPlayerExact($args[0]);
		if($target === null)
		{
			$sender->sendMessage(vsprintf(IMessages::PLAYER_NOT_FOUND, [$args[0]]));
			return;
		}
		else 
		{
			if(!isset($args[1]))
			{
				$sender->sendMessage(vsprintf(IMessages::BAN_KICKED, $args[0]));
				$target->kick(IMessages::BAN_KICK_MESSAGE_WITHOUT_REASON, null);
			} 
			else 
			{
				$sender->sendMessage(vsprintf(IMessages::BAN_KICKED, $args[0]));
				$target->kick(vsprintf(IMessages::BAN_KICK_MESSAGE_WITH_REASON, [$args[0]]), null);
			}
		}
	}
}