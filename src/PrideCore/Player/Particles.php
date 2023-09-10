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

use pocketmine\color\Color;
use pocketmine\math\Vector3;
use pocketmine\world\particle\DustParticle;
use pocketmine\world\particle\FlameParticle;
use pocketmine\world\particle\HeartParticle;
use pocketmine\world\particle\LavaDripParticle;
use pocketmine\world\particle\LavaParticle;
use pocketmine\world\particle\PortalParticle;
use pocketmine\world\particle\RedstoneParticle;
use pocketmine\world\particle\SmokeParticle;
use PrideCore\Utils\ThreeDimensionalPoint;
use function cos;
use function explode;
use function implode;
use function sin;

/**
 * Particle cosmetics related...
 */
class Particles {

	public const NONE = 0;
	public const DUST = 1;
	public const FLAME = 2;
	public const HEART = 3;
	public const LAVA = 4;
	public const LAVA_DRIP = 5;
	public const PORTAL = 6;
	public const REDSTONE = 7;
	public const SMOKE = 7;

	public const RED = 0;
	public const BLUE = 1;
	public const GREEN = 2;
	public const DARK_GREEN = 2;
	public const DARK_BLUE = 2;
	public const DARK_AQUA = 2;
	public const DARK_PURPLE = 2;
	public const MINECOIN_GOLD = 2;
	public const GOLD = 3;
	public const YELLOW = 4;
	public const AQUA = 5;
	public const PURPLE = 6;
	public const WHITE = 7;

	public const TRAIL = 0;
	public const SPIRAL = 1;
	public const WING = 3;

	public function setActiveParticle(Player $player, int $particle_id) : void{
		Database::getInstance()->getDatabase()->executeGeneric("setActiveParticle", ["uuid" => $player->getUniqueId()->__toString(), "particle_id" => $particle_id], null, fn (SqlError $err) => Server::getInstance()->getLogger()->error(Core::PREFIX . Core::ARROW . $err->getMessage()));
		$player->setActiveParticle($particle_id);
	}

	public function setParticleColor(Player $player, int $particle_color) : void{
		Database::getInstance()->getDatabase()->executeGeneric("setParticleColor", ["uuid" => $player->getUniqueId()->__toString(), "particle_color" => $particle_color], null, fn (SqlError $err) => Server::getInstance()->getLogger()->error(Core::PREFIX . Core::ARROW . $err->getMessage()));
		$player->setParticleColor($particle_color);
	}

	public function setParticleDisplay(Player $player, int $particle_type) : void{
		Database::getInstance()->getDatabase()->executeGeneric("setParticleDisplay", ["uuid" => $player->getUniqueId()->__toString(), "particle_type" => $particle_type], null, fn (SqlError $err) => Server::getInstance()->getLogger()->error(Core::PREFIX . Core::ARROW . $err->getMessage()));
		$player->setParticleDisplay($particle_type);
	}

	public function getActiveParticle(Player $player) : int{
		return $player->getActiveParticle();
	}

	public function getParticleDisplay(Player $player) : int{
		return $player->getParticleDisplay();
	}

	public function getParticleColor(Player $player) : int {
		return $player->getParticleColor();
	}

	public static function getOwnedParticles(Player $player) : ?array {
		if($player->getOwnedParticles() === null) return null;

		$tags = explode(",", $player->getOwnedParticles());

		return $tags;
	}

	public static function addParticle(Player $player, int $particle_id) : void {
		$tags = explode(",", $player->getOwnedParticles());

		$tags[] = [$tag_id => ""];
		$result = implode(",", $tags);
		$player->setOwnedParticles($result);
		Database::getInstance()->getDatabase()->executeGeneric("setParticlesOwned", ["uuid" => $player->getUniqueId()->__toString(), "particle_owned" => $result], null, fn (SqlError $err) => Server::getInstance()->getLogger()->error(Core::PREFIX . Core::ARROW . $err->getMessage()));
	}

	/**
	 * @return void
	 */
	public function QueryParticle(string $uuid, Closure $resolve) {
		Database::getInstance()->getDatabase()->executeSelect("getParticlesOwned", ["uuid" => Utils::removeDashes($uuid)], function (array $rows) use ($resolve) {
			$owned = $rows[0]["particle_owned"] ?? "";
			$resolve($owned);
		}, fn (SqlError $err) => Server::getInstance()->getLogger()->error(Core::PREFIX . Core::ARROW . $err->getMessage()));
	}

	public function updateOwnedPlayerParticles(Player $player) : void {
		$this->QueryTags($player->getUniqueId()->__toString(), function (string $owned) use ($player) {
			$player->setOwnedParticles($owned);
		});
	}

	public static function removeParticle(Player $player, int $particle_id) : void{
		$particle = explode(",", $player->getOwnedParticles());

		unset($particle[$particle_id]);
		$result = implode(",", $tags);
		$player->setOwnedParticles($result);
		Database::getInstance()->getDatabase()->executeGeneric("setParticlesOwned", ["uuid" => $player->getUniqueId()->__toString(), "particle_owned" => $result], null, fn (SqlError $err) => Server::getInstance()->getLogger()->error(Core::PREFIX . Core::ARROW . $err->getMessage()));
	}

	public function toRGB(int $color) : array {
		$rgb = [];

		switch($color){
			case Particles::RED:
				$rgb[] = 255;
				$rgb[] = 0;
				$rgb[] = 0;
				break;
			case Particles::BLUE:
				$rgb[] = 0;
				$rgb[] = 0;
				$rgb[] = 255;
				break;
			case Particles::GREEN:
				$rgb[] = 0;
				$rgb[] = 255;
				$rgb[] = 0;
				break;
			case Particles::DARK_GREEN:
				$rgb[] = 0;
				$rgb[] = 128;
				$rgb[] = 0;
				break;
			case Particles::DARK_AQUA:
				$rgb[] = 0;
				$rgb[] = 128;
				$rgb[] = 128;
				break;
			case Particles::DARK_BLUE:
				$rgb[] = 0;
				$rgb[] = 0;
				$rgb[] = 128;
				break;
			case Particles::DARK_PURPLE:
				$rgb[] = 128;
				$rgb[] = 0;
				$rgb[] = 128;
			case Particles::MINECOIN_GOLD:
				$rgb[] = 128;
				$rgb[] = 128;
				$rgb[] = 0;
				break;
			case Particles::GOLD:
				$rgb[] = 255;
				$rgb[] = 215;
				$rgb[] = 0;
				break;
			case Particles::YELLOW:
				$rgb[] = 255;
				$rgb[] = 255;
				$rgb[] = 0;
				break;
			case Particles::AQUA:
				$rgb[] = 0;
				$rgb[] = 255;
				$rgb[] = 255;
				break;
			case Particles::PURPLE:
				$rgb[] = 255;
				$rgb[] = 0;
				$rgb[] = 255;
				break;
			case Particles::WHITE:
				$rgb[] = 255;
				$rgb[] = 255;
				$rgb[] = 255;
				break;
		}

		return $rgb;
	}

	public function displayTrailParticle(Player $player, int $id) : void{
		switch($id){
			case Particles::DUST:
				$color = $this->toRGB($player->getParticleColor());
				$particle = new DustParticle(new Color($color[0], $color[1], $color[2]));
				$player->getWorld()->addParticle($player->getPosition()->add(0, 0.2, 0), $particle);
				break;
			case Particles::FLAME:
				$particle = new FlameParticle();
				$player->getWorld()->addParticle($player->getPosition()->add(0, 0.2, 0), $particle);
				break;
			case Particles::HEART:
				$particle = new HeartParticle();
				$player->getWorld()->addParticle($player->getPosition()->add(0, 0.2, 0), $particle);
				break;
			case Particles::LAVA:
				$particle = new LavaParticle();
				$player->getWorld()->addParticle($player->getPosition()->add(0, 0.2, 0), $particle);
				break;
			case Particles::LAVA_DRIP:
				$particle = new LavaDripParticle();
				$player->getWorld()->addParticle($player->getPosition()->add(0, 0.2, 0), $particle);
				break;
			case Particles::PORTAL:
				$particle = new PortalParticle();
				$player->getWorld()->addParticle($player->getPosition()->add(0, 0.2, 0), $particle);
				break;
			case Particles::REDSTONE:
				$particle = new RedstoneParticle();
				$player->getWorld()->addParticle($player->getPosition()->add(0, 0.2, 0), $particle);
				break;
			case Particles::SMOKE:
				$particle = new SmokeParticle();
				$player->getWorld()->addParticle($player->getPosition()->add(0, 0.2, 0), $particle);
				break;
		}
	}

	private static array $outline = [];

	private static array $fill = [];

	protected function loadPoints() : void {
		self::$outline[] = new ThreeDimensionalPoint(0, 0, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(0.1, 0.01, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(0.3, 0.03, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(0.3, 0.03, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(0.4, 0.04, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(0.6, 0.1, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(0.61, 0.2, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(0.61, 0.2, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(0.62, 0.4, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(0.63, 0.6, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(0.635, 0.7, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(0.7, 0.7, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(0.9, 0.75, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.2, 0.8, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.4, 0.9, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.6, 1, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.8, 1.1, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.85, 0.9, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.9, 0.7, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.85, 0.5, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.8, 0.3, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.75, 0.1, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.7, -0.1, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.65, -0.3, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.55, -0.5, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.45, -0.7, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.30, -0.75, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.15, -0.8, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.0, -0.85, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(0.8, -0.87, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(0.6, -0.7, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(0.5, -0.5, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(0.4, -0.3, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(0.3, -0.3, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(0.15, -0.3, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(0, -0.3, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(0.9, 0.55, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.2, 0.6, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.4, 0.7, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.6, 0.9, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(0.9, 0.35, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.2, 0.4, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.4, 0.5, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.6, 0.7, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(0.9, 0.15, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.2, 0.2, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.4, 0.3, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.6, 0.5, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(0.9, -0.05, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.2, 0, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.4, 0.1, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.6, 0.3, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(0.7, -0.25, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.0, -0.2, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.2, -0.1, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.4, 0.1, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(0.7, -0.45, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.0, -0.4, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.2, -0.3, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.4, -0.1, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.30, -0.55, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.15, -0.6, -0.5);
		self::$outline[] = new ThreeDimensionalPoint(1.0, -0.65, -0.5);

		self::$fill[] = new ThreeDimensionalPoint(1.2, 0.6, -0.5);
		self::$fill[] = new ThreeDimensionalPoint(1.4, 0.7, -0.5);
		self::$fill[] = new ThreeDimensionalPoint(1.1, 0.2, -0.5);
		self::$fill[] = new ThreeDimensionalPoint(1.3, 0.3, -0.5);
		self::$fill[] = new ThreeDimensionalPoint(1.0, -0.2, -0.5);
		self::$fill[] = new ThreeDimensionalPoint(1.2, -0.1, -0.5);
	}

	public function displayWingParticle(Player $player, int $color, ?Particle $fill = null) : void {
		$playerLocation = $player->getLocation();
		$x = (float) $player->getEyePos()->getX();
		$y = (float) $player->getEyePos()->getY() - 0.2;
		$z = (float) $player->getEyePos()->getZ();
		$rot = -$playerLocation->getYaw() * 0.017453292;
		$color = $this->toRGB($player->getParticleColor());
		$particle = new DustParticle(new Color($color[0], $color[1], $color[2]));

		foreach (self::outline as $point) {
			$rotated = $point->rotate($rot);

			$player->getWorld()->addParticle(new Vector3($rotated->x + $x, $rotated->y + $y, $rotated->z + $z), $particle);

			$point->z *= -1;
			$rotated = $point->rotate($rot + 3.1415);
			$point->z *= -1;

			$player->getWorld()->addParticle(new Vector3($rotated->x + $x, $rotated->y + $y, $rotated->z + $z), $particle);
		}

		if ($fill) {
			foreach (self::$fill as $point) {
				$rotated = $point->rotate($rot);

				$player->getWorld()->addParticle(new Vector3($rotated->x + $x, $rotated->y + $y, $rotated->z + $z), $fill);

				$point->z *= -1;
				$rotated = $point->rotate($rot + 3.1415);
				$point->z *= -1;

				$player->getWorld()->addParticle(new Vector3($rotated->x + $x, $rotated->y + $y, $rotated->z + $z), $fill);
			}
		}
	}

	public function displaySpiralParticle(Player $player, int $particle_id) : void {
		switch($particle_id){
			case Particles::DUST:
				$color = $this->toRGB($player->getParticleColor());
				$particle = new DustParticle(new Color($color[0], $color[1], $color[2]));
				break;
			case Particles::FLAME:
				$particle = new FlameParticle();
				break;
			case Particles::LAVA:
				$particle = new LavaParticle();
				break;
			case Particles::LAVA_DRIP:
				$particle = new LavaDripParticle();
				break;
			case Particles::PORTAL:
				$particle = new PortalParticle();
				break;
			case Particles::REDSTONE:
				$particle = new RedstoneParticle();
				break;
			case Particles::SMOKE:
				$particle = new SmokeParticle();
				break;
		}
		$slice = 2 * M_PI / 16;
		$radius = 0.65;
		$playerOffset = 2;
		for ($i = 0; $i < 16; $i++) {
			$angle = $slice * $i;
			$dx = $radius * cos($angle);
			$dy = $playerOffset;
			$dz = $radius * sin($angle);
			$player->getWorld()->addParticle($player->getPosition()->add($dx, $dy, $dz), $particle);
		}
	}
}
