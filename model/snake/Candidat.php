<?php
require_once(ABSPATH . "model/system/Database.php");
require_once("SnakeTools.php");

class Candidat
{
	// == ATTRIBUTS ==
	private $id;
	private $firstname;
	private $lastname;
	private $nb_votes = 0;
	
	// == METHODES PRIMAIRES ==
	public function __construct($dbData = null)
	{
		if($dbData != null)
		{
			$this->id = intval($dbData['id_candidat']);
			$this->firstname = $dbData['firstname'];
			$this->lastname = $dbData['lastname'];
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
	
	// == METHODES SETTERS ==
	private function SetId($id)
	{
		$this->id = intval($id);

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

	public function AddVotes($nb_votes)
	{
		$this->nb_votes += $nb_votes;
	}

	public function ToArray()
	{
		return array(
			"id_candidat" => $this->id,
			"firstname" => $this->firstname,
			"lastname" => $this->lastname,
			"name" => $this->firstname . " " . $this->lastname,
			'nbVotes' => $this->nb_votes
		);
	}

	public function SaveToDatabase()
	{
		$database = new Database();

		if($this->id == null) // Insert
		{
			$id = $database->Insert(
				"ag_candidats",
				array(
					"firstname" => $this->firstname,
					"lastname" => $this->lastname
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
			return $database->Update(
				"ag_candidats", "id_candidat", $this->id,
				array(
					"firstname" => $this->firstname,
					"lastname" => $this->lastname
				)
			);
		}
	}


	// ==============================================================================
	// ==== Fonctions statiques =====================================================
	// ==============================================================================
	static public function GetById($id_candidat)
	{
		$database = new Database();

		$rech = $database->Query(
			"SELECT * FROM ag_candidats WHERE id_candidat=:id_candidat",
			array("id_candidat" => intval($id_candidat))
		);

		if($rech != null)
		{
			$data = $rech->fetch();

			return new Candidat($data);
		}
		
		return false;
	}

	// Retourne la liste de tous les candidats se présentant à l'assemblé générale du club.
	static public function GetList()
	{
		$database = new Database();

		$candidats = $database->Query("SELECT * FROM ag_candidats");

		if($candidats != null)
		{
			$list = array();

			while($candidat = $candidats->fetch())
				$list[] = new Candidat($candidat);

			return $list;
		}
		
		return false;
	}

	static public function RemoveFromDatabase($id_candidat)
	{
		$database = new Database();
		
		return $database->Delete("ag_candidats", "id_candidat", intval($id_candidat));
	}
}