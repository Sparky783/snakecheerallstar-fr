<?php
namespace System;
use System\Database;

/**
 * Represent an user token for connection to the useristration space.
 * It is ussed for the keep login process.
 */
class UserToken
{
	// == ATTRIBUTS ==
	private ?int $_id = null;
	private ?int $_idUser = null;
	private string $_token = "";

	
	// == CONSTRUCTORS ==
	public function __construct($dbData = null)
	{
		if($dbData != null)
		{
			$this->_id = intval($dbData['id_token']);
			$this->_idUser = intval($dbData['id_user']);
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
	 * Get the useristrator's ID associatedd to this token.
	 * 
	 * @return int Useristrator's ID.
	 */
	public function getIdUser(): int
	{
		return $this->_idUser;
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
	 * Define the IDof the associated useristrator.
	 * 
	 * @param mixed $idUser Useristrator ID.
     * @return void
	 */
	public function setIdUser(mixed $idUser): void
	{
		$this->_idUser = intval($idUser);
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
				"user_tokens",
				array(
					"id_user" => $this->_idUser,
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
				"user_tokens", "id_token", $this->_id,
				array(
					"id_user" => $this->_idUser,
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
	 * @param mixed $idUser ID of the token to get.
	 * @return UserToken|bool Token wanted, else False if the process failed.
	 */
	static public function getById(mixed $idToken): UserToken|bool
	{
		$database = new Database();

		$rech = $database->Query(
			"SELECT * FROM user_tokens WHERE id_token=:id_token",
			array("id_token" => intval($idToken))
		);

		if($rech != null)
		{
			$data = $rech->fetch();

			return new UserToken($data);
		}
		
		return false;
	}

	/**
	 * Get a list of tokens from an user ID.
	 * 
	 * @param int $idUser ID of the user associated to tokens.
	 * @return array|bool List of tokens associated to this user ID, else False if the process failed.
	 */
	static public function getByUser(int $idUser): array|bool
	{
		$database = new Database();

		$tokens = $database->query(
			"SELECT * FROM user_tokens WHERE id_user=:id_user",
			array("id_user" => intval($idUser))
		);

		if($tokens != null)
		{
			$list = array();

			while($data = $tokens->fetch())
				$list[] = new UserToken($data);

			return $list;
		}
		
		return false;
	}

	/**
	 * Return the list of all useristrator tokens present into the database.
	 * 
	 * @return array|bool List of the useristrator tokens, else False is the process failed.
	 */
	static public function getList(): array|bool
	{
		$database = new Database();

		$tokens = $database->query("SELECT * FROM user_tokens");

		if($tokens != null)
		{
			$list = array();

			while($data = $tokens->fetch())
				$list[] = new UserToken($data);

			return $list;
		}
		
		return false;
	}

	/**
	 * Remove an useristrator token from the databas.
	 * 
	 * @param int $idToken Id of the token to remove.
	 * @return bool Return True if the process succeed, else False.
	 */
	static public function removeFromDatabase(int $idToken): bool
	{
		$database = new Database();

		return $database->delete("user_tokens", "id_token", intval($idToken));
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