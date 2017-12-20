<?php

namespace Thouv\PDM;

final class PlayerDataManager
{
	
	private static $instance;
	private $players;
	
	private function __construct()
	{}
	
	public static function getInstance()
	{
		if(!self::$instance) self::$instance = new PlayerDataManager();
		return self::$instance;
	}
	
	public function registerNewPlayer($player)
	{
		if($player instanceof \pocketmine\Player) $player = $player->getName();
		
		$pdm_player = new PDMPlayer();
		$this->players[$player] = $pdm_player;
		return $pdm_player;
	}
	
	public function getPlayer(string $name)
	{
		return isset($this->players[$name]) ? $this->players[$name] : false;
	}
	
}
