<?php

namespace Thouv\PDM;

class PDMProperty
{
	
	protected $property_name;
	protected $value;
	private $flags;
	
	public function __construct(string $property_name, $value, array $flags = null)
	{
		$this->property_name = $property_name;
		$this->value = $value;
		$this->flags = $flags;
	}
	
	public function getPropertyName()
	{
		return $this->property_name;
	}
	
	public function getValue()
	{
		return $this->value;
	}

	public function setFlag(string $flag_name, $flag_value)
	{
		$this->flags[$flag_name] = $flag_value;
	}

	/**
	 * @return string|null The specified flag's value or null if it doesn't exist
	 */

	public function getFlag(string $flag_name)
	{
		return isset($this->flags[$flag_name]) ? $this->flags[$flag_name] : null;
	}

	/**
	 * Whether or not this property has an explicit no-sync. Don't fight me on the grounds that this is a bad name, because you're wrong.
	 * @return bool Whether or not this property should be synced.
	 */

	public function toSyncOrNotToSync()
	{
		return $this->getFlag("no_sync") === false;
	}
	
	public function toArray()
	{
		return [$this->getPropertyName() => [$this->getValue(), "flags" => json_encode($this->flags)]];
	}
	
}