<?php
namespace Snake;

use Datetime;
use System\Database;
use Snake\SnakeTools;
use Snake\Section;
use Snake\Payment;

/**
 * Représente un adhérent
 */
class Adherent
{
	// ==== ATTRBUTS ====
	/**
	 * ID de l'adhérent.
	 * @var int|null $_id 
	 */
	private ?int $_id = null;

	/**
	 * ID de la section dans laquelle est l'adhérent.
	 * @var int|null $_idSection
	 */
	private ?int $_idSection = null;

	/**
	 * ID du paiement fait par l'adhérent pour la saison en cours.
	 * @var int|null $_idPayment
	 */
	private ?int $_idPayment = null;

	/**
	 * Prénom de l'adhérent.
	 * @var string $_firstname
	 */
	private string $_firstname = '';

	/**
	 * Nom de famille de l'adhérent.
	 * @var string $_lastname
	 */
	private string $_lastname = '';

	/**
	 * Date de naissance de l'adhérent.
	 * @var DateTime $_birthday
	 */
	private DateTime $_birthday = null;

	/**
	 * Informe si l'adhérent fait partie d'une fratrie ou non.
	 * @var bool $_isSiblings
	 */
	private bool $_isSiblings = false;

	/**
	 * Nom du traitement en cours pour l'adhérent.
	 */
	private string $_medicineInfo = '';

	/** 
	 * Informe si l'adhérent à déjà l'uniforme ou non.
	 * @var bool $_hasUniform
	 */
	private bool $_hasUniform = false;

	/**
	 * Informe si l'adhérent a donné le chèque pour l'achat de l'unifome.
	 * @var bool $_chqBuyUniform
	 */
	private bool $_chqBuyUniform = false;

	/**
	 * Informe si l'adhérent a donné de chèque pour la location de la tenue.
	 * @var bool $_chqRentUniform
	 */
	private bool $_chqRentUniform = false;
	
	/**
	 * Informe si l'adhérent a donné de chèque pour le néttoyage de la tenue.
	 * @var bool $_chqCleanUniform
	 */
	private bool $_chqCleanUniform = false;

	/**
	 * Informe si l'adhérent a fournis une pièce d'identité.
	 * @var bool $_docIdCard
	 */
	private bool $_docIdCard = false;
	
	/**
	 * Informe si l'adhérent a fournis une photo.
	 * @var bool $_docPhoto
	 */
	private bool $_docPhoto = false;
	
	/**
	 * Informe si l'adhérent a fournis le document de la FFFA rempli.
	 * @var bool $_docFffa
	 */
	private bool $_docFffa = false;

	/**
	 * Informe si l'adhérent a fournis le document de la Sportmut (Mutuel sportive)
	 * @var bool $_docSportmut
	 */
	private bool $_docSportmut = false;
	
	/**
	 * Informe si l'adhérent a fournis le document d'autorisation médicale.
	 * @var bool $_docMedicAuth
	 */
	private bool $_docMedicAuth = false;
	
	/**
	 * Date et heure d'inscription de l'adhérent.
	 * @var DateTime $_inscriptionDate
	 */
	private DateTime $_inscriptionDate = null;

	/**
	 * Section de l'adhérent.
	 * @var Section $_section
	 */
	private Section $_section = null;
	
	/**
	 * Paiement fait par l'adhérent.
	 * @var Payment $_payment
	 */
	private Payment $_payment = null;
	
	/**
	 * Liste de tuteurs associés à l'adhérent.
	 * @var array $_tuteurs
	 */
	private array $_tuteurs = [];
	
	// ==== CONSTRUCTOR ====
	public function __construct(array $dbData = null)
	{
		if ($dbData != null) {	
			$this->_id = (int)$dbData['id_adherent'];
			$this->_idSection = (int)$dbData['id_section'];
			$this->_idPayment = (int)($dbData['id_payment'];
			$this->_firstname = $dbData['firstname'];
			$this->_lastname = $dbData['lastname'];
			$this->_birthday = new Datetime($dbData['birthday']);
			$this->_siblings = (bool)$dbData['siblings'];
			$this->_medicineInfo = $dbData['medicine_info'];
			$this->_hasUniform = (bool)$dbData['has_uniform'];
			$this->_chqBuyUniform = (bool)$dbData['chq_buy_uniform'];
			$this->_chqRentUniform = (bool)$dbData['chq_rent_uniform'];
			$this->_chqCleanUniform = (bool)$dbData['chq_clean_uniform'];
			$this->_docIdCard = (bool)$dbData['doc_ID_card'];
			$this->_docPhoto = (bool)$dbData['doc_photo'];
			$this->_docFffa = (bool)$dbData['doc_fffa'];
			$this->_docSportmut = (bool)$dbData['doc_sportmut'];
			$this->_docMedicAuth = (bool)$dbData['doc_medic_auth'];
			$this->_inscriptionDate = new DateTime($dbData['inscription_date']);
		}
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
	 * Retourne l'ID de la section de l'adhérent.
	 * 
	 * @return int|null
	 */
	public function getIdSection(): int|null
	{
		if ($this->_section !== null) {
			return $this->_section->getId();
		}

		if ($this->_idSection !== null) {
			return $this->_idSection;
		}

		return null
	}

	/**
	 * Retourne l'ID du paiement fait par l'adhérent.
	 * 
	 * @return int|null
	 */
	public function getIdPayment(): int|null
	{
		if ($this->_payment !== null) {
			return $this->_payment->getId();
		}

		if ($this->_idPayment !== null) {
			return $this->_idPayment;
		}

		return null;
	}

	/**
	 * Retourne le prénom de l'adhérent.
	 * 
	 * @return string
	 */
	public function getFirstname(): string
	{
		return $this->_firstname;
	}

	/**
	 * Retourne le nom de famill de l'adhérent.
	 * 
	 * @return string
	 */
	public function getLastname(): string
	{
		return $this->_lastname;
	}

	/**
	 * Retourne la date de naissance de l'adhérent.
	 * 
	 */
	public function getBirthday(): DateTime
	{
		return $this->_birthday;
	}

	/**
	 * Retourne True si l'adhérent appartient à une fratrie, sinon False.
	 * 
	 * @return bool
	 */
	public function getIsSiblings(): bool
	{
		return $this->_isSiblings;
	}

	/**
	 * Retourne True si l'adhérent prend un traitement médical, sinon False.
	 * 
	 * @return bool
	 */
	public function hasMedicine(): bool
	{
		return $this->_medicineInfo !== '';
	}

	/**
	 * Retourne de le traitement pris par l'adhérent.
	 * 
	 * @return string
	 */
	public function getMedicineInfo(): string
	{
		return $this->_medicineInfo;
	}

	/**
	 * Retourne True si l'adhérent poscède l'uniforme, sinon False.
	 * 
	 * @return bool
	 */
	public function hasUniform(): bool
	{
		return $this->_hasUniform;
	}

	/**
	 * Retourne True si l'adhérent à fourni une pièce d'identité, sinon False.
	 * 
	 * @return bool
	 */
	public function hasDocIdCard(): bool
	{
		return $this->_docIdCard;
	}

	/**
	 * Retourne True si l'adhérent à fourni une photo, sinon False.
	 * 
	 * @return bool
	 */
	public function hasPhoto(): bool
	{
		return $this->_docPhoto;
	}

	/**
	 * Retourne True si l'adhérent à fourni le document de la FFFA rempli, sinon False.
	 * 
	 * @return bool
	 */
	public function hasDocFffa(): bool
	{
		return $this->_docFffa;
	}

	/**
	 * Retourne True si l'adhérent à fourni le document de Sportmut, sinon False.
	 * 
	 * @return bool
	 */
	public function hasDocSportmut(): bool
	{
		return $this->_docSportmut;
	}

	/**
	 * Retourne True si l'adhérent à fourni le document d'autorisation médical, sinon False.
	 * 
	 * @return bool
	 */
	public function hasDocMedicAuth(): bool
	{
		return $this->_docMedicAuth;
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
	 * Retourne la Section lié à l'adhérent. Si besoin, charge les données de la BDD.
	 * 
	 * @return Section|null
	 */
	public function getSection(): Section|null
	{
		if($this->_section === null && $this->_idSection !== null)
		{
			$section = Section::getById($this->_idSection);

			if ($section !== false) {
				$this->_section = $section;
			}
		}

		return $this->_section;
	}

	/**
	 * Retourne l'objet Payment lié à l'adhérent. Si besoin, charge les données de la BDD.
	 * 
	 * @return Payment|null
	 */
	public function getPayment(): Payment|null
	{
		if($this->_payment === null && $this->_idPayment !== null)
		{
			$payment = Payment::getById($this->_idPayment);

			if ($payment !== false) {
				$this->_payment = $payment;
			}
		}

		return $this->_payment;
	}

	/**
	 * Retourne la liste des tuteurs lié à l'adhérent. Si besoin, charge les données de la BDD.
	 * 
	 * @return Tuteur[]
	 */

	public function getTuteurs(): array
	{
		if($this->id !== null && count($this->tuteurs) === 0)
		{
			$database = new Database();
			$tuteurs = $database->Query(
				"SELECT * FROM adherent_tuteur JOIN tuteurs ON adherent_tuteur.id_tuteur = tuteurs.id_tuteur WHERE id_adherent=:id_adherent",
				array("id_adherent" => $this->id)
			);

			if ($tuteurs != null)
			{
				while($tuteur = $tuteurs->fetch())
					$this->_tuteurs[] = new Tuteur($tuteur);
			}
		}

		return $this->_tuteurs;
	}
	
	// ==== SETTERS ====
	/**
	 * Définie l'ID de l'adhérent.
	 * 
	 * @param int $id
	 * @return void
	 */
	public function setId(int $id): void
	{
		$this->_id = $id;
	}

	/**
	 * Définie le prénom de l'adhérent.
	 * 
	 * @param string $firstname
	 * @return bool Retourne True en cas de succès, sinon False.
	 */
	public function setFirstname(string $firstname): bool
	{
		if ($firstname !== '') {
			$this->_firstname = trim(ucwords(mb_strtolower($firstname)));

			return true;
		}

		return false;
	}
	
	/**
	 * Définie le nom de famille de l'adhérent.
	 * 
	 * @param string $lastname
	 * @return bool Retourne True en cas de succès, sinon False.
	 */
	public function setLastname(string $lastname): bool
	{
		if ($lastname != '') {
			$this->_lastname = trim(ucwords(mb_strtolower($lastname)));

			return true;
		}

		return false;
	}
	
	/**
	 * Définie la date de naissance de l'adhérent.
	 * 
	 * @param string $birthday
	 * @return bool Retourne True en cas de succès, sinon False.
	 */
	public function setBirthday(string $birthday): bool
	{
		$birthday = trim($birthday);
		
		//if(preg_match('/^([0-2][0-9]|(3)[0-1])(\/|-)(((0)[0-9])|((1)[0-2]))(\/|-)\d{4}$/i', $birthday)) // jj-mm-aaaa
		if (preg_match('/^\d{4}(\/|-)(((0)[0-9])|((1)[0-2]))(\/|-)([0-2][0-9]|(3)[0-1])$/i', $birthday)) { // yyyy-mm-dd
			$this->_birthday = new DateTime($birthday);
			$section = SnakeTools::findSection($birthday);

			if($section !== false) {
				$this->_section = $section;
				$this->_idSection = $section->getId();
			}

			return true;
		}

		return false;
	}

	/**
	 * Définie la date de naissance de l'adhérent.
	 * 
	 * @param bool $isSiblings
	 * @return void
	 */
	public function setIsSiblings(bool $isSiblings): void
	{
		$this->_isSiblings = $isSiblings;
	}
	
	/**
	 * Définie le traitement de l'adhérent s'il en a un.
	 * 
	 * @param string $medicineInfo
	 * @return bool Retourne True en cs de succès, sinon False.
	 */
	public function setMedicineInfo(string $medicineInfo): bool
	{
		if($medicineInfo !== '') {
			$this->_medicineInfo = $medicineInfo;

			return true;
		}

		return false;
	}
	
	/**
	 * Définie si l'adhérent à un uniforme ou non.
	 * 
	 * @param bool $uniform
	 * @return void
	 */
	public function setUniform(bool $uniform): void
	{
		$this->_hasUniform = uniform;
	}
	
	/**
	 * Définie si l'adhérent à fourni le document de Sportmut.
	 * 
	 * @param bool $docSportmut
	 * @return void
	 */
	public function setSportmut(bool $docSportmut): void
	{
		$this->_docSportmut = $docSportmut;
	}

	/**
	 * Définie la date d'inscription de l'adhérent.
	 * 
	 * @param string $inscriptionDate Si la date d'inscription est ommise, la date du jour sera mise.
	 * @return void
	 */
	public function setInscriptionDate(?string $inscriptionDate = null): void
	{
		if ($inscriptionDate !== null) {
			$this->_inscriptionDate = new DateTime();
		} else {
			$this->_inscriptionDate = new DateTime($inscriptionDate);
		}
	}

	/**
	 * Définie la section de l'adhérent.
	 * 
	 * @param Section $section
	 * @return void
	 */
	public function setSection(Section $section): void
	{
		$this->_section = $section;
		$this->_idSection = $section->getId();
	}

	/**
	 * Définie la section de l'adhérent.
	 * 
	 * @param Payment $payment
	 * @return void
	 */
	public function setPayment(Payment $payment): void
	{
		$this->_payment = $payment;
		$this->_idPayment = $payment->getId();
	}
	
	// ==== AUTRES METHODES ====
	/**
	 * Définie les informations de l'adhérent à partir des données fournis dans le formulaire d'inscription.
	 * 
	 * @param array $infos Infos saisie lors de l'inscription
	 * @return bool Retourne True en cas de succès, sinon False.
	 */
	public function setInformation(array $infos): bool
	{
		$result = true;

		if (isset($infos['firstname'])) {
			$result &= $this->setFirstname($infos['firstname']);
		}
		
		if (isset($infos['lastname'])) {
			$result &= $this->setLastname($infos['lastname']);
		}
		
		if (isset($infos['birthday'])) {
			$result &= $this->setBirthday($infos['birthday']);
		}
		
		if (isset($infos['infoMedicine'])) {
			$this->setMedicineInfo($infos['infoMedicine']);
		}
		
		if (isset($infos['tenue'])) {
			$result &= $this->setUniform($infos['tenue']);
		}
			
		return $result;
	}

	/**
	 * Sauvegarde les informations de l'adhérent dans la base de données.
	 * 
	 * @return bool Retourne True en cas de succès, sinon False.
	 */
	public function saveToDatabase(): bool
	{
		$database = new Database();

		if ($this->_id === null) { // Insert
			$id = $database->insert(
				"adherents",
				[
					'id_section' => $this->_idSection,
					'id_payment' => $this->_idPayment,
					'firstname' => $this->_firstname,
					'lastname' => $this->_lastname,
					'birthday' => $this->_birthday->format('Y-m-d'),
					'siblings' => $this->_isSiblings,
					'medicine_info' => $this->_medicineInfo,
					'has_uniform' => $this->_hasUniform,
					'chq_buy_uniform' => $this->_chqBuyUniform,
					'chq_rent_uniform' => $this->_chqRentUniform,
					'chq_clean_uniform' => $this->_chqCleanUniform,
					'doc_ID_card' => $this->_docIdCard,
					'doc_photo' => $this->_docPhoto,
					'doc_fffa' => $this->_docFffa,
					'doc_sportmut' => $this->_docSportmut,
					'doc_medic_auth' => $this->_docMedicAuth,
					'inscription_date' => $this->_inscriptionDate->format('Y-m-d H:i:s')
				]
			);

			if ($id !== false) {
				$this->_id = (int)$id;
				return true;
			}
			
			return false;
		} else { // Update
			$result = $database->update(
				'adherents', 'id_adherent', $this->_id,
				array(
					'id_section' => $this->_idSection,
					'id_payment' => $this->_idPayment,
					'firstname' => $this->_firstname,
					'lastname' => $this->_lastname,
					'birthday' => $this->_birthday->format('Y-m-d'),
					'siblings' => $this->_siblings,
					'medicine_info' => $this->_medicineInfo,
					'has_uniform' => $this->_hasUniform,
					'chq_buy_uniform' => $this->_chqBuyUniform,
					'chq_rent_uniform' => $this->_chqRentUniform,
					'chq_clean_uniform' => $this->_chqCleanUniform,
					'doc_ID_card' => $this->_docIdCard,
					'doc_photo' => $this->_docPhoto,
					'doc_fffa' => $this->_docFffa,
					'doc_sportmut' => $this->_docSportmut,
					'doc_medic_auth' => $this->_docMedicAuth,
					'inscription_date' => $this->_inscriptionDate->format('Y-m-d H:i:s')
				)
			);

			return $result;
		}
	}

	/**
	 * Supprime l'adhérent et toutes les données lié à lui de la base de données.
	 * 
	 * @return bool Retourne True en cas de succès, sinon False.
	 */
	public function removeFromDatabase(): bool
	{
		if ($this->_id != null) {
			$database = new Database();

			// Recherche l'adhérent dans la table de liaison.
			$tuteurs = $this->getTuteurs();
			$payment = $this->getPayment();

			// Suppression de l'adhérent
			$result = true;
			$result &= $database->delete('adherent_tuteur', 'id_adherent', $this->_id);
			$result &= $database->delete('adherents', 'id_adherent', $this->_id);

			// Verification du reste de la BDD (Payments et tuteurs)
			if ($result) {
				// Pour chaque tuteurs, on cherche si ils ont des adhérents. Si non, on supprime.
				foreach ($tuteurs as $tuteur) {
					if (count($tuteur->getAdherents()) === 0) {
						$tuteur->removeFromDatabase();
					}
				}

				// Idem pour le payment, on regarde si le paiement est utilisé par un autre adhérent.
				$rech = $database->query(
					"SELECT COUNT(*) AS count FROM adherents WHERE id_payment=:id_payment",
					array('id_payment' => $this->id_payment)
				);
				$donnees = $rech->fetch();

				if ($donnees !== false && $donnees['count'] === 0) {
					$payment->removeFromDatabase();
				}

				return true;
			}
		}
		
		return false;
	}


	// ==============================================================================
	// ==== Fonctions statiques =====================================================
	// ==============================================================================
	/**
	 * Retourne un adhérent suivant son ID.
	 * 
	 * @param int $idAdherent
	 * @return Adherent|false Retourne l'instance de l'adhérent, sinon False si l'adhérent n'existe pas.
	 */
	public static function getById(int $idAdherent): Adherent|false
	{
		$database = new Database();

		$rech = $database->query(
			"SELECT * FROM adherents WHERE id_adherent=:id_adherent",
			['id_adherent' => (int)$idAdherent]
		);

		if ($rech != null) {
			$data = $rech->fetch();

			return new Adherent($data);
		}
		
		return false;
	}

	/**
	 * Retourne la liste de tous les adhérents du club.Par défaut ceux de la saison en cours.
	 * 
	 * @param string $saison Saison des adhérents à retourner.
	 * @return array|false Retourne la liste d'adhérents, sinon False en cas d'échec.
	 */
	public static function getList(string $saison = null): array|false
	{
		if ($saison === null) {
			$saison = SnakeTools::getCurrentSaison();
		}

		$database = new Database();

		$adherents = $database->query(
			"SELECT * FROM adherents JOIN sections ON adherents.id_section = sections.id_section JOIN payments ON adherents.id_payment = payments.id_payment WHERE saison=:saison",
			['saison' => $saison]
		);

		if ($adherents != null) {
			$list = [];

			while ($data = $adherents->fetch()) {
				$adherent = new Adherent($data);
				$adherent->setSection(new Section($data));
				$adherent->setPayment(new Payment($data));
				$list[] = $adherent;
			}

			return $list;
		}
		
		return false;
	}

	/**
	 * Retourne la liste de tous les adhérents de la section souhaité.
	 * 
	 * @param int $idSection ID de la section souhaité
	 * @return array|false Retourne la liste d'adhérents, sinon False en cas d'échec.
	 */
	public static function getListBySection(int $idSection): array|false
	{
		$database = new Database();

		$adherents = $database->query(
			"SELECT * FROM adherents JOIN sections ON adherents.id_section = sections.id_section JOIN payments ON adherents.id_payment = payments.id_payment WHERE adherents.id_section=:id_section",
			['id_section' => $idSection]
		);

		if($adherents != null) {
			$list = [];

			while ($data = $adherents->fetch()) {
				$adherent = new Adherent($data);
				$adherent->setSection(new Section($data));
				$adherent->setPayment(new Payment($data));
				$list[] = $adherent;
			}

			return $list;
		}
		
		return false;
	}

	/**
	 * Retourne la liste de tous les adhérents associé au paiement souhaité.
	 * 
	 * @param int $idPayment ID du paiement souhaité
	 * @return array|false Retourne la liste d'adhérents, sinon False en cas d'échec.
	 */
	public static function getListByIdPayment(int $idPayment)
	{
		$database = new Database();
		$adherents = $database->query(
			"SELECT * FROM adherents WHERE id_payment=:id_payment",
			['id_payment' => $idPayment]
		);

		if ($adherents != null) {
			$list = [];

			while ($adherent = $adherents->fetch()) {
				$list[] = new Adherent($adherent);
			}

			return $list;
		}

		return false;
	}
}