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

namespace PrideCore\Commands;

use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission as PMPermission;
use pocketmine\permission\PermissionAttachment;
use pocketmine\permission\PermissionManager;
use pocketmine\utils\NotCloneable;
use pocketmine\utils\NotSerializable;
use pocketmine\utils\SingletonTrait;
use PrideCore\Core;
use PrideCore\Player\Player;

/**
 * A class that register the permissions of the plugin.
 */
class Permissions
{
	use NotSerializable;
	use NotCloneable;
	use SingletonTrait;

	public array $perm = [];
	public ?PermissionAttachment $attachment = null;

	public function __construct()
	{
		$this->register("pride.staff.rank", Permissions::OPERATOR);
		$this->register("pride.staff.maintenance", Permissions::OPERATOR);
		$this->register("pride.staff.disguise", Permissions::OPERATOR);
		$this->register("pride.staff.fly", Permissions::OPERATOR);
		$this->register("pride.staff.ban", Permissions::OPERATOR);
		$this->register("pride.staff.warn", Permissions::OPERATOR);
		$this->register("pride.staff.freeze", Permissions::OPERATOR);
		$this->register("pride.staff.kick", Permissions::OPERATOR);
		$this->register("pride.staff.pardon", Permissions::OPERATOR);
		$this->register("pride.staff.tempmute", Permissions::OPERATOR);
		$this->register("pride.staff.mute", Permissions::OPERATOR);
		$this->register("pride.staff.globalmute", Permissions::OPERATOR);
		$this->register("pride.staff.redeem", Permissions::OPERATOR);
		$this->register("pride.staff.create_redeem_code", Permissions::OPERATOR);
		$this->register("pride.staff.remove_redeem_code", Permissions::OPERATOR);

		$this->register("pride.media.nick", Permissions::OPERATOR);
		$this->register("pride.builder.build", Permissions::OPERATOR);

		$this->register("pride.basic.command", Permissions::USER); // for all basic commands

		$this->register("pride.bypass.vpn", Permissions::OPERATOR);
		$this->register("pride.bypass.player_count", Permissions::OPERATOR);
		$this->register("pride.bypass.globalmute", Permissions::OPERATOR);
	}

	public const USER = 0;
	public const OPERATOR = 1;
	public const CONSOLE = 3;
	public const NONE = -1;

	protected function register(string $permission, int $permAccess, array $childPermission = []) : void
	{
		$this->perm[] = $permission;
		$perm = new PMPermission($permission, "PrideMC Network Permission", $childPermission);
		$permManager = PermissionManager::getInstance();
		switch($permAccess) {
			case Permissions::USER:
				$p = PermissionManager::getInstance()->getPermission(DefaultPermissions::ROOT_USER);
				$p->addChild($perm->getName(), true);
				break;
			case Permissions::OPERATOR:
				$p = PermissionManager::getInstance()->getPermission(DefaultPermissions::ROOT_OPERATOR);
				$p->addChild($perm->getName(), true);
				break;
			case Permissions::CONSOLE:
				$p = PermissionManager::getInstance()->getPermission(DefaultPermissions::ROOT_CONSOLE);
				$p->addChild($perm->getName(), true);
				break;
			case Permissions::NONE:
				$p = PermissionManager::getInstance()->getPermission(DefaultPermissions::ROOT_USER);
				$p->addChild($perm->getName(), false);
				break;
		}
		$permManager->addPermission($perm);
	}

	public function addPlayerPermissions(Player $player, array $permissions) : void{
		if($this->attachment === null){
			$this->attachment = $player->addAttachment(Core::getInstance());
		}
		$this->attachment->setPermissions($permissions);
		$player->getNetworkSession()->syncAvailableCommands();
	}

	public function resetPlayerPermissions() : void{
		if($this->attachment === null) return;
		$this->attachment->clearPermissions();
	}

	public function getAllPermissions() : array{
		return $this->perm;
	}
}
