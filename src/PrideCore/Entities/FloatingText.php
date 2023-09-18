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

namespace PrideCore\Entities;

use pocketmine\block\VanillaBlocks;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket;
use pocketmine\network\mcpe\protocol\SetActorDataPacket;
use pocketmine\network\mcpe\protocol\types\entity\ByteMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\FloatMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\IntMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\LongMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\network\mcpe\protocol\types\entity\StringMetadataProperty;
use pocketmine\world\Position;
use PrideCore\Player\Player;
use PrideCore\Utils\Rank;
use Ramsey\Uuid\Uuid;
use function str_replace;

class FloatingText {

	private string $text;
	private int $runtimeUniqueId;
	private int $runtimeId;
	private Position $position;

	public function __construct(string $text, Position $position, int $id)
	{
		$this->position = $position;
		$this->text = $text;
		$this->runtimeId = $id;
		$this->runtimeUniqueId = Uuid::uuid4()->getBytes();
	}

	public function setText(string $text) : void{
		$this->text = $text;
	}

	public function getText() : string{
		return $this->text;
	}

	public function getUniqueId() : int{
		return $this->runtimeUniqueId;
	}

	public function setUniqueId(int $newId) : void{
		$this->runtimeUniqueId = $newId;
	}

	public function getId() : int{
		return $this->runtimeId;
	}

	public function updateTextTo (Player $player) : void
	{
		$pk = SetActorDataPacket::create($this->runtimeUniqueId,
			[ EntityMetadataProperties::NAMETAG => new StringMetadataProperty($this->format($this->getText(), $player)) ],
			new PropertySyncData([], []),
			0
		);

		$player->getNetworkSession()->sendDataPacket($pk);
	}

	public function spawnTo (Player $player) : void
	{
		$player->getNetworkSession()->sendDataPacket(AddActorPacket::create(
			$this->runtimeUniqueId,
			$this->runtimeId,
			EntityIds::FALLING_BLOCK,
			$this->position->asVector3(),
			null,
			0,
			0,
			0,
			0,
			[],
			[
				EntityMetadataProperties::FLAGS => new LongMetadataProperty(1 << EntityMetadataFlags::IMMOBILE),
				EntityMetadataProperties::SCALE => new FloatMetadataProperty(0.01),
				EntityMetadataProperties::BOUNDING_BOX_WIDTH => new FloatMetadataProperty(0.0),
				EntityMetadataProperties::BOUNDING_BOX_HEIGHT => new FloatMetadataProperty(0.0),
				EntityMetadataProperties::NAMETAG => new StringMetadataProperty($this->text),
				EntityMetadataProperties::VARIANT => new IntMetadataProperty(TypeConverter::getInstance()->getBlockTranslator()->internalIdToNetworkId(VanillaBlocks::AIR()->getStateId())),
				EntityMetadataProperties::ALWAYS_SHOW_NAMETAG => new ByteMetadataProperty(1),
			],
			new PropertySyncData([], []),
			[]
		));
	}

	public function despawnTo (Player $player) : void
	{
		$pk = RemoveActorPacket::create($this->runtimeUniqueId);

		$player->getNetworkSession()->sendDataPacket($pk);
	}

	public function format(string $text, Player $player) : string{
		$text = str_replace("{PING}", $player->getNetworkSession()->getPing() . " ms", $text);
		$text = str_replace("{PLAYER_NAME}", $player->getNick(), $text);
		$text = str_replace("{PLAYER_RANK}", ($player->isNick() ? Rank::getInstance()->displayTag(Rank::PLAYER) : Rank::getInstance()->displayTag($player->getRankId())), $text);
		$text = str_replace("#", "\n", $text); // space use of #

		return $text;
	}

	public function moveTo(Player $player, Position $position = null) : void{
		$this->position = ($position ?? $player->getLocation()->asPosition());
		$this->despawnTo($player);
		$this->spawnTo($player);
	}
}
