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

namespace poggit\libasynql;

interface SqlThread{
	public const MODE_GENERIC = 0;
	public const MODE_CHANGE = 1;
	public const MODE_INSERT = 2;
	public const MODE_SELECT = 3;

	/**
	 * Joins the thread
	 *
	 * @see https://php.net/thread.join Thread::join
	 */
	public function join();

	/**
	 * Signals the thread to stop waiting for queries when the send buffer is cleared.
	 */
	public function stopRunning() : void;

	/**
	 * Adds a query to the queue.
	 *
	 * @param mixed[]  $params
	 */
	public function addQuery(int $queryId, array $modes, array $queries, array $params) : void;

	/**
	 * Handles the results that this query has completed
	 *
	 * @param callable[] $callbacks
	 */
	public function readResults(array &$callbacks, ?int $expectedResults) : void;

	/**
	 * Checks if the initial connection has been made, no matter successful or not.
	 */
	public function connCreated() : bool;

	/**
	 * Checks if the initial connection failed.
	 */
	public function hasConnError() : bool;

	/**
	 * Gets the error of the initial connection.
	 */
	public function getConnError() : ?string;
}
