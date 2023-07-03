<?php
namespace Snake;

use System\Database;
use Snake\Horaire;

class Section
{
	// == ATTRIBUTS ==
	private $id = null;
	private $name = "";
	private $saison = "";
	private $min_age = 0;
	private $priceCotisation = 0;
	private $priceRentUniform = 0;
	private $priceCleanUniform = 0;
	private $priceUniform = 0;
	private $nbMaxMembers = 0;
	private $horaires = array();
	
	private $nbMembers; // Nombre d'adhérents inscrit
	
	// == METHODES PRIMAIRES ==
	public function __construct(array $dbData = null)
	{
		if($dbData != null)
		{
			$this->id = intval($dbData['id_section']);
			$this->name = $dbData['name'];
			$this->saison = $dbData['saison'];
			$this->min_age = intval($dbData['min_age']);
			$this->priceCotisation = intval($dbData['price_cotisation']);
			$this->priceRentUniform = intval($dbData['price_rent_uniform']);
			$this->priceCleanUniform = intval($dbData['price_clean_uniform']);
			$this->priceUniform = intval($dbData['price_buy_uniform']);
			$this->nbMaxMembers = intval($dbData['nb_max_members']);
			$this->horaires = unserialize($dbData['horaires']);
		}
	}
	
	// == METHODES GETTERS ==
	public function GetId() : int
	{
		return $this->id;
	}

	public function GetName() : string
	{
		return $this->name;
	}

	public function GetSaison() : string
	{
		return $this->saison;
	}

	public function GetMinAge() : int
	{
		return $this->min_age;
	}

	public function GetPriceCotisation() : int
	{
		return $this->priceCotisation;
	}

	public function GetPriceRentUniform() : int
	{
		return $this->priceRentUniform;
	}

	public function GetPriceCleanUniform() : int
	{
		return $this->priceCleanUniform;
	}

	public function GetPriceUniform() : int
	{
		return $this->priceUniform;
	}

	public function GetNbMaxMembers() : int
	{
		return $this->nbMaxMembers;
	}

	public function GetHoraires() : array
	{
		return $this->horaires;
	}
	
	public function GetNbMembers() : int
	{
		return $this->nbMembers;
	}
	
	// == METHODES SETTERS ==
	public function SetName($name)
	{
		$this->name = $name;
	}

	public function SetSaison($saison)
	{
		$this->saison = $saison;
	}

	public function SetMinAge($min_age)
	{
		$this->min_age = intval($min_age);
	}

	public function SetPriceCotisation($price)
	{
		$this->priceCotisation = intval($price);
	}

	public function SetPriceUniform($price)
	{
		$this->priceUniform = intval($price);
	}

	public function SetNbMaxMembers($nbMaxMembers)
	{
		$this->nbMaxMembers = intval($nbMaxMembers);
	}
	
	// == AUTRES METHODES ==
	public function AddHoraire(Horaire $horaire)
	{
		$this->horaires[] = $horaire;
	}
	
	public function AddMember()
	{
		$this->nbMembers ++;
	}

	public function SaveToDatabase()
	{
		$database = new Database();

		if($this->id == null) // Insert
		{
			$id = $database->Insert(
				"sections",
				array(
					"name" => $this->name,
					"saison" => $this->saison,
					"min_age" => $this->min_age,
					"price_cotisation" => $this->priceCotisation,
					"price_rent_uniform" => $this->priceRentUniform,
					"price_clean_uniform" => $this->priceCleanUniform,
					"price_buy_uniform" => $this->priceUniform,
					"nb_max_members" => $this->nbMaxMembers,
					"horaires" => serialize($this->horaires)
				)
			);

			if($id !== false)
			{
				$this->id = intval($id);
				return true;
			}
			
			return false;
		}
		else // Update
		{
			$result = $database->Update(
				"sections", "id_section", $this->id,
				array(
					"name" => $this->name,
					"saison" => $this->saison,
					"min_age" => $this->min_age,
					"price_cotisation" => $this->priceCotisation,
					"price_rent_uniform" => $this->priceRentUniform,
					"price_clean_uniform" => $this->priceCleanUniform,
					"price_buy_uniform" => $this->priceUniform,
					"nb_max_members" => $this->nbMaxMembers,
					"horaires" => serialize($this->horaires)
				)
			);

			return $result;
		}
	}

	// ==============================================================================
	// ==== Fonctions statiques =====================================================
	// ==============================================================================
	static public function GetById($id_section)
	{
		$database = new Database();

		$rech = $database->Query(
			"SELECT * FROM sections WHERE id_section=:id_section",
			array("id_section" => intval($id_section))
		);

		if($rech != null)
		{
			$data = $rech->fetch();

			return new Section($data);
		}
		
		return false;
	}

	// Retourne la liste de toutes les sections du club pour une saison donnée.
	static public function GetList($saison = null)
	{
		if($saison == null)
			$saison = SnakeTools::GetCurrentSaison();

		$database = new Database();

		$sections = $database->Query(
			"SELECT * FROM sections WHERE saison=:saison ORDER BY min_age",
			array("saison" => $saison)
		);

		if($sections != null)
		{
			$list = array();

			while($data = $sections->fetch())
				$list[] = new Section($data);

			return $list;
		}
		
		return false;
	}

	static public function RemoveFromDatabase($id_section)
	{
		$database = new Database();

		return $database->Delete("sections", "id_section", intval($id_section));
	}
}