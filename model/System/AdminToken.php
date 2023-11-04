<?php
namespace System;
use System\Database;

/**
 * Represent an admin token for connection to the administration space.
 * It is ussed for the keep login process.
 */
class AdminToken
{
	// ==== ATTRIBUTS ====
	/**
	 * @var int|null $_id Admin token's ID.
	 */
	private ?int $_id = null;
	
	/**
	 * @var int|null $_idAdmin Admin' ID associated to this admin token.
	 */
	private ?int $_idAdmin = null;
	
	/**
	 * @var string $_token Value of the token.
	 */
	private string $_token = '';

	
	// ==== CONSTRUCTOR ====
	public function __construct($dbData = [])
	{
		if (count($dbData) > 0) {
			$this->_id = (int)$dbData['id_token'];
			$this->_idAdmin = (int)$dbData['id_admin'];
			$this->_token = $dbData['token'];
		}
	}

	// ==== GETTERS ====
	/**
	 * Get the ID of this token.
	 * 
	 * @return int|null Token ID.
	 */
	public function getId(): int|null
	{
		return $this->_id;
	}

	/**
	 * Get the administrator's ID associatedd to this token.
	 * 
	 * @return int|null Administrator's ID.
	 */
	public function getIdAdmin(): int|null
	{
		return $this->_idAdmin;
	}

	/**
	 * Get the value of this token.
	 * 
	 * @return string Token value.
	 */
	public function getValue(): string
	{
		return $this->_token;
	}

	// ==== SETTERS ====
	/**
	 * Define the token ID.
	 * 
	 * @param int $id Token ID.
     * @return void
	 */
	public function setId(int $id = null): void
	{
		$this->_id = $id;
	}

	/**
	 * Define the IDof the associated administrator.
	 * 
	 * @param int $idAdmin Administrator ID.
     * @return void
	 */
	public function setIdAdmin(int $idAdmin): void
	{
		$this->_idAdmin = $idAdmin;
	}

	/**
	 * Define the token string.
	 * 
	 * @param string $token Token string.
     * @return void
	 */
	public function setValue(string $token): void
	{
		$this->_token = $token;
	}

	// ==== OTHER METHODS ====
	/**
	 * Save this token object into database.
	 * If this token is a new one, create id and affect a new ID.
	 * 
	 * @return bool Return True if the token was correctly saved, else False.
	 */
	public function saveToDatabase(): bool
	{
		$database = new Database();
		$result = false;

		if ($this->_id === null) { // Insert
			$id = $database->insert(
				'admin_tokens',
				[
					'id_admin' => $this->_idAdmin,
					'token' => $this->_token
				]
			);

			if ($id !== false) {
				$this->_id = intval($id);

				$result = true;
			}
		} else { // Update
			$result = $database->update(
				'admin_tokens', 'id_token', $this->_id,
				[
					'id_admin' => $this->_idAdmin,
					'token' => $this->_token
				]
			);
		}

		return $result;
	}


	// ===========================================================================
	// ==== Static functions =====================================================
	// ===========================================================================
	/**
	 * Get a token from his ID.
	 * 
	 * @param int $idAdmin ID of the token to get.
	 * @return AdminToken|false Token wanted, else False if the process failed.
	 */
	public static function getById(int $idToken): AdminToken|false
	{
		$database = new Database();

		$rech = $database->query(
			"SELECT * FROM admin_tokens WHERE id_token=:id_token",
			['id_token' => $idToken]
		);

		if ($rech !== null) {
			$data = $rech->fetch();
			
			if ($data !== false) {
				return new AdminToken($data);
			}
		}
		
		return false;
	}

	/**
	 * Get a list of tokens from an admin ID.
	 * 
	 * @param int $idAdmin ID of the admin associated to tokens.
	 * @return array|false List of tokens associated to this admin ID, else False if the process failed.
	 */
	public static function getByAdmin(int $idAdmin): array|false
	{
		$database = new Database();

		$tokens = $database->query(
			"SELECT * FROM admin_tokens WHERE id_admin=:id_admin",
			['id_admin' => $idAdmin]
		);

		if ($tokens !== null) {
			$list = [];

			while ($data = $tokens->fetch()) {
				$list[] = new AdminToken($data);
			}

			return $list;
		}
		
		return false;
	}

	/**
	 * Return the list of all administrator tokens present into the database.
	 * 
	 * @return array|false List of the administrator tokens, else False is the process failed.
	 */
	public static function getList(): array|false
	{
		$database = new Database();
		$tokens = $database->query("SELECT * FROM admin_tokens");

		if ($tokens !== null) {
			$list = array();

			while ($data = $tokens->fetch()) {
				$list[] = new AdminToken($data);
			}

			return $list;
		}
		
		return false;
	}

	/**
	 * Remove an administrator token from the databas.
	 * 
	 * @param int $idToken Id of the token to remove.
	 * @return bool Return True if the process succeed, else False.
	 */
	public static function removeFromDatabase(int $idToken): bool
	{
		$database = new Database();

		return $database->delete('admin_tokens', 'id_token', $idToken);
	}

	/**
	 * Generate a random token.
	 * 
	 * @return string Token generated.
	 */
	public static function generateRandomToken(): string
	{
		return hash("sha512", random_bytes(256));
	}
}