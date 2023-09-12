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

namespace xenialdan\apibossbar;

use InvalidArgumentException;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\BossEventPacket;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use function get_class;

class PacketListener implements Listener
{
	/** @var Plugin|null */
	private static $registrant;

	public static function isRegistered() : bool
	{
		return self::$registrant instanceof Plugin;
	}

	public static function getRegistrant() : Plugin
	{
		return self::$registrant;
	}

	public static function unregister() : void
	{
		self::$registrant = null;
	}

	public static function register(Plugin $plugin) : void
	{
		if (self::isRegistered()) {
			return;//silent return
		}

		self::$registrant = $plugin;
		$plugin->getServer()->getPluginManager()->registerEvents(new self(), $plugin);
	}

	public function onDataPacketReceiveEvent(DataPacketReceiveEvent $e)
	{
		if ($e->getPacket() instanceof BossEventPacket) $this->onBossEventPacket($e);
	}

	private function onBossEventPacket(DataPacketReceiveEvent $e)
	{
		if (!($pk = $e->getPacket()) instanceof BossEventPacket) throw new InvalidArgumentException(get_class($e->getPacket()) . " is not a " . BossEventPacket::class);
		/** @var BossEventPacket $pk */
		switch ($pk->eventType) {
			case BossEventPacket::TYPE_REGISTER_PLAYER:
			case BossEventPacket::TYPE_UNREGISTER_PLAYER:
				Server::getInstance()->getLogger()->debug("Got BossEventPacket " . ($pk->eventType === BossEventPacket::TYPE_REGISTER_PLAYER ? "" : "un") . "register by client for player id " . $pk->playerEid);
				break;
			default:
				$e->getPlayer()->kick("Invalid packet received", false);
		}
	}

}
