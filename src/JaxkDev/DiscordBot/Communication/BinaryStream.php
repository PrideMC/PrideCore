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

/*
 * DiscordBot, PocketMine-MP Plugin.
 *
 * Licensed under the Open Software License version 3.0 (OSL-3.0)
 * Copyright (C) 2020-present JaxkDev
 *
 * Discord :: JaxkDev
 * Email   :: JaxkDev@gmail.com
 */

namespace JaxkDev\DiscordBot\Communication;

use AssertionError;
use function count;
use function strlen;

final class BinaryStream extends \pocketmine\utils\BinaryStream{

	/** @return null (PHP 8.2 can be :null)*/
	public function getNull(){
		if($this->getBool()){
			throw new AssertionError("Expected null, got non-null.");
		}
		return null;
	}

	public function putString(string $value) : void{
		$this->putInt(strlen($value));
		$this->put($value);
	}

	public function getString() : string{
		return $this->get($this->getInt());
	}

	/** @param BinarySerializable<mixed> $value */
	public function putSerializable(BinarySerializable $value) : void{
		$this->put($value->binarySerialize()->getBuffer());
	}

	/**
	 * @template T of BinarySerializable<mixed>
	 * @param class-string<T> $class
	 * @return T
	 */
	public function getSerializable(string $class){
		/** @var T $x */
		$x = $class::fromBinary($this);
		return $x;
	}

	/** @param string[] $values */
	public function putStringArray(array $values) : void{
		$this->putInt(count($values));
		foreach($values as $value){
			$this->putString($value);
		}
	}

	/** @return string[] */
	public function getStringArray() : array{
		$array = [];
		for($i = 0, $size = $this->getInt(); $i < $size; $i++){
			$array[] = $this->getString();
		}
		return $array;
	}

	/** @param int[] $values */
	public function putByteArray(array $values) : void{
		$this->putInt(count($values));
		foreach($values as $value){
			$this->putByte($value);
		}
	}

	/** @return int[] */
	public function getByteArray() : array{
		$array = [];
		for($i = 0, $size = $this->getInt(); $i < $size; $i++){
			$array[] = $this->getByte();
		}
		return $array;
	}

	/** @param int[] $values */
	public function putIntArray(array $values) : void{
		$this->putInt(count($values));
		foreach($values as $value){
			$this->putInt($value);
		}
	}

	/** @return int[] */
	public function getIntArray() : array{
		$array = [];
		for($i = 0, $size = $this->getInt(); $i < $size; $i++){
			$array[] = $this->getInt();
		}
		return $array;
	}

	/** @param BinarySerializable<mixed>[] $values */
	public function putSerializableArray(array $values) : void{
		$this->putInt(count($values));
		foreach($values as $value){
			$this->put($value->binarySerialize()->getBuffer());
		}
	}

	/**
	 * @template T of BinarySerializable<mixed>
	 * @param class-string<T> $class
	 * @return T[]
	 */
	public function getSerializableArray(string $class) : array{
		$array = [];
		for($i = 0, $size = $this->getInt(); $i < $size; $i++){
			/** @var T $x */
			$x = $class::fromBinary($this);
			$array[] = $x;
		}
		return $array;
	}

	/** @param array<string, string> $values */
	public function putStringStringArray(array $values) : void{
		$this->putInt(count($values));
		foreach($values as $key => $value){
			$this->putString($key);
			$this->putString($value);
		}
	}

	/** @return array<string, string> */
	public function getStringStringArray() : array{
		$array = [];
		for($i = 0, $size = $this->getInt(); $i < $size; $i++){
			$array[$this->getString()] = $this->getString();
		}
		return $array;
	}

	/** @param array<string, BinarySerializable<mixed>> $values */
	public function putStringSerializableArray(array $values) : void{
		$this->putInt(count($values));
		foreach($values as $key => $value){
			$this->putString($key);
			$this->putSerializable($value);
		}
	}

	/**
	 * @template T of BinarySerializable<mixed>
	 * @param class-string<T> $type
	 * @return array<string, T>
	 */
	public function getStringSerializableArray(string $type) : array{
		$array = [];
		for($i = 0, $size = $this->getInt(); $i < $size; $i++){
			$array[$this->getString()] = $this->getSerializable($type);
		}
		return $array;
	}

	//Nullables

	public function putNullable(?string $value) : void{
		$this->putBool($value !== null);
		if($value !== null){
			$this->put($value);
		}
	}

	public function putNullableBool(?bool $value) : void{
		$this->putBool($value !== null);
		if($value !== null){
			$this->putBool($value);
		}
	}

	public function getNullableBool() : ?bool{
		return $this->getBool() ? $this->getBool() : null;
	}

	public function putNullableByte(?int $value) : void{
		$this->putBool($value !== null);
		if($value !== null){
			$this->putByte($value);
		}
	}

	public function getNullableByte() : ?int{
		return $this->getBool() ? $this->getByte() : null;
	}

	public function putNullableInt(?int $value) : void{
		$this->putBool($value !== null);
		if($value !== null){
			$this->putInt($value);
		}
	}

	public function getNullableInt() : ?int{
		return $this->getBool() ? $this->getInt() : null;
	}

	public function putNullableLong(?int $value) : void{
		$this->putBool($value !== null);
		if($value !== null){
			$this->putLong($value);
		}
	}

	public function getNullableLong() : ?int{
		return $this->getBool() ? $this->getLong() : null;
	}

	public function putNullableString(?string $value) : void{
		$this->putBool($value !== null);
		if($value !== null){
			$this->putString($value);
		}
	}

	public function getNullableString() : ?string{
		return $this->getBool() ? $this->getString() : null;
	}

	/** @param BinarySerializable<mixed>|null $value */
	public function putNullableSerializable(?BinarySerializable $value) : void{
		$this->putBool($value !== null);
		if($value !== null){
			$this->putSerializable($value);
		}
	}

	/**
	 * @template T of BinarySerializable<mixed>
	 * @param class-string<T> $class
	 * @return T|null
	 */
	public function getNullableSerializable(string $class){
		if($this->getBool()){
			return $this->getSerializable($class);
		}else{
			return null;
		}
	}

	public function putNullableDouble(?float $value) : void{
		$this->putBool($value !== null);
		if($value !== null){
			$this->putDouble($value);
		}
	}

	public function getNullableDouble() : ?float{
		return $this->getBool() ? $this->getDouble() : null;
	}

	/** @param string[]|null $values */
	public function putNullableStringArray(?array $values) : void{
		if($values === null){
			$this->putBool(false);
			return;
		}
		$this->putBool(true);
		$this->putStringArray($values);
	}

	/** @return string[]|null */
	public function getNullableStringArray() : ?array{
		if(!$this->getBool()){
			return null;
		}
		return $this->getStringArray();
	}

	/** @param int[]|null $values */
	public function putNullableByteArray(?array $values) : void{
		$this->putBool($values !== null);
		if($values !== null){
			$this->putByteArray($values);
		}
	}

	/** @return int[]|null */
	public function getNullableByteArray() : ?array{
		return $this->getBool() ? $this->getByteArray() : null;
	}

	/** @param BinarySerializable<mixed>[]|null $values */
	public function putNullableSerializableArray(?array $values) : void{
		if($values === null){
			$this->putBool(false);
			return;
		}
		$this->putBool(true);
		$this->putSerializableArray($values);
	}

	/**
	 * @template T of BinarySerializable<mixed>
	 * @param class-string<T> $class
	 * @return T[]|null
	 */
	public function getNullableSerializableArray(string $class) : ?array{
		if(!$this->getBool()){
			return null;
		}
		return $this->getSerializableArray($class);
	}

	/** @param array<string, string>|null $values */
	public function putNullableStringStringArray(?array $values) : void{
		$this->putBool($values !== null);
		if($values !== null){
			$this->putStringStringArray($values);
		}
	}

	/** @return array<string, string>|null */
	public function getNullableStringStringArray() : ?array{
		return $this->getBool() ? $this->getStringStringArray() : null;
	}

	/** @param array<string, BinarySerializable<mixed>>|null $values */
	public function putNullableStringSerializableArray(?array $values) : void{
		$this->putBool($values !== null);
		if($values !== null){
			$this->putStringSerializableArray($values);
		}
	}

	/**
	 * @template T of BinarySerializable<mixed>
	 * @param class-string<T> $type
	 * @return array<string, T>|null
	 */
	public function getNullableStringSerializableArray(string $type) : ?array{
		return $this->getBool() ? $this->getStringSerializableArray($type) : null;
	}
}
