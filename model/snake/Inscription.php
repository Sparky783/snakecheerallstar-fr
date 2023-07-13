<?php
namespace Snake;

use System\Database;
use Snake\Adherent;
use Snake\Tuteur;
use Snake\Payment;

// ==============================================================================
// ==== Enumérations ============================================================
// ==============================================================================
// Etapes de l'inscription
enum EInscriptionSteps {
	case Adherents;
	case Tuteurs;
	case Authorization;
	case Payment;
	case Validation;
}

class Inscription
{
	// ==============================================================================
	// ==== Classe ==================================================================
	// ==============================================================================
	// == ATTRIBUTS ==
	private array $_adherents = [];
	private array $_tuteurs = []];
	private bool $_authorization = false;
	private Payment $_payment = null;
	private EInscriptionSteps $_state;
	

	public function __construct()
	{
		$this->_payment = new Payment();
		$this->_state = EInscriptionSteps::Adherents;
	}
	

	// == Getters / Setters ==
	public function getAdherents(): array
	{
		return $this->_adherents;
	}
	
	public function getTuteurs(): array
	{
		return $this->_tuteurs;
	}

	public function getPayment(): Payment
	{
		return $this->_payment;
	}

	public function getState(): EInscriptionSteps
	{
		return $this->_state;
	}
	
	public function setAuthorization($authorization): void
	{
		$this->_authorization = $authorization;
	}
	
	// == AUTRES METHODES ==
	public function changeState(EInscriptionSteps $state): void
	{
		$this->_state = $state;
	}

	public function addAdherent(Adherent $adherent): void
	{
		if ($this->_payment !== null) {
			$adherent->setPayment($this->_payment);
		}

		$this->_adherents[] = $adherent;

		// Met à jour si c'est une fratrie ou non.
		if (count($this->_adherents) >= 2) {
			foreach($this->_adherents as $adherent)
				$adherent->setSiblings(true);
		}
	}
	
	public function addTuteur(Tuteur $tuteur): void
	{
		$this->_tuteurs[] = $tuteur;
	}
	
	public function numberOfAdherents(): int
	{
		return count($this->_adherents);
	}
	
	public function numberOfTuteurs(): int
	{
		return count($this->_tuteurs);
	}

	public function clearAdherents(): void
	{
		$this->_adherents = [];
	}
	
	public function clearTuteurs(): void
	{
		$this->_tuteurs = [];
	}

	// Calcule le montant total de la cotisation hors réductions (Cotisation + tenues).
	public function computeCotisation()
	{
		$base_price = 0;
		$fixed_price = 0;
		
		foreach($this->_adherents as $adherent)
		{
			$base_price += $adherent->getSection()->getCotisationPrice();
			
			if(!$adherent->getTenue())
				$fixed_price += $adherent->getSection()->getUniformPrice();
		}
		
		$this->_payment->setBasePrice($base_price);
		$this->_payment->setFixedPrice($fixed_price);
	}
	
	// Sauvegarde l'inscription dans la base de données.
	public function saveToDatabase() : bool
	{
		$database = new Database();

		$idAdherents = [];
		$idTuteurs = [];

		// Save payment
		$this->_payment->saveToDatabase();

		if ($this->_payment->getId() != null) {
			// Save adherents to database.
			foreach ($this->_adherents as $adherent) {
				$adherent->setInscriptionDate();

				if ($adherent->saveToDatabase()) {
					$idAdherents[] = $adherent->getId();
				}
			}
			
			// Save tuteurs to database.
			foreach ($this->_tuteurs as $tuteur) {
				if ($tuteur->saveToDatabase()) {
					$idTuteurs[] = $tuteur->getId();
				}
			}
			
			// Make links between adherents and tuteurs.
			if (count($idAdherents) > 0 && count($idTuteurs) > 0) {
				foreach ($idAdherents as $idA) {
					foreach ($idTuteurs as $idP) {
						$database->insert(
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