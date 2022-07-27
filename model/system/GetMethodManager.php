<?php
/*
 * Gestion de la variable $_GET[].
 */


class GetMethodManager
{
	private $values;

	
	public function __construct()
	{
		$this->Refresh();
	}
	
	
	// Get a value with its key. Return False if the value doesn't exist.
	public function GetValue($key)
	{
		if(isset($this->values[$key]))
			return $this->values[$key];
		
		return false;
	}
	
	// Add or modify an existing value.
	public function ModifyValue($key, $value)
	{
		$this->values[$key] = $value;
	}

	// Remove the value from the list.
	public function RemoveValue($key)
	{
		if(isset($this->values[$key])) {
			unset($this->values[$key]);
			return true;
		}
		
		return false;
	}
	
	// Remove all values
	public function Clear()
	{
		$this->values = array();
	}
	
	// Make the $_GET string.
	public function GetString()
	{
		$string = "?";
		foreach ($this->values as $key => $value)
			$string .= $key . "=" . $value . "&amp;";

		return substr($string, 0, strlen($string) - 5);
	}

	// Display the $_GET string.
	public function DisplayString()
	{
		echo $this->GetString();
	}

	private function Refresh()
	{
		$this->values = array();

		if(isset($_GET)) {
			foreach ($_GET as $key => $value)
				$this->ModifyValue($key, strip_tags($value));
		}
	}
}