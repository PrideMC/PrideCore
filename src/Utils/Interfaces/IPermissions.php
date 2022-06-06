<?php

declare(strict_types=1);

namespace PrideCore\Utils\Interfaces;


interface IPermissions 
{
	public const BAN = "pride.staff.ban";
	public const KICK = "pride.staff.kick";
	public const PARDON = "pride.staff.pardon";
	public const MAINTENANCE = "pride.staff.maintenance";
	public const FREEZE = "pride.staff.freeze";
	public const BUILD = "pride.staff.build";
	public const CHANGE_SKIN = "pride.staff.change_skin";
}