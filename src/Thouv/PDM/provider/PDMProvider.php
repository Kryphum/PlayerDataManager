<?php

namespace Thouv\PDM\provider;

use Thouv\PDM\PlayerDataManager;
use Thouv\PDM\PDMPlayer;

interface PDMProvider
{

    public function __construct(PlayerDataManager $plugin);

    public function registerPlayer(PDMPlayer $player);
    public function unregisterPlayer(string $player_name);
    public function getAllPlayers();
    public function getPlayer(string $player_name);
    public function updateProperties(PDMPlayer $player, array $property_names = null);

}