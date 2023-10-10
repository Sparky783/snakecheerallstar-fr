<?php
namespace Snake;

use System\Database;
use Snake\Adherent;

/**
 * Représente un tuteur d'un adhérent (parent, représentant légal, etc ...).
 * Les adhérents majeurs on pour tuteur eux même.
 */
class Tuteur
{
	// == ATTRIBUTS ==
	/**
	 * @var int|null $_id ID du tuteur.
	 */
	private ?int $_id = null;
	
	/**
	 * @var string $_firstname Prénom du tuteur.
	 */
	private string $_firstname = '';
	
	/**
	 * @var string $_lastname Nom de famille du tuteur.
	 */
	private string $_lastname = '';
	
	/**
	 * @var string $_status Status social du tuteur (Prèe, Mère, Représentant légal).
	 */
	private string $_status = '';
	
	/**
	 * @var string $_email E-mail du tuteur.
	 */
	private string $_email = '';
	
	/**
	 * @var string $_phone Numéro de téléphone du tuteur.
	 */
	private string $_phone = '';

	/**
	 * @var array $_adherents Liste des adhérents associés au tuteur.
	 */
	private array $_adherents = [];
	

	// ==== CONSTRUCTOR ==== 
	public function __construct(array $dbData = [])
	{
		if (count($dbData) !== 0) {
			$this->_id = (int)$dbData['id_tuteur'];
			$this->_firstname = $dbData['firstname'];
			$this->_lastname = $dbData['lastname'];
			$this->_status = $dbData['status'];
			$this->_email = $dbData['email'];
			$this->_phone = $dbData['phone'];
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
			'id_tuteur' => $this->_id,
			'firstname' => $this->_firstname,
			'lastname' => $this->_lastname,
			'status' => $this->_status,
			'email' => $this->_email,
			'phone' => $this->_phone
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
        $this->_id = $data['id_tuteur'];
		$this->_firstname = $data['firstname'];
		$this->_lastname = $data['lastname'];
		$this->_status = $data['status'];
		$this->_email = $data['email'];
		$this->_phone = $data['phone'];
	}
	
	// == METHODES GETTERS ==
	/**
	 * Retourne l'ID du tuteur.
	 * 
	 * @return int|null
	 */
	public function getId(): int|null
	{
		return $this->_id;
	}

	/**
	 * Retourne le prénom du tuteur.
	 * 
	 * @return string
	 */
	public function getFirstname(): string
	{
		return $this->_firstname;
	}

	/**
	 * Retourne le nom de famille du tuteur.
	 * 
	 * @return string
	 */
	public function getLastname(): string
	{
		return $this->_lastname;
	}

	/**
	 * Retourne le status social du tuteur (Adhérent, Père, Mère, Responsable légal, etc ...).
	 * 
	 * @return string
	 */
	public function getStatus(): string
	{
		return $this->_status;
	}

	/**
	 * Retourne l'E-mail'e du tuteur.
	 * 
	 * @return string
	 */
	public function getEmail(): string
	{
		return $this->_email;
	}

	/**
	 * Retourne le numéro de téléphone du tuteur.
	 * 
	 * @return string
	 */
	public function getPhone(): string
	{
		return $this->_phone;
	}

	/**
	 * Retourne la liste des adhérents liés au tuteur. Si besoin, charge les données depuis la BDD.
	 * 
	 * @return Adherent[]
	 */
	public function getAdherents(): array
	{
		if ($this->_id !== null && count($this->_adherents) === 0) {
			$database = new Database();
			$adherents = $database->query(
				"SELECT * FROM adherent_tuteur JOIN adherents ON adherent_tuteur.id_adherent = adherents.id_adherent WHERE id_tuteur=:id_tuteur",
				['id_tuteur' => $this->_id]
			);

			if ($adherents != null) {
				while($adherent = $adherents->fetch()) {
					$this->_adherents[] = new Adherent($adherent);
				}
			}
		}

		return $this->_adherents;
	}
	
	// == METHODES SETTERS ==
	/**
	 * Définie l'ID du tuteur
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
	 * Définie le prénom du tuteur
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
	 * Définie le nom de famille du tuteur
	 * 
	 * @param string $lastname
	 * @return bool Retourne True en cas de succès, sinon False.
	 */
	public function setLastname(string $lastname): bool
	{
		if($lastname !== '') {
			$this->_lastname = trim(ucwords(mb_strtolower($lastname)));

			return true;
		}

		return false;
	}

	/**
	 * Définie le status du tuteur
	 * 
	 * @param string $status
	 * @return bool Retourne True en cas de succès, sinon False.
	 */
	public function setStatus(string $status): bool
	{
		if ($status !== '') {
			$this->_status = mb_strtolower($status);

			return true;
		}

		return false;
	}
	
	/**
	 * Définie l'E-mail du tuteur
	 * 
	 * @param string $email
	 * @return bool Retourne True en cas de succès, sinon False.
	 */
	public function setEmail(string $email): bool
	{
		$email = trim($email);

		if (preg_match("/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/i", $email)) {
			$this->_email = mb_strtolower($email);

			return true;
		}

		return false;
	}
	
	/**
	 * Définie le numéro de téléphone du tuteur
	 * 
	 * @param string $phone
	 * @return bool Retourne True en cas de succès, sinon False.
	 */
	public function setPhone(string $phone): bool
	{
		$phone = trim($phone);

		if (preg_match('/^(0|\+33)[1-9]([-. ]?[0-9]{2}){4}$/i', $phone)) {
			$phone = str_replace("+33", "0", $phone);
			$phone = str_replace(array(".", "-", " "), "", $phone);
			$this->_phone = $phone;

			return true;
		}

		return false;
	}
	
	/**
	 * Rempli les informations du tuteur avec les données saisies dans le formulaire.
	 * 
	 * @param array $infos Infos saisie dans le formulaire.
	 * @return array Retourne la liste des erreurs si il y en a.
	 */
	public function setInformation(array $infos): array
	{
		$messages = [];
		
		if (isset($infos['id_tuteur'])) {
			$this->setId((int)$infos['id_tuteur']);
		}
		
		if (!isset($infos['firstname']) || !$this->setFirstname($infos['firstname'])) {
			$messages[] = "Le prénom du tuteur contient des caractères non autorisé.";
		}
		
		if (!isset($infos['lastname']) || !$this->setLastname($infos['lastname'])) {
			$messages[] = "Le nom du tuteur contient des caractères non autorisé.";
		}
		
		if (!isset($infos['status']) || !$this->setStatus($infos['status'])) {
			$messages[] = "Le statut du tuteur doit être défini.";
		}
		
		if (!isset($infos['email']) || !$this->setEmail($infos['email'])) {
			$messages[] = "L'E-mail du tuteur n'est pas au bon format.";
		}
		
		if (!isset($infos['phone']) || !$this->setPhone($infos['phone'])) {
			$messages[] = "Le numéro de téléphone du tuteur n'est pas au bon format.";
		}
		
		return $messages;
	}

	/**
	 * Ajoute un adhérent à ceux géré par ce tuteur.
	 * 
	 * @param Adherent $adherent Adhérent à associer.
	 * @return void
	 */
	public function addAdherent(Adherent $adherent): void
	{
		$this->_adherents[] = $adherent;
	}

	/**
	 * Sauvegarde les informations du tuteur dans la base de données.
	 * 
	 * @return bool Retourne True en cas de succès, sinon false.
	 */
	public function saveToDatabase(): bool
	{
		$database = new Database();
		$result = false;

		if ($this->_id == null) { // Insert
			$id = $database->insert(
				'tuteurs',
				[
					'firstname' => $this->_firstname,
					'lastname' => $this->_lastname,
					'status' => $this->_status,
					'email' => $this->_email,
					'phone' => $this->_phone
				]
			);

			if($id !== false) {
				$this->_id = (int)$id;
				$result = true;
			}
		} else { // Update
			$result = $database->update(
				'tuteurs', 'id_tuteur', $this->_id,
				[
					'firstname' => $this->_firstname,
					'lastname' => $this->_lastname,
					'status' => $this->_status,
					'email' => $this->_email,
					'phone' => $this->_phone
				]
			);
		}

		// Met à jour les liens avec les adhérents.
		if ($this->_id !== null && count($this->_adherents) !== 0) {
			foreach ($this->_adherents as $adherent) {
				if ($adherent->getId() !== null) {
					$database->insert(
						'adherent_tuteur',
						[
							'id_adherent' => $adherent->getId(),
							'id_tuteur' => $this->_id
						]
					);
				} else {
					$result = false;
					break;
				}
			}
		}

		return $result;
	}


	// ==============================================================================
	// ==== Fonctions statiques =====================================================
	// ==============================================================================
	/**
	 * Retourne un tuteur suivant son ID.
	 * 
	 * @return Tuteur|false Retourne False en cas d'échec.
	 */
	public static function getById(int $id_tuteur): Tuteur|false
	{
		$database = new Database();

		$rech = $database->query(
			"SELECT * FROM tuteurs WHERE id_tuteur=:id_tuteur",
			['id_tuteur' => $id_tuteur]
		);

		if($rech !== null) {
			$data = $rech->fetch();

			return new Tuteur($data);
		}
		
		return false;
	}

	/**
	 * Retourne la liste de tous les tuteurs du club. Par défaut ceux de la saison en cours.
	 * 
	 * @return array|false Retourne False en cas d'échec.
	 */
	public static function getList($saison = null): array|false
	{
		if ($saison === null) {
			$saison = SnakeTools::getCurrentSaison();
		}

		$database = new Database();

		$tuteurs = $database->query(
			"SELECT tuteurs.* FROM adherent_tuteur
			JOIN adherents ON adherent_tuteur.id_adherent = adherents.id_adherent
			JOIN tuteurs ON adherent_tuteur.id_tuteur = tuteurs.id_tuteur
			JOIN sections ON sections.id_section = adherents.id_section
			WHERE sections.saison=:saison",
			[
				'saison' => $saison
			]
		);
		
		if ($tuteurs !== null) {
			$list = [];

			while($tuteur = $tuteurs->fetch()) {
				$list[] = new Tuteur($tuteur);
			}
			
			return $list;
		}
		
		return false;
	}

	/**
	 * Retourne la liste de tous les tuteurs de la section souhaité. Par défaut ceux de la saison en cours.
	 * 
	 * @param int $id_section ID de la section à récupérer.
	 * @return array|false Retourne False en cas d'échec.
	 */
	public static function getListBySection(int $id_section): array|false
	{
		$database = new Database();

		$tuteurs = $database->query(
			"SELECT tuteurs.* FROM adherent_tuteur
			JOIN adherents ON adherent_tuteur.id_adherent = adherents.id_adherent
			JOIN tuteurs ON adherent_tuteur.id_tuteur = tuteurs.id_tuteur
			WHERE id_section=:id_section",
			['id_section' => $id_section]
		);

		if ($tuteurs !== null) {
			$list = [];

			while ($tuteur = $tuteurs->fetch()) {
				$list[] = new Tuteur($tuteur);
			}

			return $list;
		}
		
		return false;
	}

	/**
	 * Supprime un tuteur de la base de données.
	 * 
	 * @param int $id ID du tuteur à supprimer.
	 * @return bool Retourne True en cas de succès, sinon False.
	 */
	public static function removeFromDatabase(int $id): bool
	{
		$database = new Database();
		return $database->delete('tuteurs', 'id_tuteur', $id);
	}
}