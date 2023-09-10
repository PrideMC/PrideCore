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

use pocketmine\entity\Skin;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat as TF;
use PrideCore\Core;
use function chr;
use function getimagesize;
use function imagecolorat;
use function imagecreatefrompng;
use function imagedestroy;
use function str_contains;

/**
 * Player main class.
 */
class Player extends \pocketmine\player\Player
{

	private bool $mute = false;

	private bool $build = false;

	private bool $frozen = false;

	private bool $visibleAllPlayers = false;

	private bool $always_sprinting = false;

	private ?bool $onOtherServer = null;

	private ?string $currentServer = null;

	private int $rankId = 0;

	private int $tag = 0;

	private int $particle_id = 0;

	private int $particle_color = 0;

	private int $particle_type = 0;

	private string $tags = "";
	/** @var string|null|null */
	private ?string $capes_owned = null;

	private string $particle_owned = "";
	/** @var string|null|null */
	private ?string $nick = null;

	/** @var Skin|null|null */
	private ?Skin $oldSkin = null;

	public function getActiveParticle() : int {
		return $this->particle_id;
	}

	public function getParticleColor() : int {
		return $this->particle_color;
	}

	public function getOwnedParticles() : string {
		return $this->particle_owned;
	}

	public function setOwnedParticles(string $particles) : void {
		$this->particle_owned = $particles;
	}

	public function setParticleColor(int $color) : void {
		$this->particle_color = $color;
	}

	public function setActiveParticle(int $particle) : void {
		$this->particle_id = $particle;
	}

	public function setParticleType(int $particle) : void {
		$this->particle_type = $particle;
	}

	public function setMuted(bool $confirm = true) : void
	{
		$this->mute = $confirm;
	}

	public function setFrozen(bool $confirm = true) : void
	{
		$this->frozen = $confirm;
		$this->setNoClientPredictions($confirm); // call()
	}

	public function setBuilder(bool $confirm = true) : void
	{
		$this->build = $confirm;
	}

	public function isBuilder() : bool
	{
		return $this->build;
	}

	public function setVisibleAllPlayers(bool $confirm = true) : void
	{
		$this->visibleAllPlayers = $confirm;
	}

	public function isVisibleAllPlayers() : bool
	{
		return $this->visibleAllPlayers;
	}

	public function setAlwaysSprinting(bool $confirm = true) : void
	{
		$this->always_sprinting = $confirm;
	}

	public function isAlwaysSprinting() : bool
	{
		return $this->always_sprinting;
	}

	public function isFrozen() : bool
	{
		return $this->frozen;
	}

	public function isMuted() : bool
	{
		return $this->mute;
	}

	public function kill() : void
	{
		if (!$this->spawned) {
			return;
		}

		$this->onDeath();
		$this->startDeathAnimation();
	}

	public function playSound(string $sound, int $volume = 100, int $pitch = 1) : void
	{
		$this->getNetworkSession()->sendDataPacket(PlaySoundPacket::create($sound, $this->getLocation()->getX(), $this->getLocation()->getY(), $this->getLocation()->getZ(), $volume, $pitch));
	}

	public function stopSound(string $sound, bool $stopAll = false)
	{
		$this->getNetworkSession()->sendDataPacket(PlaySoundPacket::create($sound, $stopAll));
	}

	public function getCurrentCape() : ?string
	{
		return $this->cape ?? null;
	}

	public function setCurrentCape(string $cape) : void
	{
		$this->setCape($cape);
		$this->cape = $cape;
	}

	public function setSkinCape(string $cape) : void
	{
		$oldSkin = $this->getSkin();
		$cape = $this->createCape($cape);
		$this->oldSkin = new Skin($oldSkin->getSkinId(), $oldSkin->getSkinData(), $oldSkin->getCapeData(), $oldSkin->getGeometryName(), $oldSkin->getGeometryData());
		$setCape = new Skin($oldSkin->getSkinId(), $oldSkin->getSkinData(), $cape, $oldSkin->getGeometryName(), $oldSkin->getGeometryData());
		$this->setSkin($setCape);
		$this->sendSkin();
	}

	public function createCape(string $cape) : string
	{
		$path = $this->plugin->getDataFolder() . "capes/{$cape}.png";
		$img = @imagecreatefrompng($path);
		$bytes = '';
		$l = (int) @getimagesize($path)[1];
		for ($y = 0; $y < $l; $y++) {
			for ($x = 0; $x < 64; $x++) {
				$rgba = @imagecolorat($img, $x, $y);
				$a = ((~((int) ($rgba >> 24))) << 1) & 0xff;
				$r = ($rgba >> 16) & 0xff;
				$g = ($rgba >> 8) & 0xff;
				$b = $rgba & 0xff;
				$bytes .= chr($r) . chr($g) . chr($b) . chr($a);
			}
		}
		@imagedestroy($img);
		return $bytes;
	}

	public function removeCape() : void{
		$this->setSkin(new Skin($this->getSkin()->getSkinId(), $this->getSkin()->getSkinData(), "", $this->getSkin()->getGeometryName(), $this->getSkin()->getGeometryData()));
		$this->sendSkin();
	}

	public function resetCape() : void{
		$this->setSkin($this->oldSkin);
		$this->sendSkin();
		$this->oldSkin = null;
	}

	public function getRankId() : int
	{
		return $this->rankId;
	}

	public function setRankId(int $id) : void
	{
		$this->rankId = $id;
	}

	public function getTag() : int{
		return $this->tag;
	}

	public function setTag(int $tag) : void{
		$this->tag = $tag;
	}

	/**
	 * @return [type]
	 */
	public function setTempMute(int $duration = 1200){
		$this->setMuted(true);
		Core::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function(){
			$this->setMuted(false);
			$this->sendMessage(Core::PREFIX . " " . Core::ARROW . " " . TF::GREEN . "You're now unmuted. You can now able to chat and interact with other people.");
		}), $duration); // secret method ;)
	}

	public function getOwnedTags() : ?string {
		return $this->tags;
	}

	public function setOwnedTags(string $list) : void {
		$this->tags = $list;
	}

	public function getOwnedCapes() : ?string {
		return $this->capes_owned;
	}

	public function setOwnedCape(string $list) : void {
		$this->capes_owned = $list;
	}

	public function setNick(string $name) : void{
		$this->nick = $name;

		$this->setNametag(TF::GRAY . $this->nick);
	}

	public function isNick() : bool{
		if($this->nick != null) return true;

		return false;
	}

	public function getNick() : string {
		if($this->nick === null) return $this->getName();

		return $this->nick;
	}

	public function removeNick() : void{
		$this->nick = null;
	}

	public function isInHub() : bool{

		if(str_contains("lb", $this->getWorld()->getFolderName())){
			return true;
		}

		return false;
	}

	public function isOnOtherServer() : bool{
		return $this->onOtherServer ?? false;
	}

	public function setOnOtherServer(string $server) : void {
		$this->onOtherServer = true;

		$this->currentServer = $server;
	}

	public function removeOnOtherServer() : void{
		$this->onOtherServer = false;
		$this->currentServer = null;
	}

	/**
	 * @return string
	 */
	public function getCurrentServer() : string|bool {
		return $this->currentServer ?? false;
	}
}
