<?php
namespace System;

/**
 * Gestion de la variable $_GET[].
 */
class GetMethodManager
{
	private array $_values;

	
	/**
	 * Construct an object with current data present into GET method.
	 */
	public function __construct()
	{
		$this->_values = array();

		if(isset($_GET)) {
			foreach ($_GET as $key => $value)
				$this->setValue($key, strip_tags($value));
		}
	}
	
	
	/**
	 * Get a value with its key. Return False if the value doesn't exist.
	 * 
	 * @param string $name Name of the value that you want.
     * @return bool
	 */
	public function getValue(string $name): string|bool
	{
		if(isset($this->_values[$name]))
			return $this->_values[$name];
		
		return false;
	}
	
	/**
	 * Add or modify a value.
	 * 
	 * @param string $name Name of the value to add or modify.
	 * @param mixed $value Value associated to this name.
	 */
	public function setValue(string $name, mixed $value): void 
	{
		$this->_values[$name] = $value;
	}

	/**
	 * Remove the value from the list.
	 * 
	 * @param string $name Name of the value to remove.
	 * @return bool Return True if the value has been removed, else False.
	 */
	public function removeValue(string $name): bool
	{
		if(isset($this->_values[$name])) {
			unset($this->_values[$name]);
			return true;
		}
		
		return false;
	}
	
	/**
	 * Remove all values from this object.
	 */
	public function clear(): void
	{
		$this->_values = array();
	}
	
	/**
	 * Make the $_GET string.
	 * 
	 * @return string String to put into URL.
	 */
	public function getString(): string
	{
		$string = "?";
		foreach ($this->_values as $key => $value)
			$string .= $key . "=" . $value . "&amp;";

		return substr($string, 0, strlen($string) - 5);
	}

	/**
	 * Display the $_GET string.
	 */
	public function displayString(): void
	{
		echo $this->getString();
	}
}