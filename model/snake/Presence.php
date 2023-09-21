<?php
namespace Snake;

use DateTime;
use System\Database;
use Snake\Adherent;

/**
 * Représente une liste de présences.
 */
class Presence
{
	// ==== ATTRIBUTS ====
	/**
	 * @var int|null $_id ID de la liste de présence.
	 */
	private ?int $_id = null;

	/**
	 * @var int|null $_idSection ID de la section associé à la présence.
	 */
	private ?int $_idSection = null;

	/**
	 * @var DateTime $_day Date ou le paiement à été réglé.
	 */
	private DateTime $_day;

	/**
	 * @var array $_list Nombre d'échéance choisi pour le paiement en plusieur fois.
	 */
	private array $_list = [];

	
	// ==== CONSTRUCTOR ====
	public function __construct(array $dbData = [])
	{
		$this->_day = new DateTime();

		if (count($dbData) !== 0) {
			$this->_id = (int)$dbData['id_presence'];
			$this->_idSection = (int)$dbData['id_section'];
			$this->_day = new DateTime($dbData['jour']);
			$this->_list = unserialize($dbData['list']);
		}
	}
	
	// ==== GETTERS ====
	/**
	 * Retourne l'ID de la présence.
	 * 
	 * @return int|null
	 */
	public function getId(): int|null
	{
		return $this->_id;
	}

	/**
	 * Retourne l'ID de la section associé à la présence.
	 * 
	 * @return int|null
	 */
	public function getIdSection(): float
	{
		return $this->_idSection;
	}

	/**
	 * Retourne le jour de la présence.
	 * 
	 * @return DateTime
	 */
	public function getDay(): DateTime
	{
		return $this->_day;
	}

	/**
	 * Retourne la liste des membres associé à la présence.
	 */
	public function getListMembers(): array
	{
		return $this->_list;
	}


	/**
	 * Définie l'ID de la présence.
	 * 
	 * @param int $id
	 * @return void
	 */
	public function setId(int $id): void
	{
		$this->_id = $id;
	}
	
	/**
	 * Définie l'ID de la section associé à la présence.
	 * 
	 * @param int $idSection
	 * @return void
	 */
	public function setIdSection(int $idSection): void
	{
		$this->_idSection = $idSection;
	}

	/**
	 * Définie le jour de la présence.
	 * 
	 * @param DateTime $day
	 * @return void
	 */
	public function setDay(DateTime $day): void
	{
		$this->_day = $day;
	}

	/**
	 * Définie la methode de paiement.
	 * 
	 * @param array $list
	 * @return void
	 */
	public function setListMembers(array $list): void
	{
		$this->_list = $list;
	}

	// == AUTRES METHODES ==
	/**
	 * Sauvegarde la présence dans la base de données
	 * 
	 * @return bool Retourne True en cas de succès, sinon False;
	 */
	public function saveToDatabase(): bool
	{
		$database = new Database();

		if ($this->_id === null) { // Insert
			$id = $database->insert(
				'presences',
				[
					'id_section' => $this->_idSection,
					'jour' => $this->_day->format('Y-m-d'),
					'list' => serialize($this->_list)
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
			'presences', 'id_presence', $this->_id,
			[
				'id_section' => $this->_idSection,
				'jour' => $this->_day->format('Y-m-d'),
				'list' => serialize($this->_list)
			]
		);
	}


	// ==============================================================================
	// ==== Fonctions statiques =====================================================
	// ==============================================================================
	/**
	 * Retourne une list de présence à l'aide de sont ID.
	 * 
	 * @param int $idPresence
	 * @return Presence|false Retourne false en cas d'échec
	 */
	public static function getById(int $idPresence): Presence|false
	{
		$database = new Database();

		$rech = $database->query(
			"SELECT * FROM presences WHERE id_presence=:id_presence",
			['id_presence' => $idPresence]
		);

		if ($rech !== null) {
			$data = $rech->fetch();

			return new Presence($data);
		}
		
		return false;
	}

	/**
	 * Retourne la liste de présences d'un jour pour une section.
	 * 
	 * @param DateTime $day
	 * @param int $idSection
	 * @return Presence|false Retourne false en cas d'échec
	 */
	public static function getByDay(DateTime $day, int $idSection): Presence|false
	{
		$database = new Database();

		$rech = $database->query(
			"SELECT * FROM presences WHERE jour=:jour AND id_section=:id_section",
			[
				'jour' => $day->format('Y-m-d'),
				'id_section' => $idSection
			]
		);

		if ($rech !== null) {
			$data = $rech->fetch();
			
			if ($data !== false) {
				return new Presence($data);;
			}
		}
		
		return false;
	}

	/**
	 * Retourne les listes de présences d'une section.
	 * 
	 * @param int $idSection
	 * @return array|false Retourne false en cas d'échec
	 */
	public static function getListBySection(int $idSection): array|false
	{
		$database = new Database();

		$rech = $database->query(
			"SELECT * FROM presences WHERE id_section=:id_section",
			['id_section' => $idSection]
		);

		if ($rech !== null) {
			$list = [];

			while ($data = $rech->fetch()) {
				$list[] = new Presence($data);
			}

			return $list;
		}
		
		return false;
	}

	/**
	 * Supprime la liste de présence de la base de données.
	 * 
	 * @param int $id
	 * @return bool
	 */
	public static function removeFromDatabase(int $idPrsence): bool
	{
		$database = new Database();
		return $database->delete('presences', 'id_prsence', $idPrsence);
		// Les réductions sont supprimées grâce à la contrainte par clè étrangère de la BDD.
	}
}