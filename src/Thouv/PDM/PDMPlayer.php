<?php

class PDMPlayer
{
	
	public $properties = [];
	
	public function setProperties(array $properties)
	{
		foreach($properties as $property) {
			if(!$property instanceof PDMProperty) continue;
			$this->properties[$property->getPropertyName()] = $property;
		}
		return $this->properties;
	}
	
	public function unsetProperties(array $property_names)
	{
		foreach($property_names as $property_name) {
			unset($this->properties[$property_name]);
		}
	}
	
	public function getProperties()
	{
		return $this->properties;
	}
	
	public function getProperty(string $property_name)
	{
		return $this->getProperties()[$property_name];
	}
	
	public function updateProperty(string $property_name, PDMProperty $property)
	{
		if($property_name !== $property->getPropertyName()) return false;
		
		$this->properties[$property_name] = $property;
		return $this->getProperty($property_name);
	}
	
}