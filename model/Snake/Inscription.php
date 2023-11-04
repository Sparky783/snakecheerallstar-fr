<?php
namespace Snake;

use Exception;
use ErrorException;
use Datetime;
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
	 * @var int|null $_id ID de la réduction.
	 */
	private ?int $_id = null;

	/**
	 * Date et heure d'inscription de l'adhérent.
	 * @var DateTime|null $_inscriptionDate
	 */
	private ?DateTime $_inscriptionDate = null;

	/**
	 * @var string $_saison Saison concernée par l'inscription.
	 */
	private $_saison = '';

	/**
	 * @var string $_accessKey Clé d'accès pour accéder au dossier d'inscription.
	 */
	private $_accessKey = '';

	/**
	 * @var array $_adherents Liste des adhérents à inscrire.
	 */
	private array $_adherents = [];
	
	/**
	 * @var array $_tuteurs Liste des tuteurs associés au adhérents à inscrire.
	 */
	private array $_tuteurs = [];
	
	/**
	 * @var Payment|null $_payment Paiement généré pour l'inscription.
	 */
	private ?Payment $_payment = null;

	
	// ==== CONSTRUCTOR ====
	public function __construct(array $dbData = [])
	{
		if (count($dbData) !== 0) {
			$this->_id = (int)$dbData['id_inscription'];
			$this->_inscriptionDate = new Datetime($dbData['inscription_date']);
			$this->_saison = $dbData['saison'];
			$this->_accessKey = $dbData['access_key'];
		}
	}

	/**
	 * Surcharge Serializable interface
	 * 
	 * @return array
	 */
	public function __serialize(): array
	{
		$data = [
			'inscription_date' => serialize($this->_inscriptionDate),
			'saison' => $this->_saison,
			'access_key' => $this->_accessKey,
			'adherents' => [],
			'tuteurs' => [],
			'payment' => serialize($this->_payment),
		];

		foreach ($this->_adherents as $adherent) {
			$data['adherents'][] = serialize($adherent);
		}

		foreach ($this->_tuteurs as $tuteurs) {
			$data['tuteurs'][] = serialize($tuteurs);
		}

		return $data;
	}

	/**
	 * Surcharge Serializable interface
	 * 
	 * @param array $data
	 * @return void
	 */
	public function __unserialize(array $data): void
	{
		$this->_inscriptionDate = unserialize($data['inscription_date']);
		$this->_saison = $data['saison'];
		$this->_accessKey = $data['access_key'];

		$this->_adherents = [];

		foreach ($data['adherents'] as $adherent) {
			$this->_adherents[] = unserialize($adherent);
		}

		$this->_tuteurs = [];

		foreach ($data['tuteurs'] as $tuteur) {
			$this->_tuteurs[] = unserialize($tuteur);
		}

        $this->_payment = unserialize($data['payment']);
	}

	// ==== GETTERS ====
	/**
	 * Retourne l'ID de l'adhérent.
	 * 
	 * @return int|null
	 */
	public function getId(): int|null
	{
		return $this->_id;
	}

	/**
	 * Retourne la date d'inscription.
	 * 
	 * @return DateTime
	 */
	public function getInscriptionDate(): DateTime
	{
		return $this->_inscriptionDate;
	}

	/**
	 * Retourne la saison associée à l'inscription.
	 * 
	 * @return string
	 */
	public function getSaison(): string
	{
		return $this->_saison;
	}
	
	/**
	 * Retourne la clé d'accès au dossier d'inscription.
	 * 
	 * @return string
	 */
	public function getAccessKey(): string
	{
		return $this->_accessKey;
	}

	/**
	 * Retourne la liste des adhérents à inscrire
	 * 
	 * @return array
	 */
	public function getAdherents(): array
	{
		if (empty($this->_adherents) && $this->_id !== null) {
			$this->_adherents = Adherent::getByIdInscription($this->_id);
		}

		return $this->_adherents;
	}
	
	/**
	 * Retourne la liste des tuteurs associés aux adhérents à inscrire
	 * 
	 * @return array
	 */
	public function getTuteurs(): array
	{
		if (empty($this->_tuteurs) && $this->_id !== null) {
			$this->_tuteurs = Tuteur::getByIdInscription($this->_id);
		}
		
		return $this->_tuteurs;
	}

	/**
	 * Retourne le paiement généré pour l'inscription.
	 * 
	 * @return Payment
	 */
	public function getPayment(): Payment
	{
		if ($this->_payment === null && $this->_id !== null) {
			$paiementList = Payment::getByIdInscription($this->_id);

			if (count($paiementList) > 1) {
				throw new Exception("Plusieurs paiements sont associés au dossier d'inscription ID: " . $this->_id);
			}

			$this->_payment = $paiementList[0];
		}

		return $this->_payment;
	}

	// ==== SETTERS ====
	/**
	 * Définie l'ID de l'adhérent.
	 * 
	 * @param int|null $id
	 * @return void
	 */
	public function setId(int|null $id): void
	{
		if ($id === 0) {
			$this->_id = null;
		} else {
			$this->_id = $id;
		}
	}

	/**
	 * Définie la date d'inscription.
	 * 
	 * @param string $inscriptionDate Si la date d'inscription est omise, la date du jour sera mise.
	 * @return void
	 */
	public function setInscriptionDate(string $inscriptionDate = ''): void
	{
		if (!empty($inscriptionDate)) {
			$this->_inscriptionDate = new DateTime($inscriptionDate);
		} else {
			$this->_inscriptionDate = new DateTime();
		}
	}

	/**
	 * Définie la saison associée à l'inscription.
	 * 
	 * @param string $saison
	 * @return void
	 */
	public function setSaison(string $saison): void
	{
		$this->_saison = $saison;
	}

	/**
	 * Définie la clé d'assès au dossier d'inscription.
	 * 
	 * @param string $accessKey
	 * @return void
	 */
	public function setAccessKey(string $accessKey): void
	{
		$this->_accessKey = $accessKey;
	}

	/**
	 * Définie le paiement du dossier d'inscription.
	 * 
	 * @param Payment $payment
	 * @return void
	 */
	public function setPayment(Payment $payment): void
	{
		$this->_payment = $payment;
	}
	
	// ==== OTHER METHODS ====
	/**
	 * Initialise l'objet pour une nouvelle inscription
	 * 
	 * @return void
	 */
	public function init(): void
	{
		$this->clearAdherents();
		$this->clearTuteurs();
		$this->_payment = new Payment();

		$this->setInscriptionDate();
		$this->setSaison(SnakeTools::getCurrentSaison());
		$this->setAccessKey(SnakeTools::genarateAccessKey());
	}

	/**
	 * Ajoute un nouvel adhérent à la liste des adhérents à inscrire.
	 * 
	 * @param Adherent $adherent Adhérent à inscrire.
	 * @return void
	 */
	public function addAdherent(Adherent $adherent): void
	{
		$this->_adherents[] = $adherent;

		// Met à jour si c'est une fratrie ou non.
		if (count($this->_adherents) >= 2) {
			foreach ($this->_adherents as $adherent) {
				$adherent->setIsSiblings(true);
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
	 * Calcule le montant total de à payer pour l'inscription.
	 * 
	 * @return float Montant de l'inscription.
	 */
	public function computeFinalPrice(): float
	{
		if ($this->_payment === null) {
			throw new ErrorException('The payment object must be defined.');
		}

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
	 * Sauvegarde uniquement les informations du dossier d'inscription dans la base de données.
	 * 
	 * @return bool Retourne True en cas de succès, sinon False.
	 */
	public function saveToDatabase(): bool
	{
		$database = new Database();

		if ($this->_id == null) { // Insert
			$id = $database->insert(
				'inscriptions',
				[
					'inscription_date' => $this->_inscriptionDate->format('Y-m-d H:i:s'),
					'saison' => $this->_saison,
					'access_key' => $this->_accessKey
				]
			);

			if ($id !== false) {
				$this->_id = (int)$id;
				return true;
			}

			return false;
		}

		// Update
		return $database->update(
			'inscriptions', 'id_inscription', $this->_id,
			[
				'inscription_date' => $this->_inscriptionDate->format('Y-m-d H:i:s'),
				'saison' => $this->_saison,
				'access_key' => $this->_accessKey
			]
		);
	}

	/**
	 * Sauvegarde toutes les informations du dossier d'inscription (Adhérents, Tuteurs, Paiement, etc...) dans la base de données.
	 * 
	 * @return bool Retourne True en cas de succès, sinon False.
	 */
	public function saveAllToDatabase(): bool
	{
		$result = $this->saveToDatabase();

		if ($result === false) {
			return false;
		}

		// Save adherents to database.
		foreach ($this->_adherents as $adherent) {
			$adherent->setIdInscription($this->_id);
			$result = $result && $adherent->saveToDatabase();
		}
		
		// Save tuteurs to database.
		foreach ($this->_tuteurs as $tuteur) {
			$tuteur->setIdInscription($this->_id);
			$result = $result && $tuteur->saveToDatabase();
		}

		// Save payment
		$this->_payment->setIdInscription($this->_id);
		$result = $result && $this->_payment->saveToDatabase();

		return $result;
	}
	

	// ==============================================================================
	// ==== Fonctions statiques =====================================================
	// ==============================================================================
	/**
	 * Retourne un dossier d'inscription suivant son ID.
	 * 
	 * @param int $idInscription
	 * @return Inscription|false Retourne l'instance du dossier d'inscription, sinon False si le dossier n'existe pas.
	 */
	public static function getById(int $idInscription): Inscription|false
	{
		$database = new Database();

		$rech = $database->query(
			"SELECT * FROM inscriptions WHERE id_inscription=:id_inscription",
			['id_inscription' => (int)$idInscription]
		);

		if ($rech != null) {
			$data = $rech->fetch();

			return new Inscription($data);
		}
		
		return false;
	}

	/**
	 * Retourne une inscription en fonction de la clé d'accès (fournis au tuteurs).
	 *
	 * @param string $key
	 * @return Inscription|false Retourne l'instance du dossier d'inscription, sinon False si le dossier n'existe pas.
	 */
	public static function getByKey(string $accessKey): Inscription|false
	{
		$database = new Database();

		$rech = $database->query(
			"SELECT * FROM inscriptions WHERE access_key=:access_key",
			['access_key' => $accessKey]
		);

		if ($rech != null) {
			$data = $rech->fetch();

			return new Inscription($data);
		}
		
		return false;
	}
}