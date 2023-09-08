<?php
namespace Snake;

use DateTime;
use System\Database;
use Snake\Adherent;
use Snake\Reduction;
use Snake\EPaymentType;

/**
 * Représente un paiement.
 * Contient les réductions et les montants associés au paiement.
 */
class Payment
{
	// ==== ATTRIBUTS ====
	/**
	 * @var int|null $_id ID du paiement.
	 */
	private ?int $_id = null;

	/**
	 * @var float $_basePrice Prix de base du paiement. Ce montant peux évoluer avec les réductions.
	 */
	private float $_basePrice = 0;

	/**
	 * @var float $_fixedPrice Prix fixe du paiement. Les réductions n'impact pas ce prix.
	 */
	private float $_fixedPrice = 0;

	/**
	 * @var EPaymentType|null $_method Méthode de paiement utilisé pour régler la facture.
	 */
	private ?EPaymentType $_method = null;

	/**
	 * @var DateTime|null $_paymentDate Date ou le paiement à été réglé.
	 */
	private ?DateTime $_paymentDate = null;

	/**
	 * @var int $_nbDeadlines Nombre d'échéance choisi pour le paiement en plusieur fois.
	 */
	private int $_nbDeadlines = 0;

	/**
	 * @var bool $_isDone Informe si le paiement à été réglé en totalité ou non.
	 */
	private bool $_isDone = false;
	
	/**
	 * @var Reduction[] $_reductions Liste des réductions à appliquer pour ce paiement.
	 */
	private array $_reductions = [];
	
	/**
	 * @var Adherent[] $_adherents Liste des adhérents lié au paiement.
	 */
	private array $_adherents = [];

	
	// ==== CONSTRUCTOR ====
	public function __construct(array $dbData = [])
	{
		$this->_paymentDate = new DateTime();

		if (count($dbData) !== 0) {
			$this->_id = (int)$dbData['id_payment'];
			$this->_basePrice = (int)$dbData['base_price'];
			$this->_fixedPrice = (int)$dbData['fixed_price'];
			$this->_paymentDate = new DateTime($dbData['date_payment']);
			$this->_nbDeadlines = (int)$dbData['nb_deadlines'];
			$this->_isDone = (bool)$dbData['is_done'];
			$this->_method = EPaymentType::tryFrom($dbData['method']);
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
			'id_payment' => $this->_id,
			'base_price' => $this->_basePrice,
			'fixed_price' => $this->_fixedPrice,
			'method' => $this->_method,
			'date_payment' => serialize($this->_paymentDate),
			'nb_deadlines' => $this->_nbDeadlines,
			'is_done' => $this->_isDone,
			'reductions' => []
		];

		foreach ($this->_reductions as $reduction) {
			$data['reductions'][] = serialize($reduction);
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
        $this->_id = $data['id_payment'];
		$this->_basePrice = $data['base_price'];
		$this->_fixedPrice = $data['fixed_price'];
		$this->_method = $data['method'];
		$this->_paymentDate = unserialize($data['date_payment']);
		$this->_nbDeadlines = $data['nb_deadlines'];
		$this->_isDone = $data['is_done'];
		$this->_reductions = [];

		foreach ($data['reductions'] as $reduciton) {
			$this->_reductions[] = unserialize($reduciton);
		}
	}
	
	// ==== GETTERS ====
	/**
	 * Retourne l'ID du paiement.
	 * 
	 * @return int|null
	 */
	public function getId(): int|null
	{
		return $this->_id;
	}

	/**
	 * Retourne le prix de base du paiement. Ce montant peux être impacté par les réductions.
	 * 
	 * @return float
	 */
	public function getBasePrice(): float
	{
		return $this->_basePrice;
	}

	/**
	 * Retourne le prix fixe du paiement. Ce montantn NE peux PAS être impacté par les réductions.
	 * 
	 * @return float
	 */
	public function getFixedPrice(): float
	{
		return $this->_fixedPrice;
	}

	/**
	 * Retourne la méthode de paiement choisie pour régler ce paiement.
	 */
	public function getMethod(): EPaymentType
	{
		return $this->_method;
	}

	/**
	 * Retourne la date et l'heure à laquelle le paiement à été réglé.
	 * Pour un paiement en plusieur fois, retourne la date et l'heure du dernier paiement.
	 * 
	 * @return DateTime
	 */
	public function getDate(): DateTime
	{
		return $this->_paymentDate;
	}

	/**
	 * Retourne le nombre d'échéance choisies pour un paiement en plusieur fois.
	 * Si c'est un paiement unique, retourne 0.
	 * 
	 * @return int
	 */
	public function getNbDeadlines(): int 
	{
		if($this->_method !== EPaymentType::Cheque) {
			return 0;
		}

		return $this->_nbDeadlines;
	}

	/**
	 * Retourne la liste des montants de chaque échéance pour un paiement en plusieurs fois.
	 * Dans les autres cas de type de paiement, retourne False.
	 * 
	 * @return array|false
	 */
	public function getDeadlines(): array|false
	{
		if($this->_method !== EPaymentType::Cheque) {
			return false;
		}

		return SnakeTools::makeDeadlines($this->getFinalAmount(), $this->getNbDeadlines());
	}

	/**
	 * Retourne True si la totalité du paiement à été réglé, sinon False.
	 * 
	 * @return bool
	 */
	public function isDone(): bool
	{
		return $this->_isDone;
	}

	/**
	 * Retourne la liste des réductions associés au paiement. Si besoin, charge les données depuis la BDD.
	 * 
	 * @return Reduction[]
	 */
	public function getReductions(): array
	{
		if ($this->_id !== null && count($this->_reductions) == 0) {
			$list = Reduction::getListByIdPayment($this->_id);
			
			if ($list !== false) {
				$this->_reductions = $list;
			}
		}

		return $this->_reductions;
	}

	/**
	 * Retourne la liste des adhérents liés au paiement. Si besoin, charge les données depuis la BDD.
	 * 
	 * @return Adherent[]
	 */
	public function getAdherents(): array
	{
		if ($this->_id !== null && count($this->_adherents) === 0) {
			$list = Adherent::getListByIdPayment($this->_id);
			
			if ($list !== false) {
				$this->_adherents = $list;
			}
		}

		return $this->_adherents;
	}

	/**
	 * Calcule le prix de base en appliquant les réductions. Le prix fixe n'est pas inclus.
	 * 
	 * @return float
	 */
	public function getBasePriceWithReductions(): float
	{
		$this->getReductions(); // Charge les réductions si elle n'ont pas été chargées.

		$montant = $this->_basePrice;
		
		// Toujours appliquer les "Pourcentage" avant ...
		foreach ($this->_reductions as $reduction) {
			if ($reduction->getType() === EReductionType::Percentage) {
				$montant = round($montant * (1 - ($reduction->getValue() / 100)));
			}
		}
		
		// ... puis appliquer les "Montant".
		foreach ($this->_reductions as $reduction) {
			if ($reduction->getType() === EReductionType::Amount) {
				$montant -= $reduction->getValue();
			}
		}
		
		return $montant;
	}
	
	/**
	 * Retourne le montant final. Prix de base avec les réduction et le prix fixe.
	 * 
	 * @return float
	 */
	public function getFinalAmount(): float
	{
		return $this->getBasePriceWithReductions() + $this->_fixedPrice;
	}

	/**
	 * Définie l'ID du paiement.
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
	 * Définie le prix de base sur lequel sera appliqué les réductions
	 * 
	 * @param float $basePrice
	 * @return void
	 */
	public function setBasePrice(float $basePrice): void
	{
		$this->_basePrice = $basePrice;
	}

	/**
	 * Définie le prix fixe (Les réductions ne sont pas appliqué sur ce montant)
	 * 
	 * @param float $fixedPrice
	 * @return void
	 */
	public function setFixedPrice(float $fixedPrice): void
	{
		$this->_fixedPrice = $fixedPrice;
	}

	/**
	 * Définie la methode de paiement.
	 * 
	 * @param EPaymentType $method
	 * @return void
	 */
	public function setMethod(EPaymentType $method): void
	{
		$this->_method = $method;
	}

	/**
	 * Définie le nombre d'échéance à appliquer pour ce paiement.
	 * 
	 * @param int $nbDeadlines
	 * @return void
	 */
	public function setNbDeadlines(int $nbDeadlines): void
	{
		$this->_nbDeadlines = $nbDeadlines;
	}

	/**
	 * Définie si le paiement à été payé en totalité ou non.
	 * 
	 * @param bool $isDone
	 * @return void
	 */
	public function setIsDone(bool $isDone): void
	{
		$this->_isDone = $isDone;
	}

	// == AUTRES METHODES ==
	/**
	 * Ajoute une réduction au paiement
	 * 
	 * @param Reduction $reduction
	 * @return void
	 */
	public function addReduction(Reduction $reduction): void
	{
		if ($this->_id != null) {
			$reduction->setIdPayment($this->_id);
		}

		$this->_reductions[] = $reduction;
	}

	/**
	 * Supprime les réductions du paiement.
	 * 
	 * @return bool Retourne True si des réductions ont été supprimé, sinon False.
	 */
	public function clearReductions(): bool
	{
		if (count($this->_reductions) > 0) {
			$this->_reductions = [];
			return true;
		}

		return false;
	}

	/**
	 * Sauvegarde le paiement dans la base de données
	 * 
	 * @return bool Retourne True en cas de succès, sinon False;
	 */
	public function saveToDatabase(): bool
	{
		$database = new Database();
		$result = false;

		if ($this->_id === null) { // Insert
			$id = $database->insert(
				'payments',
				[
					'base_price' => $this->_basePrice,
					'fixed_price' => $this->_fixedPrice,
					'method' => $this->_method->value,
					'date_payment' => $this->_paymentDate->format('Y-m-d H:i:s'),
					'nb_deadlines' => $this->_nbDeadlines,
					'is_done' => $this->_isDone
				]
			);

			if ($id !== false) {
				$this->_id = (int)$id;
				$result = true;
			}
		} else { // Update
			$result = $database->update(
				'payments', 'id_payment', $this->_id,
				[
					'base_price' => $this->_basePrice,
					'fixed_price' => $this->_fixedPrice,
					'method' => $this->_method->value,
					'date_payment' => $this->_paymentDate->format('Y-m-d H:i:s'),
					'nb_deadlines' => $this->_nbDeadlines,
					'is_done' => $this->_isDone
				]
			);
		}

		// Sauvegarde les réductions.
		if ($this->_id !== null) {
			foreach ($this->_reductions as $reduction) {
				$reduction->setIdPayment($this->_id);
				$result = $result && $reduction->saveToDatabase();
			}
		}

		return $result;
	}


	// ==============================================================================
	// ==== Fonctions statiques =====================================================
	// ==============================================================================
	/**
	 * Retourne un paiement à l'aide de sont ID.
	 * 
	 * @param int $idPayment
	 * @return Payment|false Retourne false en cas d'échec
	 */
	public static function getById(int $idPayment): Payment|false
	{
		$database = new Database();

		$rech = $database->query(
			"SELECT * FROM payments WHERE id_payment=:id_payment",
			['id_payment' => $idPayment]
		);

		if($rech !== null) {
			$data = $rech->fetch();

			return new Payment($data);
		}
		
		return false;
	}

	/**
	 * Retourne un paiement à l'aide de sa clé.
	 * 
	 * @param string $key
	 * @return Payment|false Retourne false en cas d'échec
	 */
	public static function getBykey(string $key): Payment|false
	{
		$database = new Database();

		$rech = $database->query(
			"SELECT * FROM payments WHERE uniqueKey=:uniqueKey",
			['uniqueKey' => $key]
		);

		if ($rech !== null) {
			$data = $rech->fetch();

			return new Payment($data);
		}
		
		return false;
	}

	/**
	 * Retourne la liste de tous les paiements fait pendant la saison. Par défaut ceux de la saison en cours.
	 * 
	 * @param string $saison
	 * @return array|false Retourne false en cas d'échec
	 */
	public static function getList(string $saison = null): array|false
	{
		if ($saison === null) {
			$saison = SnakeTools::getCurrentSaison();
		}

		$database = new Database();

		$payments = $database->query(
			"SELECT payments.* FROM payments
			JOIN adherents ON payments.id_payment = adherents.id_payment
			JOIN sections ON sections.id_section = adherents.id_section
			WHERE saison=:saison",
			['saison' => $saison]
		);

		if($payments != null) {
			$list = [];

			while ($data = $payments->fetch()) {
				$list[] = new Payment($data);
			}

			return $list;
		}
		
		return false;
	}

	/**
	 * Supprime le paiement de la base de données.
	 * 
	 * @param int $id
	 * @return bool
	 */
	public static function removeFromDatabase(int $idPayment): bool
	{
		$database = new Database();
		return $database->delete('payments', 'id_payment', $idPayment);
		// Les réductions sont supprimées grâce à la contrainte par clè étrangère de la BDD.
	}
}