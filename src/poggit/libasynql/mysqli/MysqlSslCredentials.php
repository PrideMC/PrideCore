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

namespace poggit\libasynql\mysqli;

use JsonSerializable;
use mysqli;

class MysqlSslCredentials implements JsonSerializable{
	/** @var string|null $key */
	private $key;
	/** @var string $certificate */
	private $certificate;
	/** @var string|null $caCertificate */
	private $caCertificate;
	/** @var string|null $caPath */
	private $caPath;
	/** @var string|null $cipherAlgorithms */
	private $cipherAlgorithms;

	/**
	 * Creates a new {@link MysqlSslCredentials} instance from an array (e.g. from Config), with empty default values.
	 */
	public static function fromArray(array $array) : MysqlSslCredentials{
		return new MysqlSslCredentials(
			$array["key"] ?? null,
			$array["certificate"] ?? null,
			$array["ca-certificate"] ?? null,
			$array["ca-path"] ?? null,
			$array["cipher-algorithms"] ?? null,
		);
	}

	/**
	 * Constructs a new {@link MysqlSslCredentials} by passing parameters directly.
	 *
	 * @param string|null $key - The path name to the key file
	 * @param string|null $certificate - The path name to the certificate file
	 * @param string|null $caCertificate - The path name to the certificate authority file
	 * @param string|null $caPath - The path name to a directory that contains trusted SSL CA certificates in PEM format
	 * @param string|null $cipherAlgorithms - A list of allowable ciphers used for SSL encryption
	 */
	public function __construct(?string $key = null, ?string $certificate = null, ?string $caCertificate = null, ?string $caPath = null, ?string $cipherAlgorithms = null){
		$this->key = $key;
		$this->certificate = $certificate;
		$this->caCertificate = $caCertificate;
		$this->caPath = $caPath;
		$this->cipherAlgorithms = $cipherAlgorithms;
	}

	/**
	 * Sets the SSL credentials for the given {@link mysqli} instance.
	 */
	public function applyToInstance(mysqli $mysqli) : void{
		$mysqli->ssl_set(
			$this->key,
			$this->certificate,
			$this->caCertificate,
			$this->caPath,
			$this->cipherAlgorithms
		);
	}

	public function jsonSerialize() : array{
		return [
			"key" => $this->key,
			"certificate" => $this->certificate,
			"caCertificate" => $this->caCertificate,
			"caPath" => $this->caPath,
			"cipherAlgorithms" => $this->cipherAlgorithms,
		];
	}
}
