<?php

namespace Thouv\PDM;

use pocketmine\Player;

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
	
	public function registerPlayer($player)
	{
		if($player instanceof Player) $player = $player->getName();
		
		$pdm_player = new PDMPlayer();
		$this->players[$player] = $pdm_player;
		return $pdm_player;
	}
	
	public function getPlayer($player)
	{
		if($player instanceof Player) $player = $player->getName();
		return isset($this->players[$player]) ? $this->players[$player] : false;
	}
	
}
