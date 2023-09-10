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

namespace PrideCore\Utils;

use function cos;
use function sin;

/**
 * 3 dimensional point converter.
 */
class ThreeDimensionalPoint
{
	public float $x;
	public float $y;
	public float $z;

	public function __construct(float $x, float $y, float $z)
	{
		$this->x = $x;
		$this->y = $y;
		$this->z = $z;
	}

	public function rotate(float $rot) : ?ThreeDimensionalPoint
	{
		$cos = cos($rot);
		$sin = sin($rot);

		return new ThreeDimensionalPoint(
			(float) ($this->x * $cos + $this->z * $sin),
			$this->y,
			(float) ($this->x * -$sin + $this->z * $cos));
	}
}
