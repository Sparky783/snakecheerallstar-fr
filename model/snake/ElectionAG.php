<?php
require_once(ABSPATH . "model/system/Database.php");
require_once("SnakeTools.php");

class ElectionAG
{
	// == ATTRIBUTS ==
	private $votesRapportMoral;
	private $votesRapportFinancier;
	private $method;
	private $date_payment;
	private $deadlines;
	private $is_done;
	
	// == METHODES PRIMAIRES ==
	public function __construct($dbData = null)
	{
		if($dbData != null)
		{
			$this->id = intval($dbData['id_payment']);
			$this->tot_price = intval($dbData['tot_price']);
			$this->method = $dbData['method'];
			$this->date_payment = new DateTime($dbData['date_payment']);
			$this->deadlines = unserialize($dbData['deadlines']);
			$this->is_done = boolval($dbData['is_done']);
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