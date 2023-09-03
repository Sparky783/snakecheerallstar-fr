<?php
namespace Snake;

use System\Database;
use Snake\Horaire;

/**
 * Représente une section (équipe) du club.
 */
class Section
{
	// == ATTRIBUTS ==
	/**
	 * @var int|null $_id ID de la section.
	 */
	private ?int $_id = null;
	
	/**
	 * @var string $_name Nom de la section.
	 */
	private string $_name = '';
	
	/**
	 * @var string $_saison Saison de la section.
	 */
	private $_saison = '';
	
	/**
	 * @var int $_maxYear Année de naissance maximum accepté pour intégrer la section.
	 */
	private int $_maxYear = 0;
	
	/**
	 * @var float $_cotisationPrice Prix de la cotisation pour intégrer la section.
	 */
	private float $_cotisationPrice = 0;
	
	/**
	 * @var float $_rentUniformPrice Prix de la location de la tenue pour cette section.
	 */
	private float $_rentUniformPrice = 0;
	
	/**
	 * @var float $_cleanUniformPrice Prix du nettoyage de la tenue.
	 */
	private float $_cleanUniformPrice = 0;

	/**
	 * @var float $_buyUniformPrice Prix de l'achat de la tenue pour cette section.
	 */
	private float $_buyUniformPrice = 0;

	/**
	 * @var float $_depositUniformPrice Montant de caution de la tenue.
	 */
	private float $_depositUniformPrice = 0;
	
	/**
	 * @var int $_nbMaxMembers Nombre maximum de membre pouvant intégrer la section.
	 */
	private int $_nbMaxMembers = 0;
	
	/**
	 * @var array $_horaires Liste des horaires d'entrainement pour cette section.
	 */
	private array $_horaires = [];
	
	/**
	 * @var int $_nbMembers Nombre de membre actuel dans la section.
	 */
	private $_nbMembers = -1;

	
	// ==== CONSTRUCTOR ====
	public function __construct(array $dbData = [])
	{
		if (count($dbData) !== 0) {
			$this->_id = (int)$dbData['id_section'];
			$this->_name = $dbData['name'];
			$this->_saison = $dbData['saison'];
			$this->_maxYear = (int)$dbData['max_year'];
			$this->_cotisationPrice = (float)$dbData['cotisation_price'];
			$this->_rentUniformPrice = (float)$dbData['rent_uniform_price'];
			$this->_cleanUniformPrice = (float)$dbData['clean_uniform_price'];
			$this->_buyUniformPrice = (float)$dbData['buy_uniform_price'];
			$this->_depositUniformPrice = (float)$dbData['deposit_uniform_price'];
			$this->_nbMaxMembers = (int)$dbData['nb_max_members'];
			$this->_horaires = unserialize($dbData['horaires']);
		}
	}
	
	// ==== GETTERS ====
	/**
	 * Retourne l'ID de la section.
	 * 
	 * @return int
	 */
	public function getId(): int
	{
		return $this->_id;
	}

	/**
	 * Retourne le nom de la section.
	 * 
	 * @return int
	 */
	public function getName(): string
	{
		return $this->_name;
	}

	/**
	 * Retourne la saison de la section.
	 * 
	 * @return int
	 */
	public function getSaison(): string
	{
		return $this->_saison;
	}

	/**
	 * Retourne l'age minimum accepté pour intégrer la section.
	 * 
	 * @return int
	 */
	public function getMaxYear(): int
	{
		return $this->_maxYear;
	}

	/**
	 * Retourne le montant de la cotisation de la section.
	 * 
	 * @return float
	 */
	public function getCotisationPrice(): float
	{
		return $this->_cotisationPrice;
	}

	/**
	 * Retourne le montant de location de l'uniforme de la section.
	 * 
	 * @return float
	 */
	public function getRentUniformPrice(): float
	{
		return $this->_rentUniformPrice;
	}

	/**
	 * Retourne le montant du nettoyage de la tenue.
	 * 
	 * @return float
	 */
	public function getCleanUniformPrice(): float
	{
		return $this->_cleanUniformPrice;
	}

	/**
	 * Retourne le montant d'achat de la tenue à l'achat pour la section.
	 * 
	 * @return float
	 */
	public function getBuyUniformPrice(): float
	{
		return $this->_buyUniformPrice;
	}

	/**
	 * Retourne le montant de caution de la tenue.
	 * 
	 * @return float
	 */
	public function getDepositUniformPrice(): float
	{
		return $this->_depositUniformPrice;
	}

	/**
	 * Retourne le nombre maximum de membre accepté pour la section.
	 * 
	 * @return int
	 */
	public function getNbMaxMembers(): int
	{
		return $this->_nbMaxMembers;
	}

	/**
	 * Retourne les horaires d'entrainement de la section.
	 * 
	 * @return array
	 */
	public function getHoraires(): array
	{
		return $this->_horaires;
	}
	
	/**
	 * Retourne le nombre actuel de membre dans la section.
	 * 
	 * @return int|false
	 */
	public function getNbMembers(): int|false
	{
		if ($this->_id !== null) {
			$list = Adherent::getListBySection($this->_id);

			if($list !== false) {
				return count($list);
			}
		}

		return false;
	}
	
	// ==== SETTERS ====
	/**
	 * Définie le nom de la section.
	 * 
	 * @param string $name
	 * @return void
	 */
	public function setName(string $name): void
	{
		$this->_name = $name;
	}

	/**
	 * Définie la saison de la section.
	 * 
	 * @param string $saison
	 * @return void
	 */
	public function setSaison(string $saison): void
	{
		$this->_saison = $saison;
	}

	/**
	 * Définie l'age minimum pour intégrer la section.
	 * 
	 * @param int $maxYear
	 * @return void
	 */
	public function setMaxYear(int $maxYear): void
	{
		$this->_maxYear = $maxYear;
	}

	/**
	 * Définie le prix de la cotisation pour intégrer la section.
	 * 
	 * @param float $price
	 * @return void
	 */
	public function setCotisationPrice(float $price): void
	{
		$this->_cotisationPrice = $price;
	}

	/**
	 * Définie le montant de location de l'uniforme de la section.
	 * 
	 * @param float $price
	 * @return void
	 */
	public function setRentUniformPrice(float $price): void
	{
		$this->_rentUniformPrice = $price;
	}

	/**
	 * Définie le prix de nettoyage de l'uniforme pour la section.
	 * 
	 * @param float $price
	 * @return void
	 */
	public function setCleanUniformPrice(float $price): void
	{
		$this->_cleanUniformPrice = $price;
	}

	/**
	 * Définie le prix de l'uniforme à l'achat pour la section.
	 * 
	 * @param float $price
	 * @return void
	 */
	public function setBuyUniformPrice(float $price): void
	{
		$this->_buyUniformPrice = $price;
	}

	/**
	 * Définie le montant de caution de l'uniforme pour la section.
	 * 
	 * @param float $price
	 * @return void
	 */
	public function setDepositUniformPrice(float $price): void
	{
		$this->_depositUniformPrice = $price;
	}

	/**
	 * Définie le nombre maximum de memebre pouvant intégrer la section.
	 * 
	 * @param int $nbMaxMembers
	 * @return void
	 */
	public function setNbMaxMembers(int $nbMaxMembers): void
	{
		$this->_nbMaxMembers = $nbMaxMembers;
	}
	
	// ==== AUTRES METHODES ====
	/**
	 * Ajoute une horaire d'entrainement à la section.
	 * 
	 * @param Horaire $horaire Horaire à ajouter.
	 * @return void
	 */
	public function addHoraire(Horaire $horaire): void
	{
		$this->_horaires[] = $horaire;
	}

	/**
	 * Supprime toute les horaires d'entrainement de la section.
	 * 
	 * @return void
	 */
	public function clearHoraire(): void
	{
		$this->_horaires = [];
	}

	/**
	 * Retourne les donnnées de la section sous forme de tableau.
	 */
	public function toArray(): array
	{
		return [
			'idSection' => $this->getId(),
			'name' => $this->getName(),
			'maxYear' => $this->getMaxYear(),
			'cotisationPrice' => $this->getCotisationPrice(),
			'rentUniformPrice' => $this->getRentUniformPrice(),
			'cleanUniformPrice' => $this->getCleanUniformPrice(),
			'buyUniformPrice' => $this->getBuyUniformPrice(),
			'depositUniformPrice' => $this->getDepositUniformPrice(),
			'maxMembers' => $this->getNbMaxMembers()
		];
	}

	/**
	 * Sauvegarde les données de la section dans la base de données
	 */
	public function saveToDatabase()
	{
		$database = new Database();

		if ($this->_id === null) { // Insert
			$id = $database->insert(
				'sections',
				[
					'name' => $this->_name,
					'saison' => $this->_saison,
					'max_year' => $this->_maxYear,
					'cotisation_price' => $this->_cotisationPrice,
					'rent_uniform_price' => $this->_rentUniformPrice,
					'clean_uniform_price' => $this->_cleanUniformPrice,
					'buy_uniform_price' => $this->_buyUniformPrice,
					'deposit_uniform_price' => $this->_depositUniformPrice,
					'nb_max_members' => $this->_nbMaxMembers,
					'horaires' => serialize($this->_horaires)
				]
			);

			if ($id !== false) {
				$this->_id = $id;
				return true;
			}
			
			return false;
		} else { // Update
			$result = $database->update(
				'sections', 'id_section', $this->_id,
				[
					'name' => $this->_name,
					'saison' => $this->_saison,
					'max_year' => $this->_maxYear,
					'cotisation_price' => $this->_cotisationPrice,
					'rent_uniform_price' => $this->_rentUniformPrice,
					'clean_uniform_price' => $this->_cleanUniformPrice,
					'buy_uniform_price' => $this->_buyUniformPrice,
					'deposit_uniform_price' => $this->_depositUniformPrice,
					'nb_max_members' => $this->_nbMaxMembers,
					'horaires' => serialize($this->_horaires)
				]
			);

			return $result;
		}
	}

	// ==============================================================================
	// ==== Fonctions statiques =====================================================
	// ==============================================================================
	/**
	 * Retourne une section suivant son ID.
	 * 
	 * @param int $idSection
	 * @return Section|false Retourne False en cas d'échec.
	 */
	public static function getById(int $idSection): Section|false
	{
		$database = new Database();

		$rech = $database->query(
			"SELECT * FROM sections WHERE id_section=:id_section",
			['id_section' => $idSection]
		);

		if ($rech != null) {
			$data = $rech->fetch();

			return new Section($data);
		}
		
		return false;
	}

	/**
	 * Retourne la liste de toutes les sections du club pour une saison donnée. Par défaut c'est la saison en cours qui est sélectionnée.
	 * 
	 * @param string $saison
	 * @return array|false Retourne False en cas d'échec.
	 */
	public static function getList(string $saison = ''): array|false
	{
		if ($saison === '') {
			$saison = SnakeTools::getCurrentSaison();
		}

		$database = new Database();

		$sections = $database->query(
			"SELECT * FROM sections WHERE saison=:saison ORDER BY max_year",
			['saison' => $saison]
		);

		if ($sections !== null) {
			$list = [];

			while($data = $sections->fetch()) {
				$list[] = new Section($data);
			}

			return $list;
		}
		
		return false;
	}

	/**
	 * Supprime une section de la base de données.
	 * 
	 * @param int $idSection ID de la section à supprimer.
	 * @return bool Retourne True si la section à bien été supprimé, sinon False.
	 */
	public static function removeFromDatabase(int $idSection): bool
	{
		$database = new Database();
		return $database->delete('sections', 'id_section', $idSection);
	}
}