<?php
require_once(ABSPATH . "model/system/Database.php");
require_once("SnakeTools.php");
require_once("Adherent.php");

class Tuteur
{
	// == ATTRIBUTS ==
	private $id = null;
	private $firstname = "";
	private $lastname = "";
	private $status = "";
	private $email = "";
	private $phone = "";

	private $adherents = null;
	
	// == METHODES PRIMAIRES ==
	public function __construct($dbData = null)
	{
		if($dbData != null)
		{
			$this->id = intval($dbData['id_tuteur']);
			$this->firstname = $dbData['firstname'];
			$this->lastname = $dbData['lastname'];
			$this->status = $dbData['status'];
			$this->email = $dbData['email'];
			$this->phone = $dbData['phone'];
		}
	}
	
	// == METHODES GETTERS ==
	public function GetId()
	{
		return $this->id;
	}

	public function GetFirstname()
	{
		return $this->firstname;
	}

	public function GetLastname()
	{
		return $this->lastname;
	}

	public function GetStatus()
	{
		return $this->status;
	}

	public function GetEmail()
	{
		return $this->email;
	}

	public function GetPhone()
	{
		return $this->phone;
	}

	// Retourne la liste des adhérents liés au paiement. Si besoin, charge les données depuis la BDD.
	public function GetAdherents()
	{
		if($this->id != null && $this->adherents == null)
		{
			$this->adherents = array();

			$database = new Database();
			$adherents = $database->Query(
				"SELECT * FROM adherent_tuteur JOIN adherents ON adherent_tuteur.id_adherent = adherents.id_adherent WHERE id_tuteur=:id_tuteur",
				array("id_tuteur" => $this->id)
			);

			if($adherents != null)
			{
				while($adherent = $adherents->fetch())
					$this->adherents[] = new Adherent($adherent);
			}
		}

		return $this->adherents;
	}
	
	// == METHODES SETTERS ==
	public function SetId($id)
	{
		$this->id = intval($id);

		if($this->firstname == "" && $this->lastname = "")
			$this->LoadFromDatabase();

		return true;
	}

	public function SetFirstname($firstname)
	{
		if($firstname != "")
		{
			$this->firstname = trim(ucwords(mb_strtolower($firstname)));
			return true;
		}
		return false;
	}
	
	public function SetLastname($lastname)
	{
		if($lastname != "")
		{
			$this->lastname = trim(ucwords(mb_strtolower($lastname)));
			return true;
		}
		return false;
	}

	public function SetStatus($status)
	{
		if($status != "")
		{
			$this->status = mb_strtolower($status);
			return true;
		}
		return false;
	}
	
	public function SetEmail($email)
	{
		$email = trim($email);
		if(preg_match("/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/i", $email))
		{
			$this->email = mb_strtolower($email);
			return true;
		}
		return false;
	}
	
	public function SetPhone($phone)
	{
		$phone = trim($phone);
		if(preg_match('/^(0|\+33)[1-9]([-. ]?[0-9]{2}){4}$/i', $phone))
		{
			$phone = str_replace("+33", "0", $phone);
			$phone = str_replace(array(".", "-", " "), "", $phone);
			$this->phone = $phone;
			return true;
		}
		return false;
	}
	
	public function SetInformation($infos)
	{
		if(is_array($infos))
		{
			$result = true;
			if(isset($infos['id_tuteur']))
				$result = $result && $this->SetId($infos['id_tuteur']);
			
			if(isset($infos['firstname']))
				$result = $result && $this->SetFirstname($infos['firstname']);
			
			if(isset($infos['lastname']))
				$result = $result && $this->SetLastname($infos['lastname']);
			
			if(isset($infos['status']))
				$result = $result && $this->SetStatus($infos['status']);
			
			if(isset($infos['email']))
				$result = $result && $this->SetEmail($infos['email']);
			
			if(isset($infos['phone']))
				$result = $result && $this->SetPhone($infos['phone']);
			
			return $result;
		}
		
		return false;
	}

	public function AddAdherent(Adherent $adherent)
	{
		if(!is_array($this->adherents))
			$this->adherents = array();

		$this->adherents[] = $adherent;
	}

	public function SaveToDatabase()
	{
		$database = new Database();
		$result = false;

		if($this->id == null) // Insert
		{
			$id = $database->Insert(
				"tuteurs",
				array(
					"firstname" => $this->firstname,
					"lastname" => $this->lastname,
					"status" => $this->status,
					"email" => $this->email,
					"phone" => $this->phone
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
				"tuteurs", "id_tuteur", $this->id,
				array(
					"firstname" => $this->firstname,
					"lastname" => $this->lastname,
					"status" => $this->status,
					"email" => $this->email,
					"phone" => $this->phone
				)
			);
		}

		// Met à jour les liens avec les adhérents.
		if($this->id != null && $this->adherents != null)
		{
			if(count($this->adherents) > 0)
			{
				foreach($this->adherents as $adherent)
				{
					if($adherent->GetId() != null)
					{
						$database->Insert(
							"adherent_tuteur",
							array(
								"id_adherent" => $adherent->GetId(),
								"id_tuteur" => $this->id
							)
						);
					}
					else
					{
						$result = false;
						break;
					}
				}
			}
		}

		return $result;
	}

	public function RemoveFromDatabase()
	{
		if($this->id != null)
		{
			$database = new Database();
			return $database->Delete("tuteurs", "id_tuteur", $this->id);
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
				"SELECT * FROM tuteurs WHERE id_tuteur=:id_tuteur",
				array("id_tuteur" => $this->id)
			);
	
			if($rech != null)
			{
				$dbData = $rech->fetch();

				$this->firstname = $dbData['firstname'];
				$this->lastname = $dbData['lastname'];
				$this->status = $dbData['status'];
				$this->email = $dbData['email'];
				$this->phone = $dbData['phone'];

				return true;
			}
		}
		
		return false;
	}



	// ==============================================================================
	// ==== Fonctions statiques =====================================================
	// ==============================================================================
	static public function GetById($id_tuteur)
	{
		$database = new Database();

		$rech = $database->Query(
			"SELECT * FROM tuteurs WHERE id_tuteur=:id_tuteur",
			array("id_tuteur" => intval($id_tuteur))
		);

		if($rech != null)
		{
			$data = $rech->fetch();

			return new Tuteur($data);
		}
		
		return false;
	}

	// Retourne la liste de tous les tuteurs du club.
	// Par défaut ceux de la saison en cours.
	static public function GetList($saison = null)
	{
		if($saison == null)
			$saison = SnakeTools::GetCurrentSaison();

		$database = new Database();

		$tuteurs = $database->Query(
			"SELECT tuteurs.* FROM adherent_tuteur
			JOIN adherents ON adherent_tuteur.id_adherent = adherents.id_adherent
			JOIN tuteurs ON adherent_tuteur.id_tuteur = tuteurs.id_tuteur
			JOIN sections ON sections.id_section = adherents.id_section
			WHERE sections.saison=:saison",
			array(
				"saison" => $saison
			)
		);
		
		if($tuteurs != null)
		{
			$list = array();

			while($tuteur = $tuteurs->fetch())
				$list[] = new Tuteur($tuteur);
			
			return $list;
		}
		
		return false;
	}

	// Retourne la liste de tous les tuteurs de la section souhaité.
	// Par défaut ceux de la saison en cours.
	static public function GetListBySection($id_section)
	{
		$database = new Database();

		$tuteurs = $database->Query(
			"SELECT tuteurs.* FROM adherent_tuteur
			JOIN adherents ON adherent_tuteur.id_adherent = adherents.id_adherent
			JOIN tuteurs ON adherent_tuteur.id_tuteur = tuteurs.id_tuteur
			WHERE id_section=:id_section",
			array(
				"id_section" => intval($id_section)
			)
		);

		if($tuteurs != null)
		{
			$list = array();

			while($tuteur = $tuteurs->fetch())
				$list[] = new Tuteur($tuteur);

			return $list;
		}
		
		return false;
	}
}