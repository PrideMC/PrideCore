<?php

namespace PrideCore\Discord;

use JaxkDev\DiscordBot\Plugin\Main as DiscordBot;
use pocketmine\event\Listener;

class Bot implements Listener{

    private DiscordBot $bot;

    public function getDiscordBot() : DiscordBot{
        return $this->bot;
    }

    public function ready() :void{
        
    }
}