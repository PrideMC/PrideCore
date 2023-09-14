<?php

namespace PrideCore\Utils;

use pocketmine\utils\SingletonTrait;
use PrideCore\Player\Player;

class LobbyManager {
    
    use SingletonTrait;
    
    public array $lobbies = [];

    public static function randomLobby(Player $player) : void{
        
    }
}