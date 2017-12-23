<?php

use pocketmine\Server;

class PDMPlayer
{
	
	public $properties = [];

	/**
	 * Sets a property.
	 * @param array $properties The properties that are to be set
	 * @param bool $update Updates the property if it already exists when set to true
	 * @return array An array of PDMProperty; the player's properties
	 */
	
	public function setProperties(array $properties, bool $update = false)
	{
		foreach($properties as $key => $property) {
			if(!$property instanceof PDMProperty) {
				Server::getInstance()->getLogger()->error('Element ' . $key . ' of properties array is not an instance of PDMProperty');
				continue;
			}
			if(!$update && $this->getProperty($property->getPropertyName())) {
				Server::getInstance()->getLogger()->error('Attempted to set existent property ' . $property->getPropertyName() . ' with $no_update set to true');
				continue;
			}
			$this->properties[$property->getPropertyName()] = $property;
		}
		return $this->properties;
	}
	
	public function unsetProperties(array $property_names)
	{
		foreach($property_names as $property_name) {
			if(!$this->getProperty($property_name)) {
				Server::getInstance()->getLogger()->error('Attempted to unset nonexistent property ' . $property_name);
				continue;
			}
			unset($this->properties[$property_name]);
		}
	}
	
	public function getProperties()
	{
		return $this->properties;
	}

	/**
	 * @return PDMProperty|bool The specified property or false if it doesn't exist
	 */
	
	public function getProperty(string $property_name)
	{
		return isset($this->getProperties()[$property_name]) ? $this->getProperties()[$property_name] : false;
	}

	/**
	 * Updates an existing property.
	 * @param PDMProperty $property The property that will replace the previous one
	 * @param bool $set_if_nonexistent Sets the property if it doesn't already exist when set to true
	 * @return PDMProperty|bool The property that was passed to the method or false if it doesn't exist and $set_if_nonexistent is false
	 */
	
	public function updateProperty(PDMProperty $property, bool $set_if_nonexistent = false)
	{
		if(!$this->getProperty($property->getPropertyName())) {
			if($set_if_nonexistent) {
				Server::getInstance()->getLogger()->notice('Setting nonexistent property ' . $property->getPropertyName() . ' in PDMPlayer::updateProperty()');
				$this->setProperties([$property]);
				return $property;
			}
			Server::getInstance()->getLogger()->error('Attempted to update nonexistent property ' . $property->getPropertyName());
			return false;
		}
		
		$this->properties[$property->getPropertyName()] = $property;
		return $property;
	}
	
}