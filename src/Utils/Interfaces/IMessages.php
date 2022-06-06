<?php

declare(strict_types=1);

namespace PrideCore\Utils\Interfaces;

use pocketmine\utils\TextFormat as T;

interface IMessages
{	
	/// COMMAND MESSAGES ///
	public const NOT_PLAYER = T::RED . "You are not a player to use this command.";
	public const INVALID_ARGS = T::YELLOW . "Usage: " .  T::RED . "%s";
	public const PLAYER_NOT_FOUND = T::RED . "The player %s is not found or online!";
	public const NO_PERMISSION = T::RED . "You dont have permission to use this command.";
	
	public const FREEZE_SELF_ERROR = T::RED . "You cant froze your self!";
	public const FREEZE_UNFROZE = T::GREEN . "You have been unfrozed.";
	public const FREEZE_UNFROZEN_PLAYER = T::GREEN . "You have been unfrozed %s.";
	public const FREEZE_FROZEN = T::RED . "You have been frozed by staff.";
	public const FREEZE_FROZEN_PLAYER = T::GREEN . "You have been frozed %s.";
	
	public const BAN_KICKED = T::GREEN . "You have been banned %s from the server.";
	public const BAN_KICK_MESSAGE_WITH_REASON = T::YELLOW . "PrideMC Network\n\n" . T::GRAY . "You have been kicked to server.\n" . T::GRAY . "Reason: " . T::RESET . "%s";
	public const BAN_KICK_MESSAGE_WITHOUT_REASON = T::YELLOW . "PrideMC Network\n\n" . T::GRAY . "You have been kicked to server.\n" . T::GRAY . "Reason: " . T::RESET . "Unspecified";
	
	public const PARDON_UNBANNED = T::GREEN . "You have been unbanned %s from the server.";
	
}