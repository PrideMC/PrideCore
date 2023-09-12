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

namespace poggit\libasynql\mysqli;

use JsonSerializable;
use mysqli;
use poggit\libasynql\ConfigException;
use poggit\libasynql\SqlError;
use function mysqli_init;
use function mysqli_real_connect;
use function str_repeat;
use function strlen;

class MysqlCredentials implements JsonSerializable{
	/** @var string $host */
	private $host;
	/** @var string $username */
	private $username;
	/** @var string $password */
	private $password;
	/** @var string $schema */
	private $schema;
	/** @var int $port */
	private $port;
	/** @var string $socket */
	private $socket;
	/** @var MysqlSslCredentials|null */
	private $sslCredentials;

	/**
	 * Creates a new {@link MysqlCredentials} instance from an array (e.g. from Config), with the following defaults:
	 * <pre>
	 * host: 127.0.0.1
	 * username: root
	 * password: ""
	 * schema: {$defaultSchema}
	 * port: 3306
	 * socket: ""
	 * </pre>
	 *
	 * @param string|null $defaultSchema default null
	 * @throws ConfigException If <code>schema</code> is missing and <code>$defaultSchema</code> is null/not passed
	 */
	public static function fromArray(array $array, ?string $defaultSchema = null) : MysqlCredentials{
		if(!isset($defaultSchema, $array["schema"])){
			throw new ConfigException("The attribute \"schema\" is missing in the MySQL settings");
		}
		return new MysqlCredentials(
			$array["host"] ?? "127.0.0.1",
			$array["username"] ?? "root",
			$array["password"] ?? "",
			$array["schema"] ?? $defaultSchema,
			$array["port"] ?? 3306,
			$array["socket"] ?? "",
			isset($array["ssl"]) ? MysqlSslCredentials::fromArray($array["ssl"]) : null,
		);
	}

	/**
	 * Constructs a new {@link MysqlCredentials} by passing parameters directly.
	 */
	public function __construct(string $host, string $username, string $password, string $schema, int $port = 3306, string $socket = "", ?MysqlSslCredentials $sslCredentials = null){
		$this->host = $host;
		$this->username = $username;
		$this->password = $password;
		$this->schema = $schema;
		$this->port = $port;
		$this->socket = $socket;
		$this->sslCredentials = $sslCredentials;
	}

	/**
	 * Creates a new <a href="https://php.net/mysqli">mysqli</a> instance
	 *
	 * @throws SqlError
	 */
	public function newMysqli() : mysqli{
		$mysqli = mysqli_init();
		if($mysqli === false){
			throw new SqlError(SqlError::STAGE_CONNECT, "Failed to initialize mysqli");
		}
		if($this->sslCredentials !== null){
			$this->sslCredentials->applyToInstance($mysqli);
		}
		@mysqli_real_connect($mysqli, $this->host, $this->username, $this->password, $this->schema, $this->port, $this->socket);
		if($mysqli->connect_error){
			throw new SqlError(SqlError::STAGE_CONNECT, $mysqli->connect_error);
		}
		return $mysqli;
	}

	/**
	 * Reuses an existing <a href="https://php.net/mysqli">mysqli</a> instance
	 *
	 * @throws SqlError
	 */
	public function reconnectMysqli(mysqli $mysqli) : void{
		@mysqli_real_connect($mysqli, $this->host, $this->username, $this->password, $this->schema, $this->port, $this->socket);
		if($mysqli->connect_error){
			throw new SqlError(SqlError::STAGE_CONNECT, $mysqli->connect_error);
		}
	}

	/**
	 * Produces a human-readable output without leaking password
	 */
	public function __toString() : string{
		return "$this->username@$this->host:$this->port/schema,$this->socket";
	}

	/**
	 * Prepares value to be var_dump()'ed without leaking password
	 *
	 * @return array
	 */
	public function __debugInfo(){
		return [
			"host" => $this->host,
			"username" => $this->username,
			"password" => str_repeat("*", strlen($this->password)),
			"schema" => $this->schema,
			"port" => $this->port,
			"socket" => $this->socket,
			"sslCredentials" => $this->sslCredentials,
		];
	}

	public function jsonSerialize() : array{
		return [
			"host" => $this->host,
			"username" => $this->username,
			"password" => $this->password,
			"schema" => $this->schema,
			"port" => $this->port,
			"socket" => $this->socket,
			"sslCredentials" => $this->sslCredentials,
		];
	}
}
