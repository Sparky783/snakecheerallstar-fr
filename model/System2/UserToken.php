<?php
namespace System;
use System\Database;

/**
 * Represent an user token for connection to the user space.
 * It is ussed for the keep login process.
 */
class UserToken
{
	
	// ==== ATTRIBUTS ====
	/**
	 * @var int|null $_id User token's ID.
	 */
	private ?int $_id = null;
	
	/**
	 * @var int|null $_idUser User's ID associated tothis token.
	 */
	private ?int $_idUser = null;
	
	/**
	 * @var int|null $_id Token's value.
	 */
	private string $_token = '';

	
	// ==== CONSTRUCTOR ====
	public function __construct($dbData = [])
	{
		if (count($dbData) > 0) {
			$this->_id = (int)$dbData['id_token'];
			$this->_idUser = (int)$dbData['id_user'];
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
	 * Get the user's ID associatedd to this token.
	 * 
	 * @return int|null User's ID.
	 */
	public function getIdUser(): int|null
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
	 * Define the IDof the associated useristrator.
	 * 
	 * @param int $idUser Useristrator ID.
     * @return void
	 */
	public function setIdUser(int $idUser): void
	{
		$this->_idUser = $idUser;
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
				'user_tokens',
				[
					'id_user' => $this->_idUser,
					'token' => $this->_token
				]
			);

			if($id !== false) {
				$this->_id = (int)$id;

				$result = true;
			}
		} else { // Update
			$result = $database->update(
				'user_tokens', 'id_token', $this->_id,
				[
					'id_user' => $this->_idUser,
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
	 * @param int $idUser ID of the token to get.
	 * @return UserToken|false Token wanted, else False if the process failed.
	 */
	public static function getById(int $idToken): UserToken|false
	{
		$database = new Database();

		$rech = $database->query(
			"SELECT * FROM user_tokens WHERE id_token=:id_token",
			['id_token' => $idToken]
		);

		if ($rech !== null) {
			$data = $rech->fetch();

			return new UserToken($data);
		}
		
		return false;
	}

	/**
	 * Get a list of tokens from an user ID.
	 * 
	 * @param int $idUser ID of the user associated to tokens.
	 * @return array|false List of tokens associated to this user ID, else False if the process failed.
	 */
	public static function getByUser(int $idUser): array|false
	{
		$database = new Database();

		$tokens = $database->query(
			"SELECT * FROM user_tokens WHERE id_user=:id_user",
			['id_user' => $idUser]
		);

		if ($tokens !== null) {
			$list = [];

			while ($data = $tokens->fetch()) {
				$list[] = new UserToken($data);
			}

			return $list;
		}
		
		return false;
	}

	/**
	 * Return the list of all useristrator tokens present into the database.
	 * 
	 * @return array|false List of the useristrator tokens, else False is the process failed.
	 */
	public static function getList(): array|false
	{
		$database = new Database();

		$tokens = $database->query("SELECT * FROM user_tokens");

		if ($tokens !== null) {
			$list = [];

			while ($data = $tokens->fetch()) {
				$list[] = new UserToken($data);
			}

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
	public static function removeFromDatabase(int $idToken): bool
	{
		$database = new Database();

		return $database->delete('user_tokens', 'id_token', $idToken);
	}

	/**
	 * Generate a random token.
	 * 
	 * @return string Token generated.
	 */
	public static function generateRandomToken(): string
	{
		return hash('sha512', random_bytes(256));
	}
}