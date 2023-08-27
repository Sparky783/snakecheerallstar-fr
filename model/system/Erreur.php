<?php
namespace System;

/**
 * This class is used to contain an error.
 */
class Erreur
{
	// ==== ATTRIBUTS ====
	private string $error;

	// ==== CONSTRUCTOR ====
	/**
	 * Create a new error instance.
	 */
	public function __construct($error = null)
	{
		if($error != null){
			$this->setError($error);
		}
	}
	
	// ==== GETTERS ====
	/**
	 * Get the error.
	 * 
	 * @return string Error message.
	 */
	public function getError(): string
	{
		return $this->error;
	}
	
	// ==== SETTERS ====
	/**
	 * Define the error.
	 * 
	 * @param string $error Error's message.
     * @return void
	 */
	public function setError(string $error): void
	{
		$this->error = htmlspecialchars($error);
	}
}