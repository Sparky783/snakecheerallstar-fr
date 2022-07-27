<?php
require_once(ABSPATH . "model/system/Database.php");
require_once("SnakeTools.php");
require_once("Section.php");
require_once("Payment.php");

class Adherent
{
	// == ATTRIBUTS ==
	private $id = null;
	private $id_section = null;
	private $id_payment = null;
	private $firstname = "";
	private $lastname = "";
	private $birthday = "";
	private $siblings = false;
	private $medicine = false;
	private $medicine_info = "";
	private $has_uniform = false;
	private $chq_buy_uniform = false;
	private $chq_rent_uniform = false;
	private $chq_clean_uniform = false;
	private $doc_ID_card = false;
	private $doc_photo = false;
	private $doc_fffa = false;
	private $doc_sportmut = false;
	private $doc_medic_auth = false;
	private $inscription_date = false;

	private $section = null;
	private $payment = null;
	private $tuteurs = array();
	
	// == METHODES PRIMAIRES ==
	public function __construct($dbData = null)
	{
		if($dbData != null)
		{	
			$this->id = intval($dbData['id_adherent']);
			$this->id_section = intval($dbData['id_section']);
			$this->id_payment = intval($dbData['id_payment']);
			$this->firstname = $dbData['firstname'];
			$this->lastname = $dbData['lastname'];
			$this->birthday = new Datetime($dbData['birthday']);
			$this->siblings = boolval($dbData['siblings']);
			$this->medicine = boolval($dbData['medicine']);
			$this->medicine_info = $dbData['medicine_info'];
			$this->has_uniform = boolval($dbData['has_uniform']);
			$this->chq_buy_uniform = boolval($dbData['chq_buy_uniform']);
			$this->chq_rent_uniform = boolval($dbData['chq_rent_uniform']);
			$this->chq_clean_uniform = boolval($dbData['chq_clean_uniform']);
			$this->doc_ID_card = boolval($dbData['doc_ID_card']);
			$this->doc_photo = boolval($dbData['doc_photo']);
			$this->doc_fffa = boolval($dbData['doc_fffa']);
			$this->doc_sportmut = boolval($dbData['doc_sportmut']);
			$this->doc_medic_auth = boolval($dbData['doc_medic_auth']);
			$this->inscription_date = new DateTime($dbData['inscription_date']);
		}
	}
	
	// == METHODES GETTERS ==
	public function GetId()
	{
		return $this->id;
	}

	public function GetIdSection()
	{
		return $this->id_section;
	}
	
	public function GetIdPayment()
	{
		return $this->id_payment;
	}

	public function GetFirstname()
	{
		return $this->firstname;
	}

	public function GetLastname()
	{
		return $this->lastname;
	}

	public function GetBirthday()
	{
		return $this->birthday;
	}

	public function GetSiblings()
	{
		return $this->siblings;
	}

	public function GetMedicine()
	{
		return $this->medicine;
	}

	public function GetMedicineInfo()
	{
		return $this->medicine_info;
	}

	public function GetTenue()
	{
		return $this->has_uniform;
	}

	public function GetDocIdCard()
	{
		return $this->doc_ID_card;
	}

	public function GetDocPhoto()
	{
		return $this->doc_photo;
	}

	public function GetDocFFFA()
	{
		return $this->doc_fffa;
	}

	public function GetDocSportmut()
	{
		return $this->doc_sportmut;
	}

	public function GetDocMedicAuth()
	{
		return $this->doc_medic_auth;
	}

	public function GetInscriptionDate()
	{
		return $this->inscription_date;
	}

	// Retourne l'objet Section lié à l'adhérent. Si besoin, charge les données de la BDD.
	public function GetSection()
	{
		if($this->id_section != null && $this->section == null)
		{
			$section = Section::GetById($this->id_section);

			if($section != false)
				$this->section = $section;
		}

		return $this->section;
	}

	// Retourne l'objet Payment lié à l'adhérent. Si besoin, charge les données de la BDD.
	public function GetPayment()
	{
		if($this->id_payment != null && $this->payment == null)
		{
			$payment = Payment::GetById($this->id_payment);

			if($payment != false)
				$this->payment = $payment;
		}

		return $this->payment;
	}

	// Retourne la liste des tuteurs lié à l'adhérent. Si besoin, charge les données de la BDD.
	public function GetTuteurs()
	{
		if($this->id != null && count($this->tuteurs) == 0)
		{
			$database = new Database();
			$tuteurs = $database->Query(
				"SELECT * FROM adherent_tuteur JOIN tuteurs ON adherent_tuteur.id_tuteur = tuteurs.id_tuteur WHERE id_adherent=:id_adherent",
				array("id_adherent" => $this->id)
			);

			if($tuteurs != null)
			{
				while($tuteur = $tuteurs->fetch())
					$this->tuteurs[] = new Tuteur($tuteur);
			}
		}

		return $this->tuteurs;
	}
	
	// == METHODES SETTERS ==
	public function SetId($id)
	{
		$this->id = intval($id);

		if($this->firstname == "" && $this->lastname == "")
			$this->LoadFormDatabase();

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
	
	public function SetBirthday($birthday)
	{
		$birthday = trim($birthday);
		
		//if(preg_match('/^([0-2][0-9]|(3)[0-1])(\/|-)(((0)[0-9])|((1)[0-2]))(\/|-)\d{4}$/i', $birthday)) // jj-mm-aaaa
		if(preg_match('/^\d{4}(\/|-)(((0)[0-9])|((1)[0-2]))(\/|-)([0-2][0-9]|(3)[0-1])$/i', $birthday)) // yyyy-mm-dd
		{
			$this->birthday = new DateTime($birthday);
			$section = SnakeTools::FindSection($birthday);

			if($section !== false)
			{
				$this->section = $section;
				$this->id_section = $this->section->GetId();
			}

			return true;
		}

		return false;
	}

	public function SetSiblings($siblings)
	{
		if(is_bool($siblings))
		{
			$this->siblings = $siblings;
			return true;
		}
		
		return false;
	}
	
	public function SetMedicine($medicine)
	{
		if($medicine == "yes" || $medicine == "no")
		{
			if($medicine == "yes")
				$this->medicine = true;
			else
				$this->medicine = false;
			
			return true;
		}
		return false;
	}

	public function SetMedicineInfo($medicine_info)
	{
		if($medicine_info != "")
			$this->medicine_info = $medicine_info;
	}
	
	public function SetTenue($tenue)
	{
		if($tenue == "yes" || $tenue == "no")
		{
			if($tenue == "yes")
				$this->has_uniform = true;
			else
				$this->has_uniform = false;
			
			return true;
		}
		return false;
	}
	
	public function SetSportmut($doc_sportmut)
	{
		if($doc_sportmut == "yes" || $doc_sportmut == "no")
		{
			if($doc_sportmut == "yes")
				$this->doc_sportmut = true;
			else
				$this->doc_sportmut = false;
			
			return true;
		}
		return false;
	}

	public function SetInscriptionDate($inscription_date = null)
	{
		if($inscription_date != null)
			$this->inscription_date = new DateTime();
		else
			$this->inscription_date = new DateTime($inscription_date);
	}

	public function SetSection(Section $section)
	{
		$this->section = $section;
	}

	public function SetPayment(Payment $payment)
	{
		$this->payment = $payment;
		$this->id_payment = $this->payment->GetId();
	}
	
	// == AUTRES METHODES ==
	public function SetInformation($infos)
	{
		if(is_array($infos))
		{
			$result = true;
			if(isset($infos['firstname']))
				$result = $result && $this->SetFirstname($infos['firstname']);
			
			if(isset($infos['lastname']))
				$result = $result && $this->SetLastname($infos['lastname']);
			
			if(isset($infos['birthday']))
				$result = $result && $this->SetBirthday($infos['birthday']);
			
			if(isset($infos['medicine']))
				$result = $result && $this->SetMedicine($infos['medicine']);

			if(isset($infos['infoMedicine']))
				$this->SetMedicineInfo($infos['infoMedicine']);
			
			if(isset($infos['tenue']))
				$result = $result && $this->SetTenue($infos['tenue']);
				
			return $result;
		}
		
		return false;
	}

	public function SaveToDatabase()
	{
		$database = new Database();

		if($this->id == null) // Insert
		{
			$id = $database->Insert(
				"adherents",
				array(
					"id_section" => $this->id_section,
					"id_payment" => $this->GetPayment()->GetId(),
					"firstname" => $this->firstname,
					"lastname" => $this->lastname,
					"birthday" => $this->birthday->format('Y-m-d'),
					"siblings" => $this->siblings,
					"medicine" => $this->medicine,
					"medicine_info" => $this->medicine_info,
					"has_uniform" => $this->has_uniform,
					"chq_buy_uniform" => $this->chq_buy_uniform,
					"chq_rent_uniform" => $this->chq_rent_uniform,
					"chq_clean_uniform" => $this->chq_clean_uniform,
					"doc_ID_card" => $this->doc_ID_card,
					"doc_photo" => $this->doc_photo,
					"doc_fffa" => $this->doc_fffa,
					"doc_sportmut" => $this->doc_sportmut,
					"doc_medic_auth" => $this->doc_medic_auth,
					"inscription_date" => $this->inscription_date->format("Y-m-d H:i:s")
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
				"adherents", "id_adherent", $this->id,
				array(
					"id_section" => $this->id_section,
					"id_payment" => $this->GetPayment()->GetId(),
					"firstname" => $this->firstname,
					"lastname" => $this->lastname,
					"birthday" => $this->birthday->format('Y-m-d'),
					"siblings" => $this->siblings,
					"medicine" => $this->medicine,
					"medicine_info" => $this->medicine_info,
					"has_uniform" => $this->has_uniform,
					"chq_buy_uniform" => $this->chq_buy_uniform,
					"chq_rent_uniform" => $this->chq_rent_uniform,
					"chq_clean_uniform" => $this->chq_clean_uniform,
					"doc_ID_card" => $this->doc_ID_card,
					"doc_photo" => $this->doc_photo,
					"doc_fffa" => $this->doc_fffa,
					"doc_sportmut" => $this->doc_sportmut,
					"doc_medic_auth" => $this->doc_medic_auth,
					"inscription_date" => $this->inscription_date->format("Y-m-d H:i:s")
				)
			);

			return $result;
		}
	}

	public function RemoveFromDatabase()
	{
		if($this->id != null)
		{
			$database = new Database();

			// Recherche l'adhérent dans la table de liaison.
			$tuteurs = $this->GetTuteurs();
			$payment = $this->GetPayment();

			// Suppression de l'adhérent
			$result = true;
			$result = $result & $database->Delete("adherent_tuteur", "id_adherent", $this->id);
			$result = $result & $database->Delete("adherents", "id_adherent", $this->id);

			// Verification du reste de la BDD (Payments et tuteurs)
			if($result)
			{
				// Pour chaque tuteurs, on cherche si ils ont des adhérents. Si non, on supprime.
				foreach($tuteurs as $tuteur)
				{
					if(count($tuteur->GetAdherents()) == 0)
						$tuteur->RemoveFromDatabase();
				}

				// Idem pour le payment, on regarde si le paiement est utilisé par un autre adhérent.
				$rech = $database->Query(
					"SELECT COUNT(*) AS count FROM adherents WHERE id_payment=:id_payment",
					array("id_payment" => $this->id_payment)
				);
				$donnees = $rech->fetch();

				if($donnees != false && $donnees['count'] == 0)
					$payment->RemoveFromDatabase();

				return true;
			}
		}
		
		return false;
	}

	// Charge un adhérent depuis la base de donnée.
	private function LoadFormDatabase()
	{
		if($this->id != null)
		{
			$database = new Database();
			$rech = $database->Query(
				"SELECT * FROM adherents WHERE id_adherent=:id_adherent",
				array("id_adherent" => $this->id)
			);

			if($rech != null)
			{
				$dbData = $rech->fetch();

				$this->id = intval($dbData['id_adherent']);
				$this->id_section = intval($dbData['id_section']);
				$this->id_payment = intval($dbData['id_payment']);
				$this->firstname = $dbData['firstname'];
				$this->lastname = $dbData['lastname'];
				$this->birthday = new DateTime($dbData['birthday']);
				$this->siblings = boolval($dbData['siblings']);
				$this->medicine = boolval($dbData['medicine']);
				$this->medicine_info = $dbData['medicine_info'];
				$this->has_uniform = boolval($dbData['has_uniform']);
				$this->chq_buy_uniform = boolval($dbData['chq_buy_uniform']);
				$this->chq_rent_uniform = boolval($dbData['chq_rent_uniform']);
				$this->chq_clean_uniform = boolval($dbData['chq_clean_uniform']);
				$this->doc_ID_card = boolval($dbData['doc_ID_card']);
				$this->doc_photo = boolval($dbData['doc_photo']);
				$this->doc_fffa = boolval($dbData['doc_fffa']);
				$this->doc_sportmut = boolval($dbData['doc_sportmut']);
				$this->doc_medic_auth = boolval($dbData['doc_medic_auth']);
				$this->inscription_date = $dbData['inscription_date'];

				return true;
			}
		}

		return false;
	}



	// ==============================================================================
	// ==== Fonctions statiques =====================================================
	// ==============================================================================
	static public function GetById($id_adherent)
	{
		$database = new Database();

		$rech = $database->Query(
			"SELECT * FROM adherents WHERE id_adherent=:id_adherent",
			array("id_adherent" => intval($id_adherent))
		);

		if($rech != null)
		{
			$data = $rech->fetch();

			return new Adherent($data);
		}
		
		return false;
	}

	// Retourne la liste de tous les adhérents du club.
	// Par défaut ceux de la saison en cours.
	static public function GetList($saison = null)
	{
		if($saison == null)
			$saison = SnakeTools::GetCurrentSaison();

		$database = new Database();

		$adherents = $database->Query(
			"SELECT * FROM adherents JOIN sections ON adherents.id_section = sections.id_section JOIN payments ON adherents.id_payment = payments.id_payment WHERE saison=:saison",
			array("saison" => $saison)
		);

		if($adherents != null)
		{
			$list = array();

			while($data = $adherents->fetch())
			{
				$adherent = new Adherent($data);
				$adherent->SetSection(new Section($data));
				$adherent->SetPayment(new Payment($data));
				$list[] = $adherent;
			}

			return $list;
		}
		
		return false;
	}

	// Retourne la liste de tous les adhérents de la section souhaité.
	// Par défaut ceux de la saison en cours.
	static public function GetListBySection($id_section)
	{
		$database = new Database();

		$adherents = $database->Query(
			"SELECT * FROM adherents JOIN sections ON adherents.id_section = sections.id_section JOIN payments ON adherents.id_payment = payments.id_payment WHERE adherents.id_section=:id_section",
			array(
				"id_section" => intval($id_section)
			)
		);

		if($adherents != null)
		{
			$list = array();

			while($data = $adherents->fetch())
			{
				$adherent = new Adherent($data);
				$adherent->SetSection(new Section($data));
				$adherent->SetPayment(new Payment($data));
				$list[] = $adherent;
			}

			return $list;
		}
		
		return false;
	}

	static public function GetListByIdPayment($id_payment)
	{
		$database = new Database();
		$adherents = $database->Query(
			"SELECT * FROM adherents WHERE id_payment=:id_payment",
			array("id_payment" => $id_payment)
		);

		if($adherents != null)
		{
			$list = array();

			while($adherent = $adherents->fetch())
				$list[] = new Adherent($adherent);

			return $list;
		}

		return $this->adherents;
	}
}