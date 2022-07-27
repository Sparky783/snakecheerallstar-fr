<?php
require_once(ABSPATH . "model/system/Database.php");

class Reduction
{
	// ==============================================================================
	// ==== Enumérations ============================================================
	// ==============================================================================
	// Type de réduction
	static public $TYPE = array(
		"None" => 0,
		"Percentage" => 1,
		"Amount" => 2,
	);
	// ==============================================================================



	// ==============================================================================
	// ==== Classe ==================================================================
	// ==============================================================================
	// == ATTRIBUTS ==
	private $id = null;
	private $id_payment = null;
	private $type = 0;
	private $value = 0;
	private $sujet = "";
	
	// == METHODES PRIMAIRES ==
	public function __construct($dbData = null)
	{
		if($dbData != null)
		{
			$this->id = intval($dbData['id_reduction']);
			$this->id_payment = intval($dbData['id_payment']);
			$this->type = intval($dbData['type']);
			$this->value = intval($dbData['value']);
			$this->sujet = $dbData['sujet'];
		}
	}
	
	// == METHODES GETTERS ==
	public function GetId()
	{
		return $this->id;
	}

	public function GetIdPayment()
	{
		return $this->id_payment;
	}

	public function GetType()
	{
		return $this->type;
	}

	public function GetValue()
	{
		return $this->value;
	}

	public function GetSujet()
	{
		return $this->sujet;
	}
	
	// == METHODES SETTERS ==
	public function SetId($id)
	{
		$this->id = intval($id);
	}

	public function SetIdPayment($id_payment)
	{
		$this->id_payment = intval($id_payment);
	}
	
	public function SetType($type)
	{
		$this->type = $type;
	}

	public function SetValue($value)
	{
		$this->value = $value;
	}

	public function SetSujet($sujet)
	{
		$this->sujet = $sujet;
	}

	// == AUTRES METHODES ==
	public function SaveToDatabase()
	{
		$database = new Database();

		if($this->id_payment != null)
		{
			if($this->id == null) // Insert
			{
				$id = $database->Insert(
					"reductions",
					array(
						"id_payment" => $this->id_payment,
						"type" => $this->type,
						"value" => $this->value,
						"sujet" => $this->sujet
					)
				);

				if($id !== false)
				{
					$this->id = intval($id);
					return true;
				}
			}
			else // Update
			{
				return $database->Update(
					"reductions", "id_reduction", $this->id,
					array(
						"id_payment" => $this->id_payment,
						"type" => $this->type,
						"value" => $this->value,
						"sujet" => $this->sujet
					)
				);
			}
		}
	}



	// ==============================================================================
	// ==== Fonctions statiques =====================================================
	// ==============================================================================
	static public function GetById($id_reduction)
	{
		$database = new Database();

		$rech = $database->Query(
			"SELECT * FROM reductions WHERE id_reduction=:id_reduction",
			array("id_reduction" => intval($id_reduction))
		);

		if($rech != null)
		{
			$data = $rech->fetch();

			return new Reduction($data);
		}
		
		return false;
	}

	static public function GetListByIdPayment($id_payment)
	{
		$database = new Database();
		$reductions = $database->Query(
			"SELECT * FROM reductions WHERE id_payment=:id_payment",
			array("id_payment" => $id_payment)
		);

		if($reductions != null)
		{
			$list = array();

			while($reduction = $reductions->fetch())
				$list[] = new Reduction($reduction);

			return $list;
		}
		
		return false;
	}
}