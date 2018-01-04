<?php

namespace Thouv\PDM;

use pocketmine\Server;

class PDMPropertyFactory
{

	/**
	 * Creates an instance of PDMProperty based on the supplied arguments.
	 * @return PDMProperty
	 */
	
	public static function makeProperty(string $property_name, $value, array $flags = null)
	{
		if(is_null($flags)) $flags = [];
		return new PDMProperty($property_name, $value, $flags);
	}

	/**
	 * Creates an array of instances of PDMProperty based on the supplied array.
	 * This does not support setting flags, use PDMPropertyFactory::makeProperty() for that.
	 * @param array $properties_ar An array structured as such: [property_name => property_value, property_name => property_value, ...]
	 * @return array An array of PDMProperty
	 */
	
	public static function makeProperties(array $properties_ar)
	{
		$properties = [];
		foreach($properties_ar as $property_name => $value) {
			if(!is_string($property_name)) {
				Server::getInstance()->getLogger()->error('$property_name is not a string in PDMPropertyFactory::makeProperties()\n' . var_dump($property_name));
				continue;
			}
			$properties[] = new PDMProperty($property_name, $value, []);
		}
		return $properties;
	}
	
}