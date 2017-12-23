<?php

// yes i am aware that this isnt pretty code and that i forgot to abstract the ListenerFactorySingletonProxyInterface

namespace PDMSample;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\utils\TextFormat;
use Thouv\PDM\PlayerDataManager;
use Thouv\PDM\PDMPropertyFactory;

class PDMSample extends PluginBase implements Listener
{

    private $pdm;

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->pdm = PlayerDataManager::getInstance();
    }

    public function onJoin(PlayerJoinEvent $ev)
    {
        $player = $ev->getPlayer();
        $pdm_player = $this->pdm->getPlayer($player);
        if(!$pdm_player) {
            echo "first join\n";
            $pdm_player = $this->pdm->registerPlayer($player);
        } else {
            $prop = $this->pdm->getPlayer($player)->getProperty("last_joined");
            echo $prop->getPropertyName() . ":" . $prop->getValue() . " ";
        }
        $pdm_player->updateProperty(PDMPropertyFactory::makeProperty("last_joined", time()), true);
        $prop = $this->pdm->getPlayer($player)->getProperty("last_joined");
        echo $prop->getPropertyName() . ":" . $prop->getValue();
    }

    public function onChat(PlayerChatEvent $ev)
    {
        $player = $ev->getPlayer();
        $ev->setMessage(TextFormat::BOLD . strtoupper($player->getName()) . TextFormat::RESET . TextFormat::YELLOW . " last joined at UNIX " . TextFormat::GOLD . $this->pdm->getPlayer($player)->getProperty("last_joined")->getValue() . "\n"
        . TextFormat::RESET . $ev->getMessage());
    }

    public function onQuit(PlayerQuitEvent $ev)
    {
        $name = $ev->getPlayer()->getName();
        if(rand(0, 1)) {
            echo "unregistering " . $name;
            $this->pdm->unregisterPlayer($name);
        }
    }

}