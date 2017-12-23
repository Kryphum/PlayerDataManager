<?php

namespace Thouv\PDM;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\Server;

class PlayerDataManager extends PluginBase
{
	
	private static $instance;
	private $players;
	
	public function onLoad()
	{
		self::$instance = $this;
	}
	
	public static function getInstance()
	{
		return self::$instance;
	}

	/**
	 * @param string|Player $player
	 * @return PDMPlayer|bool The newly-created instance of PDMPlayer or false if neither a string nor an instance of Player was passed to the method
	 */
	
	public function registerPlayer($player)
	{
		if(!$player instanceof Player && !$player instanceof string) {
			Server::getInstance()->getLogger()->error('$player is an instance of neither Player nor string in PlayerDataManager::registerPlayer()');
			return false;
		}
		if($player instanceof Player) $player = $player->getName();
		
		$pdm_player = new PDMPlayer();
		$this->players[$player] = $pdm_player;
		return $pdm_player;
	}

	/**
	 * @param string|Player $player 
	 * @return bool True if the player was successfully unregistered, false if the player didn't exist
	 */

	public function unregisterPlayer($player)
	{
		if(!$player instanceof Player && !$player instanceof string) {
			Server::getInstance()->getLogger()->error('$player is an instance of neither Player nor string in PlayerDataManager::unregisterPlayer()');
			return false;
		}
		if($player instanceof Player) $player = $player->getName();

		if(!isset($this->players[$player])) {
			return false;
		}
		unset($this->players[$player]);
		return true;
	}

	/**
	 * @param string|Player $player
	 * @return PDMPlayer|bool The specified player or false if it doesn't exist
	 */
	
	public function getPlayer($player)
	{
		if(!$player instanceof Player && !$player instanceof string) {
			Server::getInstance()->getLogger()->error('$player is an instance of neither Player nor string in PlayerDataManager::getPlayer()');
			return false;
		}
		if($player instanceof Player) $player = $player->getName();

		return isset($this->players[$player]) ? $this->players[$player] : false;
	}
	
}
