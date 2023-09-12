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

use poggit\libasynql\generic\GenericVariable;

interface GenericStatement{
	/**
	 * Returns the dialect this query is intended for.
	 *
	 * @return string one of the constants in {@link SqlDialect}
	 */
	public function getDialect() : string;

	/**
	 * Returns the identifier name of this query
	 *
	 * @return string[]
	 */
	public function getName() : string;

	public function getQuery() : array;

	public function getDoc() : string;

	/**
	 * The variable list ordered by original declaration order
	 *
	 * @return GenericVariable[]
	 */
	public function getOrderedVariables() : array;

	/**
	 * Returns the variables required by this statement
	 *
	 * @return GenericVariable[]
	 */
	public function getVariables() : array;

	public function getFile() : ?string;

	public function getLineNumber() : int;

	/**
	 * Creates a query based on the args and the backend
	 *
	 * @param mixed[]     $vars        the input arguments
	 * @param string|null $placeHolder the backend-dependent variable placeholder constant, if any
	 * @param mixed[][]   &$outArgs    will be filled with the variables to be passed to the backend
	 * @return string[]
	 */
	public function format(array $vars, ?string $placeHolder, ?array &$outArgs) : array;
}
