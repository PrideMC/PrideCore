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

namespace PrideCore;

use customiesdevs\customies\block\CustomiesBlockFactory;
use customiesdevs\customies\block\Material;
use customiesdevs\customies\block\Model;
use customiesdevs\customies\item\CreativeInventoryInfo;
use libasynCurl\Curl;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\BlockTypeInfo;
use pocketmine\event\EventPriority;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\ResourcePacksInfoPacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\types\resourcepacks\ResourcePackInfoEntry;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Internet;
use pocketmine\utils\NotCloneable;
use pocketmine\utils\NotSerializable;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat as T;
use pocketmine\utils\TextFormat as TF;
use PrideCore\Anticheat\Anticheat;
use PrideCore\Blocks\LuckyBlock;
use PrideCore\Commands\Basic\LinksCommand;
use PrideCore\Commands\Basic\LobbyCommand;
use PrideCore\Commands\Basic\LockerCommand;
use PrideCore\Commands\Basic\NicknameCommand;
use PrideCore\Commands\Basic\ProfileCommand;
use PrideCore\Commands\Basic\RedeemCommand;
use PrideCore\Commands\Basic\RegionCommand;
use PrideCore\Commands\Basic\SettingsCommand;
use PrideCore\Commands\Basic\VoteCommand;
use PrideCore\Commands\Permissions;
use PrideCore\Commands\Staff\BanCommand;
use PrideCore\Commands\Staff\BuildCommand;
use PrideCore\Commands\Staff\DisguiseCommand;
use PrideCore\Commands\Staff\FlyCommand;
use PrideCore\Commands\Staff\FreezeCommand;
use PrideCore\Commands\Staff\GlobalMuteCommand;
use PrideCore\Commands\Staff\KickCommand;
use PrideCore\Commands\Staff\MaintenanceCommand;
use PrideCore\Commands\Staff\MuteCommand;
use PrideCore\Commands\Staff\PardonCommand;
use PrideCore\Commands\Staff\RankCommand;
use PrideCore\Commands\Staff\WarnCommand;
use PrideCore\Discord\DiscordWebhook;
use PrideCore\Events\PlayerListener;
use PrideCore\Events\ServerListener;
use PrideCore\Player\Skin;
use PrideCore\Tasks\BroadcastTask;
use PrideCore\Tasks\MotdTask;
use PrideCore\Tasks\ParticleUpdateTask;
use PrideCore\Tasks\RegionUpdateTask;
use PrideCore\Tasks\ScoreboardUpdateTask;
use PrideCore\Utils\Cache;
use PrideCore\Utils\Config;
use PrideCore\Utils\Database;
use PrideCore\Utils\Rank;
use PrideCore\Utils\Utils;
use function basename;
use function glob;
use function imagecreatefrompng;
use function imagedestroy;
use function is_string;

/**
 * PrideCore for PocketMine-MP
 */
class Core extends PluginBase
{
	use SingletonTrait;
	use NotCloneable;
	use NotSerializable;

	public static bool $mute = false;

	public static bool $maintenance = false;
	public static bool $connected = false;

	private Skin $skin;

	public const PREFIX = T::RED . T::BOLD . "P" . T::GREEN . "r" . T::BLUE . "i" . T::GOLD . "d" . T::YELLOW . "e" . T::WHITE . "MC" . T::RESET;
	public const ARROW = T::RESET . T::AQUA . T::BOLD . "»" . T::RESET;

	private array $encryptionKeys = [];

	protected function onLoad() : void
	{
		self::setInstance($this);
		(new Permissions());
		$this->saveResource("server.yml");
		$this->saveResource("database.yml");
		Database::getInstance()->init();
	}

	protected function onEnable() : void
	{
		Anticheat::load();
		Curl::register($this);
		$this->saveResources();
		$this->encryptPacks();
		$this->loadTasks();
		$this->registerCommands();
		$this->registerEvents();
		$this->loadPersona();
		$this->stopWorldTime();
		$this->setWorldTime();
		$this->disableEmoteMessages();
		$this->checkServerhasInternet();
		DiscordWebhook::getInstance()->sendEnabled();
		//$this->registerBlocks();
	}

	protected function onDisable() : void
	{
		Database::getInstance()->close();
		DiscordWebhook::getInstance()->sendDisabled();
	}

	private function loadPersona() : void {
		$skinPaths = glob($this->getDataFolder() . "default.png");

		foreach ($skinPaths as $skinPath) {
			$image = imagecreatefrompng($skinPath);

			if ($image === false) {
				continue;
			}

			$this->skin = new Skin("skin." . basename($skinPath), Utils::fromImage($image), "", "geometry.humanoid.custom");
			@imagedestroy($image);
		}

		TypeConverter::getInstance()->setSkinAdapter(new Skin());
	}

	private function loadTasks() : void
	{
		$this->getScheduler()->scheduleRepeatingTask(new RegionUpdateTask(), 20 * 30);
		$this->getScheduler()->scheduleRepeatingTask(new BroadcastTask(), 20 * 60);
		$this->getScheduler()->scheduleRepeatingTask(new MotdTask(), 20 * 60);
		$this->getScheduler()->scheduleRepeatingTask(new ScoreboardUpdateTask(), 20 * 5);
		$this->getScheduler()->scheduleRepeatingTask(new ParticleUpdateTask(), 20);
	}

	private function registerCommands() : void
	{
		foreach ([
			"kick",
			"ban",
			"ban-ip",
			"pardon",
			"pardon-ip",
			"status",
			"version",
			"me",
			"say",
			"setworldspawn",
			"spawnpoint",
			"tell",
			"whitelist", // favior of MaintenanceCommand
			"checkperm",
			"seed",
			"list",
			"title",
			"kill",
			"banlist", // not usefull
			"particle", // not usefull
			"listperms",
		] as $command) {
			if (($cmd = $this->getServer()->getCommandMap()->getCommand($command)) !== null) {
				$this->getServer()->getCommandMap()->unregister($cmd);
				$this->getServer()->getLogger()->debug("Unloaded \"" . $command . "\" command.");
			} else {
				$this->getServer()->getLogger()->debug("The command \"" . $command . "\" is cannot be unloaded.");
			}
		}

		$this->getServer()->getCommandMap()->registerAll($this->getDescription()->getName(), [
			new RankCommand(),
			new ProfileCommand(),
			new LobbyCommand(),
			new DisguiseCommand(),
			new MaintenanceCommand(),
			new FlyCommand(),
			new BuildCommand(),
			new BanCommand(),
			new WarnCommand(),
			new FreezeCommand(),
			new PardonCommand(),
			new MuteCommand(),
			new KickCommand(),
			new GlobalMuteCommand(),
			new LinksCommand(),
			new VoteCommand(),
			new RegionCommand(),
			new RedeemCommand(),
			new NicknameCommand(),
			new LockerCommand(),
			new SettingsCommand(),
		]);
	}

	private function registerEvents() : void
	{
		$this->getServer()->getPluginManager()->registerEvents(new PlayerListener(), $this);
		$this->getServer()->getPluginManager()->registerEvents(new ServerListener(), $this);
	}

	public function getConfigs() : Config
	{
		return new Config();
	}

	public function getRanks() : Rank
	{
		return new Rank();
	}

	private function encryptPacks() : void // thanks to alvin0219 for sharing this code.
	{
		foreach ($this->getServer()->getResourcePackManager()->getResourceStack() as $resourcePack) {
			$uuid = $resourcePack->getPackId();
			if ($this->getConfigs()->getServerConfig()->getNested("resource-packs.{$uuid}", "") !== "") {
				$encryptionKey = $this->getConfigs()->getServerConfig()->getNested("resource-packs.{$uuid}");
				$this->encryptionKeys[$uuid] = $encryptionKey;
				$this->getLogger()->debug("Successfully loaded encryption key for resource pack: $uuid, with key: $encryptionKey");
			}
		}
		$this->getServer()->getPluginManager()->registerEvent(DataPacketSendEvent::class, function (DataPacketSendEvent $event) : void {
			$packets = $event->getPackets();
			foreach ($packets as $packet) {
				if ($packet instanceof ResourcePacksInfoPacket) {
					foreach ($packet->resourcePackEntries as $index => $entry) {
						if (isset($this->encryptionKeys[$entry->getPackId()])) {
							$contentId = $this->encryptionKeys[$entry->getPackId()];
							$packet->resourcePackEntries[$index] = new ResourcePackInfoEntry($entry->getPackId(), $entry->getVersion(), $entry->getSizeBytes(), $contentId, $entry->getSubPackName(), $entry->getPackId(), $entry->hasScripts(), $entry->isRtxCapable());
						}
					}
				}
			}
		}, EventPriority::HIGHEST, $this);
	}

	private function registerBlocks() : void{
		$material = new Material(
			Material::TARGET_ALL,
			'lucky_block',
			Material::RENDER_METHOD_ALPHA_TEST
		);
		$model = new Model(
			[$material],
			'geometry.lucky_block',
			new Vector3(-8, 0, -8),
			new Vector3(16, 16, 16)
		);
		$creativeInfo = new CreativeInventoryInfo(
			CreativeInventoryInfo::CATEGORY_CONSTRUCTION,
			CreativeInventoryInfo::NONE
		);
		CustomiesBlockFactory::getInstance()->registerBlock(
			static fn() => new LuckyBlock(
				new BlockIdentifier(BlockTypeIds::newId(), null),
				'§r§eLucky Block',
				new BlockTypeInfo(new BlockBreakInfo(1))
			),
			'mcpride:lucky_block',
			$model,
			$creativeInfo
		);
	}

	private function stopWorldTime() : void
	{
		foreach ($this->getServer()->getWorldManager()->getWorlds() as $world) {
			$world->setTime(0);
			$this->getServer()->getLogger()->debug("Set time to 0 in \"" . $world->getFolderName() . "\".");
			$world->stopTime();
			$this->getServer()->getLogger()->debug("Stopped \"" . $world->getFolderName() . "\" the world time.");
		}
	}

	private function setWorldTime() : void
	{
		foreach ($this->getServer()->getWorldManager()->getWorlds() as $world) {
			$world->setTime(12000);
			$this->getServer()->getLogger()->debug("Set to 12000 the \"" . $world->getFolderName() . "\" of world time.");
		}
	}

	public function saveResources() : void
	{
		foreach ($this->getResources() as $resource) {
			$this->saveResource($resource->getFilename());
		}

		foreach ($this->getConfig()->get("capes") as $resource) {
			$this->saveResource("capes/" . $resource);
		}

		foreach ($this->getConfig()->get("costumes") as $resource) {
			$this->saveResource("costumes/" . $resource);
		}
	}

	public function getCache() : Cache
	{
		return Cache::getInstance();
	}

	public function disableEmoteMessages() : void{
		$this->getServer()->getPluginManager()->registerEvent(DataPacketSendEvent::class, function (DataPacketSendEvent $event) : void {
			foreach ($event->getPackets() as $packet) {
				if ($packet instanceof StartGamePacket) {
					$packet->levelSettings->muteEmoteAnnouncements = true;
				}
			}
		}, EventPriority::HIGHEST, $this);
	}

	public function checkServerhasInternet() : void{
		if(!is_string(Internet::getIp())){
			$this->getServer()->getLogger()->info(Core::PREFIX . " " . Core::ARROW . " " . TF::RED . "The server internet connection is currently offline. Please check server-connection and try again. Forcing to stop webhook post to prevent crashes.");
			Core::$connected = false;
		} else {
			Core::$connected = true;
		}
	}
}
