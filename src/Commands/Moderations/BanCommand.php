<?php 

declare(strict_types=1);

namespace PrideCore\Commands\Moderations;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as T;
use pocketmine\permission\BanEntry;
use PrideCore\PrideCore;
use PrideCore\RPlayer;
use PrideCore\Utils\Interfaces\IMessages;
use PrideCore\Utils\Interfaces\IPermissions;

class BanCommand extends Command implements IMessages, IPermissions
{
	public function __construct()
	{
		$this->plugin = PrideCore::getInstance();
		parent::__construct("ban", "Ban a player.", "/ban <name> [reason: ...]");
		$this->setPermission(IPermissions::BAN);
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args) 
	{
		if(!isset($args[0]))
		{
			$sender->sendMessage(vsprintf(IMessages::INVALID_ARGS, [$this->usageMessage]));
			return;
		}
		if(!isset($args[1]))
		{
			$sender->sendMessage(vsprintf(IMessages::INVALID_ARGS, [$this->usageMessage]));
			return;
		}
		$target = $sender->getServer()->getPlayerExact($args[0]);
		if($target instanceof RPlayer)
		{
			if(!isset($args[0]))
			{
				$target->kick(T::YELLOW . "PrideMC Network\n\n" . T::GRAY . "You have been banned to server.\n" . T::GRAY . "Reason: " . T::RESET . "Unspecified");
			}
			else
			{
				$target->kick(T::YELLOW . "PrideMC Network\n\n" . T::GRAY . "You have been banned to server.\n" . T::GRAY . "Reason: " . T::RESET . $args[1]);
			}
		}
		$sender->getServer()->getNameBans()->addBan($args[0], $args[1], null, $sender->getName());
		$sender->sendMessage(T::GREEN . "Sucessfully banned " . $args[0] . " from this server!");
		return;
	}
}