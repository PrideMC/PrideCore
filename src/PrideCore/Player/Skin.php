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
 *
 *  www.mcpride.tk                 github.com/PrideMC
 *  twitter.com/PrideMC         youtube.com/c/PrideMC
 *  discord.gg/PrideMC           facebook.com/PrideMC
 *               bit.ly/JoinInPrideMC
 *  #StandWithUkraine                     #PrideMonth
 *
 */

declare(strict_types=1);

namespace PrideCore\Player;

use pocketmine\entity\Skin as PMSkin;
use pocketmine\network\mcpe\convert\LegacySkinAdapter;
use pocketmine\network\mcpe\protocol\types\skin\SkinData;
use function spl_object_id;

/**
 * Player skin related...
 */
class Skin extends LegacySkinAdapter
{

	private array $personaSkinData = [];

	public function fromSkinData(SkinData $data) : PMSkin
	{
		$skin = parent::fromSkinData($data);

		if($data->isPersona()){
			$this->personaSkinData[spl_object_id($skin)] = $data;
		}

		return $skin;
	}

	public function toSkinData(PMSkin $skin) : SkinData
	{
		return $this->personaSkinData[spl_object_id($skin)] ?? parent::toSkinData($skin);
	}
}
