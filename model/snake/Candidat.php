<?php
namespace Snake;

use System\Database;

/**
 * Représente un candidat pour l'election de l'AG.
 */
class Candidat
{
	// ==== ATTRIBUTS ====
	/**
	 * @var int|null $_id ID du candidat.
	 */
	private ?int $_id;
	
	/**
	 * @var string $_firstname Prénom du candidat.
	 */
	private string $_firstname;
	
	/**
	 * @var string $_lastname Nom de famille du candidat.
	 */
	private string $_lastname;
	
	/**
	 * @var int $_nbVotes Nombre de voix pour le candidat.
	 */
	private int $_nbVotes = 0;
	
	// ==== CONSTRUCTOR ====
	public function __construct(array $dbData = null)
	{
		if ($dbData != null) {
			$this->_id = (int)$dbData['id_candidat'];
			$this->_firstname = $dbData['firstname'];
			$this->_lastname = $dbData['lastname'];
		}
	}
	
	// ==== GETTERS ====
	/**
	 * Retourne l'ID du candidat.
	 * 
	 * @return int|null
	 */
	public function getId(): int|null
	{
		return $this->_id;
	}

	/**
	 * Retourne le prénom du candidat.
	 * 
	 * @return string
	 */
	public function getFirstname(): string
	{
		return $this->_firstname;
	}

	/**
	 * Retourne le nom de famille du candidat.
	 * 
	 * @return string
	 */
	public function getLastname(): string
	{
		return $this->_lastname;
	}

	/**
	 * Retourne le nombre de voix pour le candidat.
	 * 
	 * @return int
	 */
	public function getNbVotes(): int
	{
		return $this->_nbVotes;
	}
	
	// ==== SETTERS ====
	/**
	 * Définie d'ID du candidat.
	 * 
	 * @param int $id
	 * @return void
	 */
	private function setId(int $id): void
	{
		$this->_id = $id;
	}

	/**
	 * Définie le prénom du candidat.
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
	 * Définie le nom de famille du candidat.
	 * 
	 * @param string $lastname
	 * @return bool Retourne True en cas de succès, sinon False.
	 */
	public function setLastname(string $lastname): bool
	{
		if ($lastname !== '') {
			$this->lastname = trim(ucwords(mb_strtolower($lastname)));

			return true;
		}

		return false;
	}

	/**
	 * Ajoute des votes au candidat.
	 * 
	 * @param int $nbVotes Nombre de votes à ajouter.
	 * @return void
	 */
	public function addVotes(int $nbVotes): void
	{
		$this->_nbVotes += $nbVotes;
	}

	/**
	 * Retourne un tableau représentant le candidat.
	 * 
	 * @return array
	 */
	public function toArray(): array
	{
		return [
			'id_candidat' => $this->_id,
			'firstname' => $this->_firstname,
			'lastname' => $this->_lastname,
			'name' => $this->_firstname . ' ' . $this->_lastname,
			'nbVotes' => $this->_nbVotes
		];
	}

	/**
	 * Sauvegarde les informations dans la base de données
	 * 
	 * @return bool Retourne True en cas de succès, sinon False.
	 */
	public function saveToDatabase(): bool
	{
		$database = new Database();

		if ($this->_id === null) { // Insert
			$id = $database->insert(
				'ag_candidats',
				[
					'firstname' => $this->_firstname,
					'lastname' => $this->_lastname
				]
			);

			if($id !== false) {
				$this->_id = (int)$id;

				return true;
			}
			
			return false;
		} else { // Update
			return $database->update(
				'ag_candidats', 'id_candidat', $this->_id,
				array(
					'firstname' => $this->_firstname,
					'lastname' => $this->_lastname
				)
			);
		}
	}


	// ==============================================================================
	// ==== Fonctions statiques =====================================================
	// ==============================================================================
	/**
	 * Retourne un candidat suivant son ID.
	 * 
	 * @param int $idCandidat ID du candidat.
	 * @return Candidat|false Retourne l'instance du candidat, sinon False si l'adhérent n'existe pas.
	 */
	public static function getById(int $idCandidat): Candidat|false
	{
		$database = new Database();

		$rech = $database->query(
			"SELECT * FROM ag_candidats WHERE id_candidat=:id_candidat",
			['id_candidat' => $idCandidat]
		);

		if ($rech !== null) {
			$data = $rech->fetch();

			return new Candidat($data);
		}
		
		return false;
	}

	/**
	 * Retourne la liste de tous les candidats se présentant à l'assemblé générale du club.
	 * 
	 * @return array|false Retourne la liste des candidats, sinon False en cas d'échec.
	 */
	public static function getList(): array|false
	{
		$database = new Database();

		$candidats = $database->query("SELECT * FROM ag_candidats");

		if ($candidats !== null) {
			$list = array();

			while($candidat = $candidats->fetch()) {
				$list[] = new Candidat($candidat);
			}

			return $list;
		}
		
		return false;
	}

	/**
	 * Supprime un candidat de la liste
	 * 
	 * @return bool Retourne True si le candidat à été supprimé, sinon False.
	 */
	public static function removeFromDatabase(int $idCandidat)
	{
		$database = new Database();
		
		return $database->delete('ag_candidats', 'id_candidat', $id_candidat);
	}
}