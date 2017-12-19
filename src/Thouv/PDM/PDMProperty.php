<?php

class PDMProperty
{
	
	public $property_name;
	public $value;
	
	public function __construct(string $property_name, $value)
	{
		$this->property_name = $property_name;
		$this->value = $value;
	}
	
	public function getPropertyName()
	{
		return $this->property_name;
	}
	
	public function getValue()
	{
		return $this->value;
	}
	
	public function toArray()
	{
		return [$this->getPropertyName() => $this->getValue()];
	}
	
}