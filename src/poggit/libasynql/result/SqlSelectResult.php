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

namespace poggit\libasynql\result;

use poggit\libasynql\SqlResult;

class SqlSelectResult extends SqlResult{
	private $columnInfo;
	private $rows;

	/**
	 * SqlSelectResult constructor.
	 *
	 * @param SqlColumnInfo[] $columnInfo
	 * @param array[]         $rows
	 */
	public function __construct(array $columnInfo, array $rows){
		$this->columnInfo = $columnInfo;
		$this->rows = $rows;
	}

	/**
	 * Returns the columns from the query
	 *
	 * @return SqlColumnInfo[]
	 */
	public function getColumnInfo() : array{
		return $this->columnInfo;
	}

	/**
	 * Returns an array of rows. Each row is an array with keys as the (virtual) column name and values as the cell value. The type of cell values are juggled with the following special rules:
	 * - <code>TINYINT(1)</code> and <code>BIT(1)</code> in MySQL are expressed in <code>bool</code>
	 * - Signed <code>long long</code>, a.k.a. <code>BIGINT [SIGNED]</code>, i.e. 64-bit unsigned integers, are expressed in <code>int</code>, because PocketMine only supports 64-bit machines.
	 * - Unsigned <code>long long</code>, a.k.a. <code>BIGINT [SIGNED]</code>, i.e. 64-bit unsigned integers, are also expressed in <code>int</code>. If it exceeds <code>PHP_INT_MAX</code>, it overflows natively, i.e. <b>PHP_INT_MAX + 1 becomes PHP_INT_MIN</b>, which is different from both mysqli's implementation and PHP's behaviour.
	 * - Timestamps will be converted to a {@link https://php.net/date date()}-compatible UNIX timestamp in seconds.
	 * - Other types are juggled according to rules provided by the backend.
	 *
	 * If the query has multiple columns with the same name, the latter one overwrites the former ones. For example, the query <code>SELECT 1 a, 2 a</code> returns the result set <code>[ ["a" => 2] ]</code>.
	 *
	 * Also note that qualifying the column reference with the table name will not add the table name into the column name in the result set. For example, the query <code>SELECT foo.qux, bar.qux</code> will return the result set <code>[ ["qux" => "the value in bar.qux"] ]</code>.
	 *
	 * Therefore, use column aliases when the column names may duplicate.
	 *
	 * @return array[]
	 */
	public function getRows() : array{
		return $this->rows;
	}
}
