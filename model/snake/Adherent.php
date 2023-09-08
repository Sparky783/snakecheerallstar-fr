<?php
namespace Snake;

use Datetime;
use System\Database;
use Snake\SnakeTools;
use Snake\Section;
use Snake\Payment;
use Snake\EUniformOption;

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
	 * @var DateTime|null $_birthday
	 */
	private ?DateTime $_birthday = null;

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
	 * Informe sur le choix de l'adhérent par rapport à l'uniforme (Achat/Location).
	 * @var EUniformOption $_uniformOption
	 */
	private EUniformOption $_uniformOption = EUniformOption::None;

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
	 * Informe si l'adhérent a un code pour bénéficier du Pass Sport.
	 * @var string $_passSport
	 */
	private string $_passSport = '';
	
	/**
	 * Date et heure d'inscription de l'adhérent.
	 * @var DateTime|null $_inscriptionDate
	 */
	private ?DateTime $_inscriptionDate = null;

	/**
	 * Numéro de sécurité sociale de l'adhérent.
	 * @var string $_socialSecurityNumber
	 */
	private string $_socialSecurityNumber = '';

	/**
	 * Nom de la personne à contacter en cas d'urgence pour l'adhérent.
	 * @var string $_nameEmergencyContact
	 */
	private string $_nameEmergencyContact = '';

	/**
	 * Numéro de téléphone de la personne à contacter en cas d'urgence pour l'adhérent.
	 * @var string $_phoneEmergencyContact
	 */
	private string $_phoneEmergencyContact = '';

	/**
	 * Nom du médecin traitant pour l'adhérent.
	 * @var string $_doctorName
	 */
	private string $_doctorName = '';

	/**
	 * Section de l'adhérent.
	 * @var Section|null $_section
	 */
	private ?Section $_section = null;
	
	/**
	 * Paiement fait par l'adhérent.
	 * @var Payment|null $_payment
	 */
	private ?Payment $_payment = null;
	
	/**
	 * Liste de tuteurs associés à l'adhérent.
	 * @var array $_tuteurs
	 */
	private array $_tuteurs = [];
	
	// ==== CONSTRUCTOR ====
	public function __construct(array $dbData = [])
	{
		if (count($dbData) > 0) {	
			$this->_id = (int)$dbData['id_adherent'];
			$this->_idSection = (int)$dbData['id_section'];
			$this->_idPayment = (int)$dbData['id_payment'];
			$this->_firstname = $dbData['firstname'];
			$this->_lastname = $dbData['lastname'];
			$this->_birthday = new Datetime($dbData['birthday']);
			$this->_isSiblings = (bool)$dbData['is_sibling'];
			$this->_medicineInfo = $dbData['medicine_info'];
			$this->_uniformOption = EUniformOption::tryFrom($dbData['uniform_option']) ?? EUniformOption::None;
			$this->_chqBuyUniform = (bool)$dbData['chq_buy_uniform'];
			$this->_chqRentUniform = (bool)$dbData['chq_rent_uniform'];
			$this->_chqCleanUniform = (bool)$dbData['chq_clean_uniform'];
			$this->_docIdCard = (bool)$dbData['doc_ID_card'];
			$this->_docPhoto = (bool)$dbData['doc_photo'];
			$this->_docFffa = (bool)$dbData['doc_fffa'];
			$this->_docSportmut = (bool)$dbData['doc_sportmut'];
			$this->_docMedicAuth = (bool)$dbData['doc_medic_auth'];
			$this->_passSport = $dbData['pass_sport'];
			$this->_socialSecurityNumber = $dbData['social_security_number'];
			$this->_nameEmergencyContact = $dbData['name_emergency_contact'];
			$this->_phoneEmergencyContact = $dbData['phone_emergency_contact'];
			$this->_doctorName = $dbData['doctor_name'];
			$this->_inscriptionDate = new DateTime($dbData['inscription_date']);
		}
	}

	/**
	 * Surcharge Serializable interface
	 * 
	 * @return array
	 */
	public function __serialize(): array
	{
		return [
			'id_adherent' => $this->_id,
			'id_section' => $this->_idSection,
			'id_payment' => $this->_idPayment,
			'firstname' => $this->_firstname,
			'lastname' => $this->_lastname,
			'birthday' => serialize($this->_birthday),
			'is_sibling' => $this->_isSiblings,
			'medicine_info' => $this->_medicineInfo,
			'uniform_option' => $this->_uniformOption,
			'chq_buy_uniform' => $this->_chqBuyUniform,
			'chq_rent_uniform' => $this->_chqRentUniform,
			'chq_clean_uniform' => $this->_chqCleanUniform,
			'doc_ID_card' => $this->_docIdCard,
			'doc_photo' => $this->_docPhoto,
			'doc_fffa' => $this->_docFffa,
			'doc_sportmut' => $this->_docSportmut,
			'doc_medic_auth' => $this->_docMedicAuth,
			'pass_sport' => $this->_passSport,
			'inscription_date' => serialize($this->_inscriptionDate),
			'social_security_number' => $this->_socialSecurityNumber,
			'name_emergency_contact' => $this->_nameEmergencyContact,
			'phone_emergency_contact' => $this->_phoneEmergencyContact,
			'doctor_name' => $this->_doctorName
		];
	}

	/**
	 * Surcharge Serializable interface
	 * 
	 * @param array $data
	 * @return void
	 */
	public function __unserialize(array $data): void
	{
        $this->_id = $data['id_adherent'];
		$this->_idSection = $data['id_section'];
		$this->_idPayment = $data['id_payment'];
		$this->_firstname = $data['firstname'];
		$this->_lastname = $data['lastname'];
		$this->_birthday = unserialize($data['birthday']);
		$this->_isSiblings = $data['is_sibling'];
		$this->_medicineInfo = $data['medicine_info'];
		$this->_uniformOption = $data['uniform_option'];
		$this->_chqBuyUniform = $data['chq_buy_uniform'];
		$this->_chqRentUniform = $data['chq_rent_uniform'];
		$this->_chqCleanUniform = $data['chq_clean_uniform'];
		$this->_docIdCard = $data['doc_ID_card'];
		$this->_docPhoto = $data['doc_photo'];
		$this->_docFffa = $data['doc_fffa'];
		$this->_docSportmut = $data['doc_sportmut'];
		$this->_docMedicAuth = $data['doc_medic_auth'];
		$this->_passSport = $data['pass_sport'];
		$this->_inscriptionDate = unserialize($data['inscription_date']);
		$this->_socialSecurityNumber = $data['social_security_number'];
		$this->_nameEmergencyContact = $data['name_emergency_contact'];
		$this->_phoneEmergencyContact = $data['phone_emergency_contact'];
		$this->_doctorName = $data['doctor_name'];
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

		return null;
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
	 * @return DateTime|null
	 */
	public function getBirthday(): DateTime|null
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
	 * Retourne le choix de l'adhérent par rapport à l'uniforme (Achat/Location)
	 * 
	 * @return EUniformOption
	 */
	public function getUniformOption(): EUniformOption
	{
		return $this->_uniformOption;
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
	 * Retourne True si l'adhérent bénéficie du Pass Sport, sinon False.
	 * 
	 * @return bool
	 */
	public function hasPassSport(): bool
	{
		return !empty($this->_passSport);
	}

	/**
	 * Retourne le code du Pass Sport de l'adhérent.
	 * 
	 * @return bool
	 */
	public function getPassSport(): string
	{
		return $this->_passSport;
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
	 * Retourne le numéro de sécurité sociale de l'adhérent.
	 * 
	 * @return string
	 */
	public function getSocialSecurityNumber(): string
	{
		return $this->_socialSecurityNumber;
	}

	/**
	 * Retourne le nom de la personne à contacter en cas d'urgence pour l'adhérent.
	 * 
	 * @return string
	 */
	public function getNameEmergencyContact(): string
	{
		return $this->_nameEmergencyContact;
	}

	/**
	 * Retourne le numéro de téléphone de la personne à contacter en cas d'urgence pour l'adhérent.
	 * 
	 * @return string
	 */
	public function getPhoneEmergencyContact(): string
	{
		return $this->_phoneEmergencyContact;
	}

	/**
	 * Retourne le nom du médecin traitant de l'adhérent.
	 * 
	 * @return string
	 */
	public function getDoctorName(): string
	{
		return $this->_doctorName;
	}

	/**
	 * Retourne la Section lié à l'adhérent. Si besoin, charge les données de la BDD.
	 * 
	 * @return Section|null
	 */
	public function getSection(): Section|null
	{
		if ($this->_section === null && $this->_idSection !== null) {
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
		if ($this->_payment === null && $this->_idPayment !== null) {
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
		if ($this->_id !== null && count($this->_tuteurs) === 0) {
			$database = new Database();
			$tuteurs = $database->query(
				"SELECT * FROM adherent_tuteur JOIN tuteurs ON adherent_tuteur.id_tuteur = tuteurs.id_tuteur WHERE id_adherent=:id_adherent",
				['id_adherent' => $this->_id]
			);

			if ($tuteurs !== null) {
				while($tuteur = $tuteurs->fetch()) {
					$this->_tuteurs[] = new Tuteur($tuteur);
				}
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
		if ($id === 0) {
			$this->_id = null;
		} else {
			$this->_id = $id;
		}
	}

	/**
	 * Définie l'ID de la sectrion pour l'adhérent.
	 * 
	 * @param int $id
	 * @return void
	 */
	public function setIdSection(int $id): void
	{
		if ($id === 0) {
			$this->_idSection = null;
		} else {
			$this->_idSection = $id;
		}
	}

	/**
	 * Définie l'ID du paiment de l'adhérent.
	 * 
	 * @param int $id
	 * @return void
	 */
	public function setIdPayment(int $id): void
	{
		if ($id === 0) {
			$this->_idPayment = null;
		} else {
			$this->_idPayment = $id;
		}
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
			$section = SnakeTools::findSection($this->_birthday);

			if ($section !== false) {
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
	 * Définie le choix de l'adhérent par rapport à l'uniforme (Achat/Location).
	 * 
	 * @param EUniformOption $uniformOption
	 * @return void
	 */
	public function setUniformOption(EUniformOption $uniformOption): void
	{
		$this->_uniformOption = $uniformOption;
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
	 * Définie le code Pass Sport de l'adhérent.
	 * 
	 * @param string $passSport
	 * @return void
	 */
	public function setPassSport(string $passSport): void
	{
		$this->_passSport = $passSport;
	}

	/**
	 * Définie la date d'inscription de l'adhérent.
	 * 
	 * @param string $inscriptionDate Si la date d'inscription est ommise, la date du jour sera mise.
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
	 * Définie le numéro de sécurité sociale de l'adhérent.
	 * 
	 * @param string $socialSecurityNumber
	 * @return bool
	 */
	public function setSocialSecurityNumber(string $socialSecurityNumber): bool
	{
		if (empty(trim($socialSecurityNumber))) {
			return false;
		}

		$this->_socialSecurityNumber = $socialSecurityNumber;
		return true;
	}

	/**
	 * Définie le nom de la personne à contacter en cas d'urgence pour l'adhérent.
	 * 
	 * @param string
	 * @return bool
	 */
	public function setNameEmergencyContact(string $nameEmergencyContact): bool
	{
		if (empty(trim($nameEmergencyContact))) {
			return false;
		}

		$this->_nameEmergencyContact = $nameEmergencyContact;
		return true;
	}

	/**
	 * Définie le numéro de téléphone de la personne à contacter en cas d'urgence pour l'adhérent.
	 * 
	 * @param string
	 * @return bool
	 */
	public function setPhoneEmergencyContact(string $phoneEmergencyContact): bool
	{
		if (empty(trim($phoneEmergencyContact))) {
			return false;
		}

		$this->_phoneEmergencyContact = $phoneEmergencyContact;
		return true;
	}

	/**
	 * Définie le nom du médecin traitant de l'adhérent.
	 * 
	 * @param string
	 * @return bool
	 */
	public function setDoctorName(string $doctorName): bool
	{
		if (empty(trim($doctorName))) {
			return false;
		}

		$this->_doctorName = $doctorName;
		return true;
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
	 * Rempli les informations de l'adhérent à partir des données saisies dans le formulaire.
	 * 
	 * @param array $infos Infos saisie dans le formulaire.
	 * @return array Retourne la liste des erreurs si il y en a.
	 */
	public function setInformation(array $infos): array
	{
		$messages = [];

		if (!isset($infos['firstname']) || !$this->setFirstname($infos['firstname'])) {
			$messages[] = "Le prénom de l'adhérent contient des caractères non autorisé.";
		}
		
		if (!isset($infos['lastname']) || !$this->setLastname($infos['lastname'])) {
			$messages[] = "Le nom de l'adhérent contient des caractères non autorisé.";
		}
		
		if (!isset($infos['birthday']) || !$this->setBirthday($infos['birthday'])) {
			$messages[] = "La date de naissance de l'adhérent n'est pas au bon format.";
		}
		
		if (isset($infos['medicineInfo'])) {
			$this->setMedicineInfo($infos['medicineInfo']);
		}

		if (!empty($infos['passSportCode'])) {
			$this->setPassSport($infos['passSportCode']);
		}

		if (!empty($infos['socialSecurityNumber'])) {
			$this->setSocialSecurityNumber($infos['socialSecurityNumber']);
		}

		if (!empty($infos['nameEmergencyContact'])) {
			$this->setNameEmergencyContact($infos['nameEmergencyContact']);
		}

		if (!empty($infos['phoneEmergencyContact'])) {
			$this->setPhoneEmergencyContact($infos['phoneEmergencyContact']);
		}

		if (!empty($infos['doctorName'])) {
			$this->setDoctorName($infos['doctorName']);
		}
			
		return $messages;
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
					'is_sibling' => $this->_isSiblings,
					'medicine_info' => $this->_medicineInfo,
					'uniform_option' => $this->_uniformOption->value,
					'chq_buy_uniform' => $this->_chqBuyUniform,
					'chq_rent_uniform' => $this->_chqRentUniform,
					'chq_clean_uniform' => $this->_chqCleanUniform,
					'doc_ID_card' => $this->_docIdCard,
					'doc_photo' => $this->_docPhoto,
					'doc_fffa' => $this->_docFffa,
					'doc_sportmut' => $this->_docSportmut,
					'doc_medic_auth' => $this->_docMedicAuth,
					'pass_sport' => $this->_passSport,
					'inscription_date' => $this->_inscriptionDate->format('Y-m-d H:i:s'),
					'social_security_number' => $this->_socialSecurityNumber,
					'name_emergency_contact' => $this->_nameEmergencyContact,
					'phone_emergency_contact' => $this->_phoneEmergencyContact,
					'doctor_name' => $this->_doctorName
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
				[
					'id_section' => $this->_idSection,
					'id_payment' => $this->_idPayment,
					'firstname' => $this->_firstname,
					'lastname' => $this->_lastname,
					'birthday' => $this->_birthday->format('Y-m-d'),
					'is_sibling' => $this->_isSiblings,
					'medicine_info' => $this->_medicineInfo,
					'uniform_option' => $this->_uniformOption->value,
					'chq_buy_uniform' => $this->_chqBuyUniform,
					'chq_rent_uniform' => $this->_chqRentUniform,
					'chq_clean_uniform' => $this->_chqCleanUniform,
					'doc_ID_card' => $this->_docIdCard,
					'doc_photo' => $this->_docPhoto,
					'doc_fffa' => $this->_docFffa,
					'doc_sportmut' => $this->_docSportmut,
					'doc_medic_auth' => $this->_docMedicAuth,
					'pass_sport' => $this->_passSport,
					'inscription_date' => $this->_inscriptionDate->format('Y-m-d H:i:s'),
					'social_security_number' => $this->_socialSecurityNumber,
					'name_emergency_contact' => $this->_nameEmergencyContact,
					'phone_emergency_contact' => $this->_phoneEmergencyContact,
					'doctor_name' => $this->_doctorName
				]
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
						Tuteur::removeFromDatabase($tuteur->getId());
					}
				}

				// Idem pour le payment, on regarde si le paiement est utilisé par un autre adhérent.
				$rech = $database->query(
					"SELECT COUNT(*) AS count FROM adherents WHERE id_payment=:id_payment",
					['id_payment' => $this->_idPayment]
				);
				$donnees = $rech->fetch();

				if ($donnees !== false && $donnees['count'] === 0) {
					Payment::removeFromDatabase($payment->getId());
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