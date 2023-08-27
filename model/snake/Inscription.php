<?php
namespace Snake;

use System\Database;
use Snake\Adherent;
use Snake\Tuteur;
use Snake\Payment;
use Snake\Reduction;
use Snake\ReductionPack;

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
	 * @var Payment $_payment Paiement généré pour l'inscription.
	 */
	private Payment $_payment;
	
	// ==== CONSTRUCTOR ====
	public function __construct()
	{
		$this->_payment = new Payment();
	}
	

	// ==== GETTERS ====
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
	 * @return Payment
	 */
	public function getPayment(): Payment
	{
		return $this->_payment;
	}
	
	// ==== OTHER METHODS ====
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
	 * Ajoute une réduction au paiement lors de l'inscription.
	 * 
	 * @param Reduction $reduction.
	 * @return void
	 */
	public function addReduction(Reduction $reduction): void
	{
		$this->_payment->addReduction($reduction);
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
	 * Vide l'inscription des informations saisies.
	 * 
	 * @return void
	 */
	public function clear(): void
	{
		$this->clearAdherents();
		$this->clearTuteurs();
	}

	/**
	 * Calcule le montant total de à payer pour l'inscription.
	 * 
	 * @return float Montant de l'inscription.
	 */
	public function computeFinalPrice(): float
	{
		$this->_payment->clearReductions();
		$base_price = 0;
		$fixed_price = 0;
		
		foreach ($this->_adherents as $adherent) {
			$base_price += $adherent->getSection()->getCotisationPrice();

			switch ($adherent->getUniformOption()) {
				case EUniformOption::Rent:
					$fixed_price += $adherent->getSection()->getRentUniformPrice();
					break;

				case EUniformOption::Buy:
					$fixed_price += $adherent->getSection()->getBuyUniformPrice();
					break;
			}

			if ($adherent->hasPassSport()) {
				$this->_payment->addReduction(ReductionPack::buildPassSportReduction());
			}
		}
		
		$this->_payment->setBasePrice($base_price);
		$this->_payment->setFixedPrice($fixed_price);

		if (count($this->_adherents) > 1) {
			$this->_payment->addReduction(ReductionPack::buildFratrieReduction());
		}
		
		return $this->_payment->getFinalAmount();
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

		if ($this->_payment->getId() !== null) {
			// Save adherents to database.
			foreach ($this->_adherents as $adherent) {
				$adherent->setInscriptionDate();
				$adherent->setPayment($this->_payment);

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
							'adherent_tuteur',
							[
								'id_adherent' => $idA,
								'id_tuteur' => $idP
							]
						);
					}
				}

				return true;
			}
		}

		return false;
	}
}