<?php
namespace Snake;

use System\Database;
use Snake\Adherent;
use Snake\Tuteur;
use Snake\Payment;
use Snake\EInscriptionStep;

/**
 * Outil de gestion d'inscription.
 */
class Inscription
{
	// ==== ATTRIBUTS ====
	/**
	 * @var array $_adherents Liste des adhérents à inscrire.
	 */
	private array $_adherents = [];
	
	/**
	 * @var array $_tuteurs Liste des tuteurs associés au adhérents à inscrire.
	 */
	private array $_tuteurs = [];
	
	/**
	 * @var bool $_authorization Informe si les termes d'inscription ont bien été acceptés.
	 */
	private bool $_authorization = false;
	
	/**
	 * @var Payment|null $_adherents Paiement généré pour l'inscription.
	 */
	private Payment $_payment = null;
	
	/**
	 * @var EInscriptionStep $_state Etape en cours pour l'inscription.
	 */
	private EInscriptionStep $_state;
	

	public function __construct()
	{
		$this->_payment = new Payment();
		$this->_state = EInscriptionStep::Adherents;
	}
	

	// == Getters / Setters ==
	/**
	 * Retourne la liste des adhérents à inscrire
	 * 
	 * @return array
	 */
	public function getAdherents(): array
	{
		return $this->_adherents;
	}
	
	/**
	 * Retourne la liste des tuteurs associés aux adhérents à inscrire
	 * 
	 * @return array
	 */
	public function getTuteurs(): array
	{
		return $this->_tuteurs;
	}

	/**
	 * Retourne le paiement généré pour l'inscription.
	 * 
	 * @return array
	 */
	public function getPayment(): Payment
	{
		return $this->_payment;
	}

	/**
	 * Retourne l'état de l'inscription.
	 * 
	 * @return EInscriptionStep
	 */
	public function getState(): EInscriptionStep
	{
		return $this->_state;
	}
	
	/**
	 * Définie si les termes de l'inscription ont été acceptés ou non.
	 * 
	 * @param bool $authorization
	 * @return void
	 */
	public function setAuthorization(bool $authorization): void
	{
		$this->_authorization = $authorization;
	}
	
	// ==== AUTRES METHODES ====
	/**
	 * Change l'étape de l'inscription.
	 * 
	 * @param EInscriptionStep $state Nouvelle étape de l'inscription
	 * @return void
	 */
	public function changeState(EInscriptionStep $state): void
	{
		$this->_state = $state;
	}

	/**
	 * Ajoute un nouvel adhérent à la liste des adhérents à inscrire.
	 * 
	 * @param Adherent $adherent Adhérent à inscrire.
	 * @return void
	 */
	public function addAdherent(Adherent $adherent): void
	{
		if ($this->_payment !== null) {
			$adherent->setPayment($this->_payment);
		}

		$this->_adherents[] = $adherent;

		// Met à jour si c'est une fratrie ou non.
		if (count($this->_adherents) >= 2) {
			foreach ($this->_adherents as $adherent) {
				$adherent->setSiblings(true);
			}
		}
	}
	
	/**
	 * Ajoute un tuteur àla liste des tuteurs responsable des adhérents à inscrire.
	 * 
	 * @param Tuteur $tuteur.
	 * @return void
	 */
	public function addTuteur(Tuteur $tuteur): void
	{
		$this->_tuteurs[] = $tuteur;
	}
	
	/**
	 * Retourne le nombre d'adhérent à inscrire.
	 * 
	 * @return int
	 */
	public function numberOfAdherents(): int
	{
		return count($this->_adherents);
	}
	
	/**
	 * Retourne le nombre de tuteurs responsable des adhérents à inscrire.
	 * 
	 * @return int
	 */
	public function numberOfTuteurs(): int
	{
		return count($this->_tuteurs);
	}

	/**
	 * Vide la liste des adhérents à inscrire.
	 * 
	 * @return void
	 */
	public function clearAdherents(): void
	{
		$this->_adherents = [];
	}

	/**
	 * Vide la liste des tuteur responsable des adhérents à inscrire.
	 * 
	 * @return void
	 */
	public function clearTuteurs(): void
	{
		$this->_tuteurs = [];
	}

	/**
	 * Calcule le montant total de la cotisation hors réductions (Cotisation + tenues) et l'affecte au paiement.
	 * 
	 * @return void.
	 */
	public function computeCotisation(): void
	{
		$base_price = 0;
		$fixed_price = 0;
		
		foreach($this->_adherents as $adherent)	{
			$base_price += $adherent->getSection()->getCotisationPrice();
			
			if(!$adherent->getTenue()) {
				$fixed_price += $adherent->getSection()->getUniformPrice();
			}
		}

		if($this->_payment === null) {
			$this->_payment = new Payment();
		}
		
		$this->_payment->setBasePrice($base_price);
		$this->_payment->setFixedPrice($fixed_price);
	}
	
	/**
	 * Sauvegarde  toutes les informations de l'inscription dans la base de données.
	 * 
	 * @return bool Retourne True en cas de succès, sinon False.
	 */
	public function saveToDatabase(): bool
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