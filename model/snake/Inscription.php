<?php
namespace Snake;

use System\Database;
use Snake\Adherent;
use Snake\Tuteur;
use Snake\Payment;

class Inscription
{
	// ==============================================================================
	// ==== Enumérations ============================================================
	// ==============================================================================
	// Etapes de l'inscription
	static public $STEPS = array(
		"Adherents" => 1,
		"Tuteurs" => 2,
		"Authorization" => 3,
		"Payment" => 4,
		"Validation" => 5
	);

	
	// ==============================================================================
	// ==== Classe ==================================================================
	// ==============================================================================
	// == ATTRIBUTS ==
	private $adherents = array();
	private $tuteurs = array();
	private $authorization = false;
	private $payment = null;
	private $state;
	
	// == METHODES PRIMAIRES ==
	public function __construct()
	{
		$this->payment = new Payment();
		$this->state = self::$STEPS['Adherents'];
	}
	
	// == METHODES GETTERS ==
	public function GetAdherents()
	{
		return $this->adherents;
	}
	
	public function GetTuteurs()
	{
		return $this->tuteurs;
	}

	public function GetPayment()
	{
		return $this->payment;
	}

	public function GetState()
	{
		return $this->state;
	}
	
	// == METHODES SETTERS ==
	public function SetAuthorization($authorization)
	{
		$this->authorization = $authorization;
	}
	
	// == AUTRES METHODES ==
	public function ChangeState(int $state)
	{
		$this->state = $state;
	}

	public function AddAdherent(Adherent $adherent)
	{
		$adherent->SetPayment($this->payment);

		$this->adherents[] = $adherent;

		// Met à jour si c'est une fratrie ou non.
		if(count($this->adherents) >= 2)
		{
			foreach($this->adherents as $adherent)
				$adherent->SetSiblings(true);
		}
	}
	
	public function AddTuteur(Tuteur $tuteur)
	{
		$this->tuteurs[] = $tuteur;
	}
	
	public function CountAdherents() : int
	{
		return count($this->adherents);
	}
	
	public function CountTuteurs() : int
	{
		return count($this->tuteurs);
	}

	public function ClearAdherents()
	{
		$this->adherents = array();
	}
	
	public function ClearTuteurs()
	{
		$this->tuteurs = array();
	}

	// Fonction d'ajout d'une réduction dans le cadre du Covid-19.
	// A mettre à jour en fonction du besoin.
	public function ApplyReductionCovid()
	{
		$nbAdh = count($this->adherents);

		if($nbAdh > 0 && count($this->tuteurs) > 0)
		{
			// Recherche si les adhérents étaient inscrit la saison précédente.
			$number = 0;

			foreach($this->tuteurs as $tuteur)
			{
				$database = new Database();
				$rech = $database->Query(
					"SELECT COUNT(*) FROM adherent_tuteur
					JOIN adherents ON adherent_tuteur.id_adherent = adherents.id_adherent
					JOIN tuteurs ON adherent_tuteur.id_tuteur = tuteurs.id_tuteur
					JOIN sections ON sections.id_section = adherents.id_section
					WHERE saison=:saison AND tuteurs.email=:email",
					array(
						"saison" => SnakeTools::GetPreviousSaison(),
						"email" => $tuteur->GetEmail()
					)
				);

				if($rech != null)
				{
					$data = $rech->fetch();
					$val = intval($data['COUNT(*)']);

					if($val > 0)
						$number = $val;
				}
			}
			
			// Si la réduction est applicable
			if($number > 0)
			{
				if($number > $nbAdh)
					$number = $nbAdh;

				$reduc = new Reduction();
				$reduc->SetType(Reduction::$TYPE['Amount']);
				$reduc->SetValue($number * 30); // 30€ de réduction par ancien adhérent.
				$reduc->SetSujet("Covid-19 saison 2019-2020");
				
				$this->payment->AddReduction($reduc);
			}
		}

		return false;
	}

	// Calcule le montant total de la cotisation hors réductions (Cotisation + tenues).
	public function ComputeCotisation()
	{
		$base_price = 0;
		$fixed_price = 0;
		
		foreach($this->adherents as $adherent)
		{
			$base_price += $adherent->GetSection()->GetPriceCotisation();
			
			if(!$adherent->GetTenue())
				$fixed_price += $adherent->GetSection()->GetPriceUniform();
		}
		
		$this->payment->SetBasePrice($base_price);
		$this->payment->SetFixedPrice($fixed_price);
	}
	
	// Sauvegarde l'inscription dans la base de données.
	public function SaveToDatabase() : bool
	{
		$database = new Database();

		$idAdherents = array();
		$idTuteurs = array();

		// Save payment
		$this->payment->SaveToDatabase();

		if($this->payment->GetId() != null)
		{
			// Save adherents to database.
			foreach($this->adherents as $adherent)
			{
				$adherent->SetInscriptionDate();

				if($adherent->SaveToDatabase())
					$idAdherents[] = $adherent->GetId();
			}
			
			// Save tuteurs to database.
			foreach($this->tuteurs as $tuteur)
			{
				if($tuteur->SaveToDatabase())
					$idTuteurs[] = $tuteur->GetId();
			}
			
			// Make links between adherents and tuteurs.
			if(count($idAdherents) > 0 && count($idTuteurs) > 0)
			{
				foreach($idAdherents as $idA)
				{
					foreach($idTuteurs as $idP)
					{
						$database->Insert(
							"adherent_tuteur",
							array(
								"id_adherent" => $idA,
								"id_tuteur" => $idP
							)
						);
					}
				}

				return true;
			}
		}

		return false;
	}
}