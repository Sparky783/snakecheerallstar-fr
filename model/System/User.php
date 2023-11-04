<?php
namespace System;
use System\Database;

/**
 * Represent a user user session.
 */
class User
{
	// ==== ATTRIBUTS ====
	/**
	 * @var int|null $_id User's ID.
	 */
	private ?int $_id = null;
	
	/**
	 * @var string $_email User's E-mail.
	 */
	private string $_email = '';
	
	/**
	 * @var string $_password User's password.
	 */
	private string $_password = '';
	
	/**
	 * @var string $_name User's name.
	 */
	private string $_name = '';
	
	/**
	 * @var array $_status User's status.
	 */
	private array $_status = [];

	// ==== CONSTRUCTOR ====
	/**
	 * Make a new object of user session
	 */
	public function __construct($dbData = [])
	{
		if (count($dbData) > 0) {
			$this->_id = (int)$dbData['id_user'];
			$this->_email = $dbData['email'];
			$this->_password = $dbData['password'];
			$this->_name = $dbData['name'];
			$this->_status = unserialize($dbData['status']);
		}
	}

	// ==== GETTERS ====
	/**
	 * Return the user ID.
	 * 
	 * @return int|null
	 */
	public function getId(): int|null
	{
		return $this->_id;
	}

	/**
	 * Return the user E-mail.
	 * 
	 * @return string
	 */
	public function getEmail(): string
	{
		return $this->_email;
	}

	/**
	 * Retrun the user password.
	 * WARNING: The password is hashed.
	 * 
	 * @return string
	 */
	public function getPassword(): string
	{
		return $this->_password;
	}

	/**
	 * Return the user name.
	 * 
	 * @return string
	 */
	public function getName(): string
	{
		return $this->_name;
	}

	/**
	 * Return status granted to the user.
	 * 
	 * @return array
	 */
	public function getStatus(): array
	{
		return $this->_status;
	}

	// ==== SETTERS ====
	/**
	 * Define the user ID.
	 * 
	 * @param int $id ID of the user.
     * @return void
	 */
	public function setId(int $id): void
	{
		$this->_id = $id;
	}

	/**
	 * Define the user E-mail.
	 * 
	 * @param string $email Email of the user.
	 * @return bool Return True if the E-mail is correctly added, else False.
	 */
	public function setEmail(string $email): bool
	{
		if (preg_match("/^[a-z0-9._-]+@[a-z0-9._-]+\.[a-z]{2,6}$/", $email)) {
			$this->_email = $email;

			return true;
		}

		return false;
	}

	/**
	 * Set the user password and hash it.
	 * 
	 * @param string $password Password of the user.
     * @return void
	 */
	public function setPassword(string $password): void
	{
		$this->_password = sha1(sha1(AUTH_SALT) . sha1($password));
	}

	/**
	 * Set the user name.
	 * 
	 * @param  string $name Name of the user.
     * @return void
	 */
	public function setName(string $name): void
	{
		$this->_name = $name;
	}

	/**
	 * Set the list of user status.
	 * 
	 * @param array $status List of status.
     * @return void
	 */
	public function setStatus(array $status): void
	{
		$this->_status = $status;
	}

	// ==== OTHER METHODS ===
	/**
	 * Add a nex status to the user.
	 * 
	 * @param string $role Role to add.
     * @return void
	 */
	public function addStatus(string $role): void
	{
		$this->_status[] = $role;
	}

	/**
	 * Convert this object to an associative array.
	 * 
	 * @return array Associative array of this user.
	 */
	public function toArray(): array
	{
		return [
			'id_user' => $this->_id,
			'email' => $this->_email,
			'name' => $this->_name,
			'status' => $this->_status
		];
	}

	/**
	 * Save this user object into database.
	 * If this user is a new one, create id and affect a new ID.
	 * 
	 * @return bool Return True if the user was correctly saved, else False.
	 */
	public function saveToDatabase(): bool
	{
		$database = new Database();
		$result = false;

		if ($this->_id === null) { // Insert
			$id = $database->insert(
				'users',
				[
					'email' => $this->_email,
					'password' => $this->_password,
					'name' => $this->_name,
					'status' => serialize($this->_status)
				]
			);

			if ($id !== false) {
				$this->_id = (int)$id;

				$result = true;
			}
		} else { // Update
			$result = $database->Update(
				'users', 'id_user', $this->_id,
				[
					'email' => $this->_email,
					'password' => $this->_password,
					'name' => $this->_name,
					'status' => serialize($this->_status)
				]
			);
		}

		return $result;
	}

	
	// ===========================================================================
	// ==== Static functions =====================================================
	// ===========================================================================
	/**
	 * Get an user from his ID.
	 * 
	 * @param int $idUser ID of the user to get.
	 * @return User|false User wanted, else False if the process failed.
	 */
	public static function getById(int $idUser): User|false
	{
		$database = new Database();

		$rech = $database->query(
			"SELECT * FROM users WHERE id_user=:id_user",
			['id_user' =>$idUser]
		);

		if ($rech != null) {
			$data = $rech->fetch();

			return new User($data);
		}
		
		return false;
	}

	/**
	 * Return the list of all useristrators of the website.
	 * 
	 * @return array|false List of the useristrators, else False is the process failed.
	 */
	public static function GetList(): array|false
	{
		$database = new Database();

		$users = $database->query("SELECT * FROM users");

		if ($users !== null) {
			$list = [];

			while($data = $users->fetch()) {
				$list[] = new User($data);
			}

			return $list;
		}
		
		return false;
	}

	/**
	 * Remove an useristrator from the databas.
	 * 
	 * @param int $idUser Id of the user to remove.
	 * @return bool Return True if the process succeed, else False.
	 */
	static public function removeFromDatabase(int $idUser): bool
	{
		$database = new Database();

		return $database->delete('users', 'id_user', $idUser);
	}

	/**
	 * Get an user with his credentials (Login)
	 * 
	 * @param string $login Username or E-mail of the user depending of the configuration.
	 * @param string $password Password of the user. The password must be correctly hashed.
	 * @return User|false Return the user logged, else False if the process failed.
	 */
	public static function login(string $login, string $password): User|false
	{
		$database = new Database();
		$rech = $database->query(
			"SELECT * FROM users WHERE email=:email AND password=:password",
			[
				'email' => $login,
				'password' => $password,
			]
		);
		
		if ($rech !== false) {
			$data = $rech->fetch();
			
			if($data !== false) {
				return new User($data);
			}
		}

		return false;
	}
}