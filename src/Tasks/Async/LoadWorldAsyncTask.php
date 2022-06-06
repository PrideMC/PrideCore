<?php

declare(strict_types=1);

namespace PrideCore\Tasks\Async;

use PrideCore\PrideCore;
use pocketmine\utils\TextFormat as T;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\scheduler\CancelTaskException;

class LoadWorldAsyncTask extends AsyncTask {
	
	public function onRun() :void {
		
	}
	
	public function onCompletion() :void {
		foreach(PrideCore::getInstance()->getServer()->getWorldManager()->getWorlds() as $world){
			$folderName = $world->getFolderName();
			if(!PrideCore::getInstance()->getServer()->getWorldManager()->isWorldLoaded($folderName)){
				PrideCore::getInstance()->getServer()->getWorldManager()->loadWorld($folderName);
			}
		}
	}
}