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

namespace SOFe\AwaitGenerator;

use Exception;

/**
 * The exception to throw into loser generators of
 * a {@link Await::safeRace()}.
 *
 * If your generator has side effects, please consider
 * handling this exception by taking cancellation in a
 * `finally` block. Otherwise, if you prefer the `catch`
 * block, please re-throw this exception at the end.
 * (Please refer to {@link AwaitTest::testSafeRaceCancel()}.)
 *
 * NOTICE: it would not cause a crash even though your
 * generator did not catch it.
 */
final class RaceLostException extends Exception{
	public function __construct() {
	}
}
