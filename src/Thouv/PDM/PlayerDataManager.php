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
	 * @param bool $reset_if_exists Re-registers (which also resets) the player if it already exists
	 * @return PDMPlayer|bool The newly-created instance of PDMPlayer or false if neither a string nor an instance of Player was passed to the method
	 */
	
	public function registerPlayer(string $player, bool $reset_if_exists = false)
	{
		if($this->getPlayer($player) && !$reset_if_exists) {
			Server::getInstance()->getLogger()->warning('Attempted to register an existing player ' . $player);
			return false;
		}
		$pdm_player = new PDMPlayer();
		$this->players[$player] = $pdm_player;
		return $pdm_player;
	}

	/**
	 * @param string|Player $player 
	 * @return bool True if the player was successfully unregistered, false if the player didn't exist
	 */

	public function unregisterPlayer(string $player)
	{
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
	
	public function getPlayer(string $player)
	{
		return isset($this->players[$player]) ? $this->players[$player] : false;
	}
	
}
