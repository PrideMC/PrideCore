<?php

namespace PrideCore\Anticheat\Modules;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\TextPacket;
use PrideCore\Anticheat\Anticheat;
use PrideCore\Player\Player;
use PrideCore\Utils\Rank;

class BadPackets extends Anticheat implements Listener{
    
    public function __construct(){
        parent::__construct(Anticheat::BADPACKET);
    }

    public array $packetsPerSecond = [];

    public const MAX_PACKETS = 650;

    public const MESSAGE_LIMIT = 500;

    // limit the packet recieve.
    public function badPacketV1(DataPacketReceiveEvent $event) : void{
        $player = $event->getOrigin()->getPlayer();
        $packet = $event->getPacket();

        if (!($player instanceof Player)) {
            return;
        }

        if (!(isset($this->packetsPerSecond[$player->getUniqueId()->getBytes()]))) {
            $this->packetsPerSecond[$player->getUniqueId()->getBytes()] = 0;
        }

        if($this->packetsPerSecond[$player->getUniqueId()->getBytes()] > BadPackets::MAX_PACKETS){
            $this->kick($player, $this->typeToReasonString($this->getFlagId()));
        } else {
            $this->packetsPerSecond[$player->getUniqueId()->getBytes()]++;
        }
    }

    // some people bypass message limit, so to prevent message vulnerabilities, we check this.
    public function badPacketV2(DataPacketReceiveEvent $event) : void{
        $player = $event->getOrigin()->getPlayer();
        $packet = $event->getPacket();

        if (!($player instanceof Player)) {
            return;
        }

        if ($packet instanceof TextPacket) {
            if (mb_strlen($packet->message) > BadPackets::MESSAGE_LIMIT) {
                if ($player->getRankId() === Rank::OWNER) {
                    return;
                }
                $this->fail($player);
            }
        }
    }
}