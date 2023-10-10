<?php
namespace System;
use System\Database;

/**
 * Represent a admin user session.
 * It used for the connection to the administration space.
 */
class Admin
{
	// ==== ATTRIBUTS ====
	/**
	 * @var int|null $_id Admin's ID.
	 */
	private ?int $_id = null;
	
	/**
	 * @var string $_email Admin's E-mail.
	 */
	private string $_email = '';
	
	/**
	 * @var string $_password Admin's password.
	 */
	private string $_password = '';
	
	/**
	 * @var string $_name Admin's name.
	 */
	private string $_name = '';
	
	/**
	 * @var array $_roles Admin's roles.
	 */
	private array $_roles = [];

	// ==== CONSTRUCTOR ====
	/**
	 * Make a new object of admin session
	 */
	public function __construct($dbData = [])
	{
		if (count($dbData) > 0) {
			$this->_id = (int)$dbData['id_admin'];
			$this->_email = $dbData['email'];
			$this->_password = $dbData['password'];
			$this->_name = $dbData['name'];
			$this->_roles = unserialize($dbData['roles']);
		}
	}

	// ==== GETTERS ====
	/**
	 * Return the admin ID.
	 * 
	 * @return int
	 */
	public function getId(): int
	{
		return $this->_id;
	}

	/**
	 * Return the admin E-mail.
	 * 
	 * @return string
	 */
	public function getEmail(): string
	{
		return $this->_email;
	}

	/**
	 * Retrun the admin password.
	 * WARNING: The password is hashed.
	 * 
	 * @return string
	 */
	public function getPassword(): string
	{
		return $this->_password;
	}

	/**
	 * Return the admin name.
	 * 
	 * @return string
	 */
	public function getName(): string
	{
		return $this->_name;
	}

	/**
	 * Return roles granted to the admin.
	 * 
	 * @return array
	 */
	public function getRoles(): array
	{
		return $this->_roles;
	}

	// ==== SETTERS ====
	/**
	 * Define the admin ID.
	 * 
	 * @param int $id ID of the admin.
     * @return void
	 */
	public function setId(int $id = null): void
	{
		$this->_id = $id;
	}

	/**
	 * Define the admin E-mail.
	 * 
	 * @param string $email Email of the admin.
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
	 * Set the admin password and hash it.
	 * 
	 * @param string $password Password of the admin.
     * @return void
	 */
	public function setPassword(string $password): void
	{
		$this->_password = sha1(sha1(AUTH_SALT) . sha1($password));
	}

	/**
	 * Set the admin name.
	 * 
	 * @param  string $name Name of the admin.
     * @return void
	 */
	public function setName(string $name): void
	{
		$this->_name = $name;
	}

	/**
	 * Set the list of admin roles.
	 * 
	 * @param array $roles List of roles.
     * @return void
	 */
	public function setRoles(array $roles): void
	{
		$this->_roles = $roles;
	}

	// ==== OTHER METHODS ====
	/**
	 * Add a nex roles to the admin.
	 * 
	 * @param string $role Role to add.
     * @return void
	 */
	public function addRoles(string $role): void
	{
		$this->_roles[] = $role;
	}

	/**
	 * Convert this object to an associative array.
	 * 
	 * @return array Associative array of this admin.
	 */
	public function toArray(): array
	{
		return [
			'id_admin' => $this->_id,
			'email' => $this->_email,
			'name' => $this->_name,
			'roles' => $this->_roles
		];
	}

	/**
	 * Save this admin object into database.
	 * If this admin is a new one, create id and affect a new ID.
	 * 
	 * @return bool Return True if the admin was correctly saved, else False.
	 */
	public function saveToDatabase(): bool
	{
		$database = new Database();
		$result = false;

		if ($this->_id === null) { // Insert
			$id = $database->insert(
				'admins',
				[
					'email' => $this->_email,
					'password' => $this->_password,
					'name' => $this->_name,
					'roles' => serialize($this->_roles)
				]
			);

			if ($id !== false) {
				$this->_id = (int)$id;

				$result = true;
			}
		} else { // Update
			$result = $database->update(
				'admins', 'id_admin', $this->_id,
				[
					'email' => $this->_email,
					'password' => $this->_password,
					'name' => $this->_name,
					'roles' => serialize($this->_roles)
				]
			);
		}

		return $result;
	}

	
	// ===========================================================================
	// ==== Static functions =====================================================
	// ===========================================================================
	/**
	 * Get an admin from his ID.
	 * 
	 * @param int $idAdmin ID of the admin to get.
	 * @return Admin|false Admin wanted, else False if the process failed.
	 */
	public static function getById(int $idAdmin): Admin|false
	{
		$database = new Database();

		$rech = $database->Query(
			"SELECT * FROM admins WHERE id_admin=:id_admin",
			['id_admin' => $idAdmin]
		);

		if ($rech !== null) {
			$data = $rech->fetch();

			return new Admin($data);
		}
		
		return false;
	}

	/**
	 * Return the list of all administrators of the website.
	 * 
	 * @return array|bool List of the administrators, else False is the process failed.
	 */
	public static function getList(): array|bool
	{
		$database = new Database();

		$admins = $database->query("SELECT * FROM admins");

		if ($admins !== null) {
			$list = [];

			while ($data = $admins->fetch()) {
				$list[] = new Admin($data);
			}

			return $list;
		}
		
		return false;
	}

	/**
	 * Remove an administrator from the databas.
	 * 
	 * @param int $idAdmin Id of the admin to remove.
	 * @return bool Return True if the process succeed, else False.
	 */
	public static function removeFromDatabase(int $idAdmin): bool
	{
		$database = new Database();

		return $database->delete('admins', 'id_admin', $idAdmin);
	}

	/**
	 * Get an admin with his credentials (Login)
	 * 
	 * @param string $login Username or E-mail of the admin depending of the configuration.
	 * @param string $password Password of the admin. The password must be correctly hashed.
	 * @return Admin|false Return the admin logged, else False if the process failed.
	 */
	public static function login(string $login, string $password): Admin|false
	{
		$database = new Database();
		$rech = $database->query(
			"SELECT * FROM admins WHERE email=:email AND password=:password",
			[
				'email' => $login,
				'password' => $password,
			]
		);
		
		if ($rech !== false) {
			$data = $rech->fetch();
			
			if ($data !== false) {
				return new Admin($data);
			}
		}

		return false;
	}
}