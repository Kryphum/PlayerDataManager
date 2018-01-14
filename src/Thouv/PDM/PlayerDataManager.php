<?php

namespace Thouv\PDM;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\Server;
use Thouv\PDM\provider\PDMMySQLProvider;

final class PlayerDataManager extends PluginBase
{
	
	private static $instance;
	private $players;
	private $provider;
	public $sync_enabled;
	
	public function onLoad()
	{
		self::$instance = $this;
	}

	public function onEnable()
	{
		$this->sync_enabled = $this->getConfig()->get("enable-sync");
		if($this->sync_enabled) {
			$this->setProvider();
			$this->getServer()->getScheduler()->scheduleRepeatingTask($this->getProvider()->getPingClass(), 20 * 60 * 5);

			$this->players = $this->getProvider()->getAllPlayers();
		}
	}
	
	public static function getInstance()
	{
		return self::$instance;
	}

	public function getProvider()
	{
		if(!$this->sync_enabled) return false;

		if(!$this->provider instanceof PDMProvider) $this->setProvider();

		return $this->provider;
	}

	private function setProvider()
	{
		if(!$this->sync_enabled) return false;

		$this->provider = new PDMMySQLProvider(self::$instance);
	}

	/**
	 * @param string $player_name
	 * @param bool $reset_if_exists Re-registers (which also resets) the player if it already exists
	 * @return PDMPlayer The newly-created instance of PDMPlayer
	 */
	
	public function registerPlayer(string $player_name, bool $sync = false, bool $reset_if_exists = false)
	{
		if($this->getPlayer($player_name) && !$reset_if_exists) {
			Server::getInstance()->getLogger()->warning('Attempted to register an existing player ' . $player_name);
			return false;
		}

		$this->unregisterPlayer($player_name);
		$pdm_player = new PDMPlayer($player_name, $sync);
		$this->players[$player_name] = $pdm_player;

		if($sync && $provider = $this->getProvider()) $provider->registerPlayer($pdm_player);

		return $pdm_player;
	}

	/**
	 * @param string $player_name
	 * @return bool True if the player was successfully unregistered, false if the player didn't exist
	 */

	public function unregisterPlayer(string $player_name)
	{
		if(!isset($this->players[$player_name])) {
			return false;
		}

		unset($this->players[$player_name]);
		
		if($provider = $this->getProvider()) $provider->unregisterPlayer($player_name);

		return true;
	}

	/**
	 * @param string $player_name
	 * @return PDMPlayer|bool The specified player or false if it doesn't exist
	 */
	
	public function getPlayer(string $player_name)
	{
		return isset($this->players[$player_name]) ? $this->players[$player_name] : false;
	}
	
}
