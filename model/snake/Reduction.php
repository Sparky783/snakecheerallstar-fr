<?php
namespace Snake;

use System\Database;
use Snake\EReductionType;

/**
 * Représente une réduction pour un paiement.
 */
class Reduction
{
	// ==== ATTRIBUTS ====
	/**
	 * @var int|null $_id ID de la réduction.
	 */
	private ?int $_id = null;
	
	/**
	 * @var int|null $_idPayment ID du paiement associé à la réduction.
	 */
	private ?int $_idPayment = null;
	
	/**
	 * @var EReductionType $_type Type de réduction (montant, pourçantage).
	 */
	private EReductionType $_type = EReductionType::None;
	
	/**
	 * @var float $_value $_id Valeur à appliquer pour la réduction.
	 */
	private float $_value = 0;
	
	/**
	 * @var string $_sujet Objet de la réduction.
	 */
	private string $_sujet = '';
	

	// ==== CONSTRUCTOR ====
	public function __construct(array $dbData = [])
	{
		if (count($dbData) !== 0) {
			$this->_id = (int)$dbData['id_reduction'];
			$this->_idPayment = (int)$dbData['id_payment'];
			$this->_type = EReductionType::tryFrom($dbData['type']) ?? EReductionType::None;
			$this->_value = (float)$dbData['value'];
			$this->_sujet = $dbData['sujet'];
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
			'id_reduction' => $this->_id,
			'id_payment' => $this->_idPayment,
			'type' => $this->_type,
			'value' => $this->_value,
			'sujet' => $this->_sujet
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
        $this->_id = $data['id_reduction'];
		$this->_idPayment = $data['id_payment'];
		$this->_type = $data['type'];
		$this->_value = $data['value'];
		$this->_sujet = $data['sujet'];
	}
	
	// ==== GETTERS ====
	/**
	 * Retourne l'ID de la réduction.
	 * 
	 * @return int|null
	 */
	public function getId(): int|null
	{
		return $this->_id;
	}

	/**
	 * Retourne l'ID du paiement associé à la réduction.
	 * 
	 * @return int|null
	 */
	public function getIdPayment(): int|null
	{
		return $this->_idPayment;
	}

	/**
	 * Retourne le type de réduction (montant, pourçantage).
	 * 
	 * @return EReductionType
	 */
	public function getType(): EReductionType
	{
		return $this->_type;
	}

	/**
	 * Valeur de la réduction à appliquer
	 * 
	 * @return float
	 */
	public function getValue(): float
	{
		return $this->_value;
	}

	/**
	 * Retourne l'objet de la réduction
	 * 
	 * @return string
	 */
	public function getSujet(): string
	{
		return $this->_sujet;
	}
	
	// ==== SETTERS ====
	/**
	 * Définie l'ID de la réduction.
	 * 
	 * @param int $id ID de la réduction.
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
	 * Définie l'ID du paiement associé à la réduction.
	 * 
	 * @param int $idPayment ID du paiement associé à la réduction.
	 * @return void
	 */
	public function setIdPayment(int $idPayment): void
	{
		$this->_idPayment = $idPayment;
	}
	
	/**
	 * Définie le type de la réduction (montant, pourçantage).
	 * 
	 * @param EReductionType $type Type de la réduction.
	 * @return void
	 */
	public function setType(EReductionType $type): void
	{
		$this->_type = $type;
	}

	/**
	 * Définie la valeur à appliquer pour cette réduction.
	 * 
	 * @param float $value Valeur de la réduction.
	 * @return void
	 */
	public function setValue(float $value): void
	{
		$this->_value = $value;
	}

	/**
	 * Définie l'objet de la réduction.
	 * 
	 * @param string $sujet Objet de la réduction.
	 * @return void
	 */
	public function setSujet(string $sujet): void
	{
		$this->_sujet = $sujet;
	}

	// ==== AUTRES METHODES ====
	/**
	 * Retourne les données de la réduction sous forme de tableau.
	 */
	public function toArray(): array
	{
		return [
			'id_reduction' => $this->_id,
			'id_payment' => $this->_idPayment,
			'type' => $this->_type->value,
			'value' => $this->_value,
			'sujet' => $this->_sujet
		];
	}

	public function saveToDatabase()
	{
		$database = new Database();

		if ($this->_idPayment != null) {
			if ($this->_id == null) { // Insert
				$id = $database->insert(
					'reductions',
					[
						'id_payment' => $this->_idPayment,
						'type' => $this->_type->value,
						'value' => $this->_value,
						'sujet' => $this->_sujet
					]
				);

				if ($id !== false) {
					$this->_id = (int)$id;
					return true;
				}
			} else { // Update
				return $database->update(
					'reductions', 'id_reduction', $this->_id,
					[
						'id_payment' => $this->_idPayment,
						'type' => $this->_type->value,
						'value' => $this->_value,
						'sujet' => $this->_sujet
					]
				);
			}
		}
	}


	// ==============================================================================
	// ==== Fonctions statiques =====================================================
	// ==============================================================================
	/**
	 * Retourne une réduction suivant son ID.
	 * 
	 * @param int
	 * @return Reduction|false Retourne False en cas d'échec.
	 */
	public static function getById(int $idReduction): Reduction|false
	{
		$database = new Database();

		$rech = $database->query(
			"SELECT * FROM reductions WHERE id_reduction=:id_reduction",
			['id_reduction' => $idReduction]
		);

		if ($rech !== null) {
			$data = $rech->fetch();

			return new Reduction($data);
		}
		
		return false;
	}

	/**
	 * Retourne toutes les réductions d'un paiement.
	 * 
	 * @param int $idPayment ID du paiement ou les réduction doivent être récupérées.
	 * @return array|false Retourne False en cas d'échec.
	 */
	public static function getListByIdPayment(int $idPayment): array|false
	{
		$database = new Database();
		$reductions = $database->Query(
			"SELECT * FROM reductions WHERE id_payment=:id_payment",
			['id_payment' => $idPayment]
		);

		if ($reductions !== null) {
			$list = [];

			while ($reduction = $reductions->fetch()) {
				$list[] = new Reduction($reduction);
			}

			return $list;
		}
		
		return false;
	}
}