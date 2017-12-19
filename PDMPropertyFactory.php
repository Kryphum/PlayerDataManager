<?php

class PDMPropertyFactory
{
	
	public static function makeProperty(string $property_name, string $value)
	{
		return new PDMProperty($property_name, $value);
	}
	
	public static function makeProperties(array $properties_ar)
	{
		$properties = [];
		foreach($properties_ar as $property_name => $value) {
			$properties[] = new PDMProperty($property_name, $value);
		}
		return $properties;
	}
	
}