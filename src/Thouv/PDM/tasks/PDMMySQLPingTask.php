<?php

namespace Thouv\PDM\tasks;

use pocketmine\scheduler\PluginTask;
use Thouv\PDM\PlayerDataManager;

class PDMMySQLPingTask extends PluginTask
{

    public function onRun(int $currentTick)
    {
        $this->getOwner()->getProvider()->getConnection()->ping();
    }

}