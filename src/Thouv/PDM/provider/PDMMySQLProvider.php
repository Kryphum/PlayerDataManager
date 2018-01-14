<?php

namespace Thouv\PDM\provider;

use Thouv\PDM\PlayerDataManager;
use Thouv\PDM\PDMPlayer;
use Thouv\PDM\tasks\PDMMySQLPingTask;

class PDMMySQLProvider implements PDMProvider
{

    protected $plugin;
    private $connection;

    public function __construct(PlayerDataManager $plugin)
    {
        $this->plugin = $plugin;
        $this->connection = $this->makeConnection();
        $this->getConnection()->query("CREATE TABLE IF NOT EXISTS pdm_players (
        player_name VARCHAR(96) PRIMARY KEY,
        properties TEXT);"); // allow up to 96 characters just in case I decide to have a very messy system where data about the player is stored in the player column
    }

    /**
     * @return \mysqli
     */

    private function makeConnection()
    {
        $creds = $this->plugin->getConfig()->get("mysql");
        return new \mysqli($creds["host"], $creds["username"], $creds["password"], $creds["database"], $creds["port"]);
    }

    public function getConnection()
    {
        if(!$this->connection instanceof \mysqli) $this->makeConnection();
        return $this->connection;
    }

    public function getPingClass() {
        return new PDMMySQLPingTask($this->plugin);
    }

    public function registerPlayer(PDMPlayer $player)
    {
        $player_name = $player->getName();

        if($this->getPlayer($player_name)) return false;

        $stmt = $this->getConnection()->prepare("INSERT INTO pdm_players (player_name) VALUES(?);");
        $stmt->bind_param("s", $player_name);
        $player_name = $player->getName();
        $stmt->execute();
        $stmt->close();

        if(!empty($player->getProperties())) $this->updateProperties($player);
    }

    public function unregisterPlayer(string $player_name)
    {
        if(!$this->getPlayer($player_name)) return false;

        $stmt = $this->getConnection()->prepare("DELETE FROM pdm_players WHERE player_name=?;");
        $stmt->bind_param("s", $player_name);
        $stmt->execute();
        $stmt->close();
    }

    public function getAllPlayers()
    {
        $stmt = $this->getConnection()->prepare("SELECT player_name, properties FROM pdm_players;");
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($player_name, $serialized_properties);
        $players = [];
        while($stmt->fetch()) {
            $pdm_player = new PDMPlayer($player_name, true);
            if(is_array($properties = unserialize($serialized_properties))) $pdm_player->setProperties($properties);
            
            $players[$player_name] = $pdm_player;
        }
        $stmt->free_result();
        $stmt->close();

        return $players;
    }

    public function getPlayer(string $player_name)
    {
        $stmt = $this->getConnection()->prepare("SELECT properties FROM pdm_players WHERE player_name=?;");
        $stmt->bind_param("s", $player_name);
        $stmt->execute();
        $stmt->store_result();

        // player doesn't exist
        if($stmt->num_rows === 0) {
            $stmt->free_result();
            $stmt->close();

            return false;
        }

        $stmt->fetch();
        $stmt->bind_result($serialized_properties);

        $pdm_player = new PDMPlayer($player_name, true);
        if(is_array($properties = unserialize($serialized_properties))) $pdm_player->setProperties($properties);

        $stmt->free_result();
        $stmt->close();

        return $pdm_player;
    }

    public function updateProperties(PDMPlayer $player, array $property_names = null)
    {
        $properties = $this->getPlayer($player->getName())->getProperties();

        if(is_null($property_names)) {
            foreach($property_names as $property_name) {
                if(!is_string($property_name)) continue;

                $property = $player->getProperty($property_name);
                if(!$property) {
                    unset($properties[$property_name]);
                    continue;
                }

                if(!$property->toSyncOrNotToSync()) continue;
                $properties[$property_name] = $property;
            }
        } else {
            $properties = $player->getProperties([
                "no_sync" => ["blacklist", [true]]
            ]);
        }

        $stmt = $this->getConnection()->prepare("UPDATE pdm_players SET properties=? WHERE player_name=?;");
        $stmt->bind_param("ss", $properties_serialized, $player_name);
        $properties_serialized = serialize($properties);
        $player_name = $player->getName();
        $stmt->execute();
        $stmt->close();
    }

}