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
	 * @var int $_minAge Age minimun accepté pour intégrer la section.
	 */
	private int $_minAge = 0;
	
	/**
	 * @var float $_priceCotisation Prix de la cotisation pour intégrer la section.
	 */
	private float $_priceCotisation = 0;
	
	/**
	 * @var float $_priceRentUniform Prix de la location de la tenue pour cette section.
	 */
	private float $_priceRentUniform = 0;
	
	/**
	 * @var float $_priceUniform Prix de l'achat de la tenue pour cette section.
	 */
	private float $_priceUniform = 0;

	/**
	 * @var float $_priceCleanUniform Prix du nettoyage de la tenue.
	 */
	private float $_priceCleanUniform = 0;
	
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
	private $_nbMembers;

	
	// ==== CONSTRUCTOR ====
	public function __construct(array $dbData = [])
	{
		if (count($dbData) !== 0) {
			$this->_id = (int)$dbData['id_section'];
			$this->_name = $dbData['name'];
			$this->_saison = $dbData['saison'];
			$this->_minAge = (int)$dbData['min_age'];
			$this->_priceCotisation = (float)$dbData['price_cotisation'];
			$this->_priceRentUniform = (float)$dbData['price_rent_uniform'];
			$this->_priceCleanUniform = (float)$dbData['price_clean_uniform'];
			$this->_priceUniform = (float)$dbData['price_buy_uniform'];
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
	public function getMinAge(): int
	{
		return $this->_minAge;
	}

	/**
	 * Retourne le montant de la cotisation de la section.
	 * 
	 * @return float
	 */
	public function getPriceCotisation(): float
	{
		return $this->_priceCotisation;
	}

	/**
	 * Retourne le montant de location de l'uniforme de la section.
	 * 
	 * @return float
	 */
	public function getPriceRentUniform(): float
	{
		return $this->_priceRentUniform;
	}

	/**
	 * Retourne le montant d'achat de la tenue pour la section.
	 * 
	 * @return float
	 */
	public function getPriceUniform(): float
	{
		return $this->_priceUniform;
	}

	/**
	 * Retourne le montant du nettoyage de la tenue.
	 * 
	 * @return float
	 */
	public function getPriceCleanUniform(): float
	{
		return $this->_priceCleanUniform;
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
	 * @return int
	 */
	public function getNbMembers(): int
	{
		return $this->_nbMembers;
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
	 * @param int $minAge
	 * @return void
	 */
	public function setMinAge(int $minAge): void
	{
		$this->_minAge = intval($minAge);
	}

	/**
	 * Définie le prix de la cotisation pour intégrer la section.
	 * 
	 * @param float $price
	 * @return void
	 */
	public function setPriceCotisation(float $price): void
	{
		$this->_priceCotisation = intval($price);
	}

	/**
	 * Définie le prix de l'uniforme pour la section.
	 * 
	 * @param float $price
	 * @return void
	 */
	public function setPriceUniform(float $price): void
	{
		$this->_priceUniform = intval($price);
	}

	/**
	 * Définie le nombre maximum de memebre pouvant intégrer la section.
	 * 
	 * @param int $nbMaxMembers
	 * @return void
	 */
	public function setNbMaxMembers(int $nbMaxMembers): void
	{
		$this->nbMaxMembers = intval($nbMaxMembers);
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
	 * Incrémente le nombre de membre présent dans la section.
	 *
	 * @return void
	 */
	public function addMember(): void
	{
		$this->_nbMembers ++;
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
				array(
					'name' => $this->_name,
					'saison' => $this->_saison,
					'min_age' => $this->_minAge,
					'price_cotisation' => $this->_priceCotisation,
					'price_rent_uniform' => $this->_priceRentUniform,
					'price_clean_uniform' => $this->_priceCleanUniform,
					'price_buy_uniform' => $this->_priceUniform,
					'nb_max_members' => $this->_nbMaxMembers,
					'horaires' => serialize($this->_horaires)
				)
			);

			if ($id !== false) {
				$this->id = intval($id);
				return true;
			}
			
			return false;
		} else { // Update
			$result = $database->update(
				'sections', 'id_section', $this->_id,
				array(
					'name' => $this->_name,
					'saison' => $this->_saison,
					'min_age' => $this->_minAge,
					'price_cotisation' => $this->_priceCotisation,
					'price_rent_uniform' => $this->_priceRentUniform,
					'price_clean_uniform' => $this->_priceCleanUniform,
					'price_buy_uniform' => $this->_priceUniform,
					'nb_max_members' => $this->_nbMaxMembers,
					'horaires' => serialize($this->_horaires)
				)
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
			"SELECT * FROM sections WHERE saison=:saison ORDER BY min_age",
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