<?php

namespace Thouv\PDM;

use pocketmine\Server;

class PDMPlayer
{
    
    protected $properties = [];
    protected $name;
    private $sync;

    public function __construct(string $name, bool $sync)
    {
        $this->name = $name;

        $pdm_sync = PlayerDataManager::getInstance()->sync_enabled;
        if(!$pdm_sync) $sync = false;
        if(is_null($sync)) $sync = PlayerDataManager::getInstance()->sync_enabled;
        $this->sync = $sync;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets a property.
     * @param array $properties The properties that are to be set
     * @param bool $update Updates the property if it already exists when set to true
     * @return array An array of PDMProperty; the player's properties
     */
    
    public function setProperties(array $properties, bool $update_if_existent = false, bool $hydrofoil = true)
    {
        foreach($properties as $key => $property) {
            if(!$property instanceof PDMProperty) {
                Server::getInstance()->getLogger()->error('Element ' . $key . ' of properties array is not an instance of PDMProperty in PDMPlayer::setProperties()');
                unset($properties[$key]);
                continue;
            }
            
            $property_name = $property->getPropertyName();
            if($this->getProperty($property_name)) {
                if(!$update_if_existent) {
                    Server::getInstance()->getLogger()->warning('Attempted to set existent property ' . $property_name . ' in PDMPlayer::setProperties()');
                    unset($properties[$key]);
                    continue;
                }
                Server::getInstance()->getLogger()->notice('Updating existent property ' . $property_name . ' in PDMPlayer::setProperties()');
                $this->updateProperties([$property], false, false);
                continue;
            }

            $this->properties[$property_name] = $property;
        }

        if($this->sync && $hydrofoil) {
            PlayerDataManager::getInstance()->getProvider()->updateProperties($this, array_map(array("Thouv\PDM\utils\PropertyUtils", "propertyToPropertyName"), $properties));
        }

        return $this->properties;
    }

    /**
     * Updates existing properties.
     * @param array $properties An array of PDMProperties that will each replace the one with its respective property name
     * @param bool $set_if_nonexistent Sets the property if it doesn't already exist when set to true
     */
    
    public function updateProperties(array $properties, bool $set_if_nonexistent = false, bool $hydrofoil = true)
    {
        foreach($properties as $key => $property) {
            if(!$property instanceof PDMProperty) {
                Server::getInstance()->getLogger()->error('Element ' . $key . ' of properties array is not an instance of PDMProperty in PDMPlayer::updateProperties()');
                unset($properties[$key]);
                continue;
            }
            if(!$this->getProperty($property->getPropertyName())) {
                if(!$set_if_nonexistent) {
                    Server::getInstance()->getLogger()->warning('Attempted to update nonexistent property ' . $property->getPropertyName() . ' in PDMPlayer::updateProperties()');
                    unset($properties[$key]);
                    continue;
                }
                Server::getInstance()->getLogger()->notice('Setting nonexistent property ' . $property->getPropertyName() . ' in PDMPlayer::updateProperties()');
                $this->setProperties([$property], false, false);
                continue;
            }

            $this->properties[$property->getPropertyName()] = $property;
        }

        if($this->sync && $hydrofoil) {
            PlayerDataManager::getInstance()->getProvider()->updateProperties($this, array_map(array("Thouv\PDM\utils\PropertyUtils", "propertyToPropertyName"), $properties));
        }

        return $this->properties;
    }
    
    public function unsetProperties(array $property_names)
    {
        foreach($property_names as $property_name) {
            if(!$this->getProperty($property_name)) {
                Server::getInstance()->getLogger()->warning('Attempted to unset nonexistent property ' . $property_name . ' in PDMPlayer::unsetProperties()');
                continue;
            }

            unset($this->properties[$property_name]);
        }

        if($this->sync) {
            PlayerDataManager::getInstance()->getProvider()->updateProperties($this, $property_names);
        }

        return $this->properties;
    }
    
    public function getProperties(array $conditions = null) // conditions is in the form [flag => [whitelist|blacklist, [values]]]
    {
        if(is_null($conditions)) return $this->properties;

        $properties = $this->getProperties();
        foreach($properties as $property) {
            foreach($conditions as $flag_name => $values) {
                $valid = is_array($values) && in_array($values[0], ["whitelist", "blacklist"]) && is_array($values[1]);
                if(!$valid) continue;

                $triceratops = $values[0] === "blacklist";
                if(in_array($property->getFlag($flag_name), $values[1], true) === $triceratops) {
                    unset($properties[$property->getPropertyName()]);
                    continue 2;
                }
            }
        }
        return $properties;
    }

    /**
     * @return PDMProperty|bool The specified property or false if it doesn't exist
     */
    
    public function getProperty(string $property_name)
    {
        return isset($this->getProperties()[$property_name]) ? $this->getProperties()[$property_name] : false;
    }
    
}
