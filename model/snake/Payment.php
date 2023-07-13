<?php
namespace Snake;

use DateTime;
use System\Database;
use Snake\Adherent;
use Snake\Reduction;

class Payment
{
	// ==============================================================================
	// ==== Enumérations ============================================================
	// ==============================================================================
	// Méthode de paiement
	static public $METHODS = array(
		"Espece" => 1,
		"Cheque" => 2,
		"Internet" => 3,
		"Virement" => 4,
	);



	// ==============================================================================
	// ==== Classe ==================================================================
	// ==============================================================================
	// == ATTRIBUTS ==
	private $id = null;
	private $base_price = null;
	private $fixed_price = null;
	private $method = 0;
	private $date_payment = null;
	private $nb_deadlines = 0;
	private $is_done = false;
	
	private $reductions = array();
	private $adherents = array();
	
	// == METHODES PRIMAIRES ==
	public function __construct($dbData = null)
	{
		$this->date_payment = date("Y-m-d");

		if($dbData != null)
		{
			$this->id = intval($dbData['id_payment']);
			$this->base_price = intval($dbData['base_price']);
			$this->fixed_price = intval($dbData['fixed_price']);
			$this->method = $dbData['method'];
			$this->date_payment = new DateTime($dbData['date_payment']);
			$this->nb_deadlines = intval($dbData['nb_deadlines']);
			$this->is_done = boolval($dbData['is_done']);
		}
	}
	
	// == METHODES GETTERS ==
	public function getId()
	{
		return $this->id;
	}

	public function getBasePrice()
	{
		if($this->base_price == null)
			$this->loadFromDatabase();
		
		return $this->base_price;
	}

	public function getFixedPrice()
	{
		if($this->fixed_price == null)
			$this->loadFromDatabase();
		
		return $this->fixed_price;
	}

	public function getMethod()
	{
		if($this->method == 0)
			$this->loadFromDatabase();

		return $this->method;
	}

	public function getDatePayment()
	{
		if($this->date_payment == null)
			$this->LoadFromDatabase();

		return $this->date_payment;
	}

	public function getNbDeadlines()
	{
		if($this->nb_deadlines == 0 && $this->method == 2) // 2 = Paiement par chèques
			$this->loadFromDatabase();

		return $this->nb_deadlines;
	}

	public function getDeadlines()
	{
		return SnakeTools::makeDeadlines($this->getFinalAmount(), $this->getNbDeadlines());
	}

	public function isDone()
	{
		return $this->is_done;
	}

	// Retourne la liste des réductions associés au paiement. Si besoin, charge les données depuis la BDD.
	public function getReductions()
	{
		if($this->id != null && count($this->reductions) == 0)
		{
			$list = Reduction::getListByIdPayment($this->id);
			
			if($list !== false)
				$this->reductions = $list;
		}

		return $this->reductions;
	}

	// Retourne la liste des adhérents liés au paiement. Si besoin, charge les données depuis la BDD.
	public function getAdherents()
	{
		if($this->id != null && count($this->adherents) == 0)
		{
			$list = Adherent::getListByIdPayment($this->id);
			
			if($list !== false)
				$this->adherents = $list;
		}

		return $this->adherents;
	}

	public function getBasePriceWithReductions()
	{
		$this->getReductions(); // Charge les réductions si elle n'ont pas été chargées.

		$montant = $this->base_price;
		
		// Toujours appliquer les "Pourcentage" avant ...
		foreach($this->reductions as $reduction)
		{
			if($reduction->getType() == Reduction::$TYPE['Percentage'])
				$montant = round($montant * (1 - ($reduction->getValue() / 100)));
		}
		
		// ... puis appliquer les "Montant".
		foreach($this->reductions as $reduction)
		{
			if($reduction->getType() == Reduction::$TYPE['Amount'])
				$montant -= $reduction->GetValue();
		}
		
		return $montant;
	}
	
	public function getFinalAmount()
	{
		return $this->GetBasePriceWithReductions() + $this->fixed_price;
	}

	public function setId($id)
	{
		$this->id = intval($id);
	}
	
	public function setBasePrice($base_price)
	{
		$this->base_price = intval($base_price);
	}

	public function SetFixedPrice($fixed_price)
	{
		$this->fixed_price = intval($fixed_price);
	}

	public function SetMethod($method)
	{
		$this->method = $method;
	}

	public function SetNbDeadlines($nb_deadlines)
	{
		$this->nb_deadlines = intval($nb_deadlines);
	}

	public function SetIsDone($is_done)
	{
		$this->is_done = boolval($is_done);
	}

	// == AUTRES METHODES ==
	public function AddReduction(Reduction $reduction)
	{
		if($this->id != null)
			$reduction->SetIdPayment($this->id);

		$this->reductions[] = $reduction;
	}

	public function SaveToDatabase()
	{
		$database = new Database();
		$result = false;

		if($this->id == null) // Insert
		{
			$id = $database->Insert(
				"payments",
				array(
					"base_price" => $this->base_price,
					"fixed_price" => $this->fixed_price,
					"method" => $this->method,
					"date_payment" => $this->date_payment,
					"nb_deadlines" => $this->nb_deadlines,
					"is_done" => $this->is_done
				)
			);

			if($id !== false)
			{
				$this->id = intval($id);
				$result = true;
			}
		}
		else // Update
		{
			$result = $database->Update(
				"payments", "id_payment", $this->id,
				array(
					"base_price" => $this->base_price,
					"fixed_price" => $this->fixed_price,
					"method" => $this->method,
					"date_payment" => $this->date_payment,
					"nb_deadlines" => $this->nb_deadlines,
					"is_done" => $this->is_done
				)
			);
		}

		// Sauvegarde les réductions.
		if($id !== false)
		{
			foreach($this->reductions as $reduction)
			{
				$reduction->SetIdPayment($this->id);
				$result = $result & $reduction->SaveToDatabase();
			}
		}

		return $result;
	}

	public function RemoveFromDatabase()
	{
		if($this->id != null)
		{
			$database = new Database();
			return $database->Delete("payments", "id_payment", $this->id);

			// Les réductions sont supprimées grâce à la contrainte par clè étrangère de la BDD.
		}

		return false;
	}

	// Remonte les infos depuis la BDD.
	private function LoadFromDatabase()
	{
		if($this->id != null)
		{
			$database = new Database();
			$rech = $database->Query(
				"SELECT * FROM payments WHERE id_payment=:id_payment",
				array("id_payment" => $this->id)
			);
	
			if($rech != null)
			{
				$dbData = $rech->fetch();

				$this->base_price = intval($dbData['base_price']);
				$this->fixed_price = intval($dbData['fixed_price']);
				$this->method = $dbData['method'];
				$this->date_payment = new DateTime($dbData['date_payment']);
				$this->nb_deadlines = intval($dbData['nb_deadlines']);
				$this->is_done = boolval($dbData['is_done']);

				return true;
			}
		}
		
		return false;
	}



	// ==============================================================================
	// ==== Fonctions statiques =====================================================
	// ==============================================================================
	static public function GetById($id_payment)
	{
		$database = new Database();

		$rech = $database->Query(
			"SELECT * FROM payments WHERE id_payment=:id_payment",
			array("id_payment" => intval($id_payment))
		);

		if($rech != null)
		{
			$data = $rech->fetch();

			return new Payment($data);
		}
		
		return false;
	}

	// Retourne la liste de tous les paiement fait pendant la saison.
	// Par défaut ceux de la saison en cours.
	static public function GetList($saison = null)
	{
		if($saison == null)
			$saison = SnakeTools::GetCurrentSaison();

		$database = new Database();

		$payments = $database->Query(
			"SELECT payments.* FROM payments
			JOIN adherents ON payments.id_payment = adherents.id_payment
			JOIN sections ON sections.id_section = adherents.id_section
			WHERE saison=:saison",
			array("saison" => $saison)
		);

		if($payments != null)
		{
			$list = array();

			while($data = $payments->fetch())
				$list[] = new Payment($data);

			return $list;
		}
		
		return false;
	}
}