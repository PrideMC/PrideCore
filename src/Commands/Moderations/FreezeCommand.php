<?php

declare(strict_types=1);

namespace PrideCore\Commands\Moderations;

use PrideCore\RPlayer;
use PrideCore\PrideCore;
use PrideCore\Utils\Interfaces\IMessages;
use PrideCore\Utils\Interfaces\IPermissions;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class FreezeCommand extends Command implements IMessages, IPermissions
{
	public function __construct() 
	{
		$this->plugin = PrideCore::getInstance();
		parent::__construct("freeze", "Freeze a player.", "/freeze");
		$this->setPermission(IPermissions::FREEZE);
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if(!$sender instanceof RPlayer)
		{
			$sender->sendMessage(IMessages::NOT_PLAYER);
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
			$sender->sendMessage(vsprintf(IMessages::FREEZE_NOT_FOUND, [$args[0]]));
			return;
		} 
		else
		{
			if($sender === $target) 
			{
				$sender->sendMessage(IMessages::FREEZE_SELF_ERROR);
				return;
			}
			if($target->isImmobile())
			{
				$target->setImmobile(false);
				$target->sendMessage(IMessages::FREEZE_UNFROZEN);
				$sender->sendMessage(vsprintf(IMessages::FREEZE_UNFROZEN_PLAYER, [$target->getName()]));
				return;
			} 
			else 
			{
				$target->setImmobile(true);
				$target->sendMessage(IMessages::FREEZE_FROZEN);
				$sender->sendMessage(vsprintf(IMessages::FREEZE_FROZEN_PLAYER, [$target->getName()]));
				return;
			}
		}
	}
}