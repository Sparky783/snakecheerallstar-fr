<?php
namespace Snake;

use DateTime;
use System\Database;
use Snake\SnakeTools;

// TODO

/**
 * Outil de gestion des élection de l'assemblé générale.
 */
class ElectionAG
{
	// == ATTRIBUTS ==
	/**
	 * @var 
	 */
	private $_votesRapportMoral;
	private $_votesRapportFinancier;
	private $_method;
	private $_datePayment;
	private $_deadlines;
	private $_isDone;
	
	// == METHODES PRIMAIRES ==
	public function __construct($dbData = null)
	{
		if($dbData != null)
		{
			$this->_id = intval($dbData['id_payment']);
			$this->_tot_price = intval($dbData['tot_price']);
			$this->_method = $dbData['method'];
			$this->_date_payment = new DateTime($dbData['date_payment']);
			$this->_deadlines = unserialize($dbData['deadlines']);
			$this->_is_done = boolval($dbData['is_done']);
		}
	}
	
	// == METHODES GETTERS ==
	public function GetId()
	{
		return $this->id;
	}

	public function GetTotalPrice()
	{
		return $this->tot_price;
	}

	public function GetMethod()
	{
		return $this->method;
	}

	public function GetDatePayment()
	{
		return $this->date_payment;
	}

	public function GetDeadlines()
	{
		return $this->deadlines;
	}

	public function IsDone()
	{
		return $this->is_done;
	}
	
	// == METHODES SETTERS ==
	private function SetId($id)
	{
		$this->id = intval($id);

		return true;
	}
}