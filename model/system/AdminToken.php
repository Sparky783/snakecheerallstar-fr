<?php
namespace System;
use System\Database;

/**
 * Represent an admin token for connection to the administration space.
 * It is ussed for the keep login process.
 */
class AdminToken
{
	// == ATTRIBUTS ==
	private ?int $_id = null;
	private ?int $_idAdmin = null;
	private string $_token = "";

	
	// == CONSTRUCTORS ==
	public function __construct($dbData = null)
	{
		if($dbData != null)
		{
			$this->_id = intval($dbData['id_token']);
			$this->_idAdmin = intval($dbData['id_admin']);
			$this->_token = $dbData['token'];
		}
	}

	// == GETTERS ==
	/**
	 * Get the ID of this token.
	 * 
	 * @return int Token ID.
	 */
	public function getId(): int
	{
		return $this->_id;
	}

	/**
	 * Get the administrator's ID associatedd to this token.
	 * 
	 * @return int Administrator's ID.
	 */
	public function getIdAdmin(): int
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

	// == SETTERS ==
	/**
	 * Define the token ID.
	 * 
	 * @param mixed $id Token ID.
     * @return void
	 */
	public function SetId(mixed $id = null): void
	{
		$this->_id = intval($id);
	}

	/**
	 * Define the IDof the associated administrator.
	 * 
	 * @param mixed $idAdmin Administrator ID.
     * @return void
	 */
	public function setIdAdmin(mixed $idAdmin): void
	{
		$this->_idAdmin = intval($idAdmin);
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

	// == OTHER METHODS ==
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

		if($this->_id == null) // Insert
		{
			$id = $database->Insert(
				"admin_tokens",
				array(
					"id_admin" => $this->_idAdmin,
					"token" => $this->_token
				)
			);

			if($id !== false)
			{
				$this->_id = intval($id);

				$result = true;
			}
		}
		else // Update
		{
			$result = $database->Update(
				"admin_tokens", "id_token", $this->_id,
				array(
					"id_admin" => $this->_idAdmin,
					"token" => $this->_token
				)
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
	 * @param mixed $idAdmin ID of the token to get.
	 * @return AdminToken|bool Token wanted, else False if the process failed.
	 */
	static public function getById(mixed $idToken): AdminToken|bool
	{
		$database = new Database();

		$rech = $database->Query(
			"SELECT * FROM admin_tokens WHERE id_token=:id_token",
			array("id_token" => intval($idToken))
		);

		if($rech != null)
		{
			$data = $rech->fetch();

			return new AdminToken($data);
		}
		
		return false;
	}

	/**
	 * Get a list of tokens from an admin ID.
	 * 
	 * @param int $idAdmin ID of the admin associated to tokens.
	 * @return array|bool List of tokens associated to this admin ID, else False if the process failed.
	 */
	static public function getByAdmin(int $idAdmin): array|bool
	{
		$database = new Database();

		$tokens = $database->query(
			"SELECT * FROM admin_tokens WHERE id_admin=:id_admin",
			array("id_admin" => intval($idAdmin))
		);

		if($tokens != null)
		{
			$list = array();

			while($data = $tokens->fetch())
				$list[] = new AdminToken($data);

			return $list;
		}
		
		return false;
	}

	/**
	 * Return the list of all administrator tokens present into the database.
	 * 
	 * @return array|bool List of the administrator tokens, else False is the process failed.
	 */
	static public function getList(): array|bool
	{
		$database = new Database();

		$tokens = $database->query("SELECT * FROM admin_tokens");

		if($tokens != null)
		{
			$list = array();

			while($data = $tokens->fetch())
				$list[] = new AdminToken($data);

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
	static public function removeFromDatabase(int $idToken): bool
	{
		$database = new Database();

		return $database->delete("admin_tokens", "id_token", $idToken);
	}

	/**
	 * Generate a random token.
	 * 
	 * @return string Token generated.
	 */
	static public function generateRandomToken(): string
	{
		return hash("sha512", random_bytes(256));
	}
}