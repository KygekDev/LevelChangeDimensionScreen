<?php

declare(strict_types=1);

namespace Dapro718\LevelChangeDimensionScreen;

use pocketmine\Player;
use pocketmine\network\mcpe\protocol\ChangeDimensionPacket;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\math\Vector3;

final class LCDSMain extends PluginBase implements Listener{

    private static $levels = [];
    
    public function onEnable(): void{
        foreach($this->getConfig()->get("levels") as $name=>$id){
            self::$levels[strtolower($name)] = $id["dimension"];
        }
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    
    public function onLevelChange(EntityTeleportEvent $event): void{
        $player = $event->getEntity();
        if($player instanceof Player){
            if($event->getPosition()->getLevel()->getFolderName() !== $player->getLevel()->getFolderName()){
                if(isset(self::$levels[strtolower($event->getPosition()->getLevel()->getFolderName())])){
                    if(!$player->hasPermission("levelchangedimensionscreen.noscreen")){
                        $pk = new ChangeDimensionPacket();
                        $pk->dimension = self::$levels[strtolower($event->getPosition()->getLevel()->getFolderName())];
                        $pk->position = new Vector3($event->getPosition()->getX(), $event->getPosition()->getY(), $event->getPosition()->getZ());
                        $pk->respawn = false;
                        $player->sendPacket($pk);
                    }
                }
            }
        }
    }
}
