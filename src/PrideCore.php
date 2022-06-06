<?php

declare(strict_types=1);

namespace PrideCore;

use PrideCore\RPlayer;
use PrideCore\Utils\Forms\Forms;
use PrideCore\Utils\Forms\Form;
use PrideCore\Utils\Forms\SimpleForm;
use PrideCore\Utils\Forms\ModalForm;
use PrideCore\Utils\Forms\CustomForm;
use PrideCore\Utils\RankManager;
use PrideCore\Utils\InventoryManager;
use PrideCore\Utils\FormManager;
use PrideCore\Utils\Utils;
use PrideCore\Utils\Duels;
use PrideCore\Utils\Events;
use PrideCore\Utils\Interfaces\IUtils;
use PrideCore\Utils\Interfaces\IForm;
use PrideCore\Utils\Interfaces\IPermission;
use PrideCore\Utils\Interfaces\IMessage;
use PrideCore\Utils\Interfaces\IManager;
use PrideCore\Listeners\PlayerListener;
use PrideCore\Listeners\LobbyListener;
use PrideCore\Listeners\PracticeListener;
use PrideCore\Tasks\Async\LoadWorldAsyncTask;
use PrideCore\Tasks\Regular\MotdTask;
use PrideCore\Tasks\Regular\BroadcastTask;
use PrideCore\Tasks\Regular\HealthTask;
use PrideCore\Commands\SpawnCommand;
use PrideCore\Commands\RekitCommand;
use PrideCore\Commands\InfoCommand;
use PrideCore\Commands\LinksCommand;
use PrideCore\Commands\Moderations\BanCommand;
use PrideCore\Commands\Moderations\FreezeCommand;
use PrideCore\Commands\Moderations\KickCommand;
use PrideCore\Commands\Moderations\PardonCommand;
use PrideCore\Commands\Managements\MaintenanceCommand;
use PrideCore\Database\Database;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;

class PrideCore extends PluginBase 
{
	
	public static PrideCore $instance;
	
	
	public function onLoad() :void 
	{
		self::$instance = $this;
	}
	
	
	
	
	
	public function onEnable() :void 
	{
		$this->unloadCommands();
		$this->loadCommands();
		$this->launchTasks();
		$this->loadWorlds();
		$this->clearEntities();
		$this->loadListener();
	}
	
	
	
	
	public static function getInstance() :self
	{
		return self::$instance;
	}
	
	
	
	
	private function unloadCommands() 
	{
		
		$command = [
			"me", 
			"kill", 
			"pl", 
			"ver", 
			"say", 
			"ban", 
			"kick", 
			"unban", 
			"tell", 
			"transferserver", 
			"seed", 
			"list", 
			"checkperm",
		];
		
		foreach ($command as $cmd) {
			$this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand($cmd));
		}
		
	}
	
	
	
	
	private function loadCommands() 
	{
		
		foreach([
			/// MODERATIONS ///
			new FreezeCommand(),
			new BanCommand(),
			new KickCommand(),
			new PardonCommand(),
			/// DEFAULTS ///
			new SpawnCommand(),
			new RekitCommand(),
			new LinksCommand(),
			/// MANAGEMENTS ///
			new MaintenanceCommand(),
		] as $command){
			$this->getServer()->getCommandMap()->register("PrideCore", $command);
		}
		
	}
	
	
	
	
	private function launchTasks() 
	{
		
		/// SERVER CLASS ////
		$this->getScheduler()->scheduleRepeatingTask(new MotdTask(), 20*60);
		$this->getScheduler()->scheduleRepeatingTask(new BroadcastTask(), 1*60*20);
		
		/// PLAYER CLASS ///
		$this->getScheduler()->scheduleRepeatingTask(new HealthTask(), 2);
		
		/// LOAD WORLD CLASS ///
		$this->getServer()->getAsyncPool()->submitTask(new LoadWorldAsyncTask(), $this);
		
	}
	
	
	
	
	
	private function loadWorlds() 
	{
		
		foreach (array_diff(scandir($this->getServer()->getDataPath() . "worlds"), ["..", "."]) as $WorldName) {
            $this->getServer()->getWorldManager()->loadWorld($WorldName);
        }
        foreach($this->getServer()->getWorldManager()->getWorlds() as $world) {
            $world->setTime(0);
            $world->stopTime();
        }
	}
	
	
	
	
	
	public function getPropertyType($value): int 
	{
		
        if(is_bool($value)) return 1;
        if(is_int($value)) return 2;
        return 0;
		
    }
	
	
	
	
	private function clearEntities(): void
	{
		
        foreach ($this->getServer()->getWorldManager()->getWorlds() as $world) {
            foreach ($world->getEntities() as $entity) {
                $entity->flagForDespawn();
            }
        }
		
    }
	
	
	
	
	private function loadListener() : void
	{
		
        foreach ([
			/// PLAYER CLASS //
            new PlayerListener(),
			/// PRACTICE CLASS ///
            new PracticeListener(),
			/// LOBBY CLASS ///
			new LobbyListener(),
            ] as $class) {
				$this->getServer()->getPluginManager()->registerEvents($class, $this);
        }
		
    }
	
	
	
	
}