<?php

declare(strict_types=1);

namespace PrideCore;

use pocketmine\player\Player;
use pocketmine\entity\Living;
use pocketmine\player\GameMode;
use PrideCore\PrideCore;
use PrideCore\Tasks\Regular\KilledTask;

class RPlayer extends Player 
{
	
	public function knockBack(float $x, float $z, float $force = 0.4, ?float $verticalLimit = 0.4): void
    {
        $xzKB = 0.4;
        $yKb = 0.4;
        $f = sqrt($x * $x + $z * $z);
        if ($f <= 0) {
            return;
        }
        if (mt_rand() / mt_getrandmax() > $this->knockbackResistanceAttr->getValue()) {
            $f = 1 / $f;
            $motion = clone $this->motion;
            $motion->x /= 2;
            $motion->y /= 2;
            $motion->z /= 2;
            $motion->x += $x * $f * $xzKB;
            $motion->y += $yKb;
            $motion->z += $z * $f * $xzKB;
            if ($motion->y > $yKb) {
                $motion->y = $yKb;
            }
            $this->setMotion($motion);
        }
    }
	
	
	
	
	public function playSound(string $sound, float $minimumVolume = 1.0, float $volume = 1.0, float $pitch = 1.0)
	{
		
        $pk = new PlaySoundPacket();
        $pk->soundName = $sound;
        $pk->volume = $volume > $minimumVolume ? $minimumVolume : $volume;
        $pk->pitch = $pitch;
        $pk->x = $this->x;
        $pk->y = $this->y;
        $pk->z = $this->z;
		$this->getNetworkSession()->sendDataPacket($pk);
		
	}
	
	
	
	
	public function setCape(string $cape) : void
	{
		
        $oldSkin = $this->getSkin();
        $cape = $this->createCape($cape);
        $setCape = new Skin($oldSkin->getSkinId(), $oldSkin->getSkinData(), $cape, $oldSkin->getGeometryName(), $oldSkin->getGeometryData());
        $this->setSkin($setCape);
        $this->sendSkin();
		
    }
	
	
	
	
	public function createCape(string $path): string
	{
        $img = @imagecreatefrompng($path);
        $bytes = '';
        $l = (int)@getimagesize($path)[1];
        for ($y = 0; $y < $l; $y++) {
            for ($x = 0; $x < 64; $x++) {
                $rgba = @imagecolorat($img, $x, $y);
                $a = ((~((int)($rgba >> 24))) << 1) & 0xff;
                $r = ($rgba >> 16) & 0xff;
                $g = ($rgba >> 8) & 0xff;
                $b = $rgba & 0xff;
                $bytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($img);
        return $bytes;
    }
	
	public function kill() :void
    {
        
        if(!$this->spawned){
            return;
        }

        $this->onDeath();
    }
}