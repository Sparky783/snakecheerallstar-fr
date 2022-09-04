<?php
require_once(ABSPATH . "model/system/ToolBox.php");
require_once(ABSPATH . "model/system/Database.php");

// ========================================================================================
// ==== Administrateur du site. Nécessaire pour la connexion à l'espace administrateur ====
// ========================================================================================

class Admin
{
	// == ATTRIBUTS ==
	private $id = null;
	private $email = "";
	private $password = "";
	private $name = "";
	private $roles = array();

	
	// == METHODES PRIMAIRES ==
	public function __construct($dbData = null)
	{
		if($dbData != null)
		{
			$this->id = intval($dbData['id_admin']);
			$this->email = $dbData['email'];
			$this->password = $dbData['password'];
			$this->name = $dbData['name'];
			$this->roles = unserialize($dbData['roles']);
		}
	}

	// == METHODES GETTERS ==
	public function GetId()
	{
		return $this->id;
	}

	public function GetEmail()
	{
		return $this->email;
	}

	public function GetPassword()
	{
		return $this->password;
	}

	public function GetName()
	{
		return $this->name;
	}

	public function GetRoles()
	{
		return $this->roles;
	}

	// == METHODES SETTERS ==
	public function SetId($id = null)
	{
		$this->id = intval($id);
	}

	public function SetEmail($email) {
		if(preg_match("/^[a-z0-9._-]+@[a-z0-9._-]+\.[a-z]{2,6}$/", $email)) {
			$this->email = $email;
			return true;
		}
		return false;
	}

	public function SetPassword($password)
	{
		$this->password = sha1(sha1(AUTH_SALT) . sha1($password));
	}

	public function SetName($name)
	{
		$this->name = $name;
	}

	public function SetRoles($roles)
	{
		$this->roles = $roles;
	}

	// == AUTRES METHODES ==
	public function AddRoles($roles)
	{
		$this->roles[] = $roles;
	}

	public function ToArray()
	{
		return array(
			"id_admin" => $this->id,
			"email" => $this->email,
			"name" => $this->name,
			"roles" => $this->roles
		);
	}

	public function SaveToDatabase()
	{
		$database = new Database();
		$result = false;

		if($this->id == null) // Insert
		{
			$id = $database->Insert(
				"admins",
				array(
					"email" => $this->email,
					"password" => $this->password,
					"name" => $this->name,
					"roles" => serialize($this->roles)
				)
			);

			if($id !== false)
			{
				$this->id = intval($id);

				$result = true;
			}
		}
		else // Update
		{
			$result = $database->Update(
				"admins", "id_admin", $this->id,
				array(
					"email" => $this->email,
					"password" => $this->password,
					"name" => $this->name,
					"roles" => serialize($this->roles)
				)
			);
		}

		return $result;
	}

	
	// ==============================================================================
	// ==== Fonctions statiques =====================================================
	// ==============================================================================
	// Récupère un administrateur à l'aide de son ID.
	static public function GetById($id_admin)
	{
		$database = new Database();

		$rech = $database->Query(
			"SELECT * FROM admins WHERE id_admin=:id_admin",
			array("id_admin" => intval($id_admin))
		);

		if($rech != null)
		{
			$data = $rech->fetch();

			return new Admin($data);
		}
		
		return false;
	}

	// Retourne la liste des administrateurs du site.
	static public function GetList()
	{
		$database = new Database();

		$admins = $database->Query("SELECT * FROM admins");

		if($admins != null)
		{
			$list = array();

			while($data = $admins->fetch())
				$list[] = new Admin($data);

			return $list;
		}
		
		return false;
	}

	// Supprime un utilisateur de la base de donénes.
	static public function RemoveFromDatabase($id_admin)
	{
		$database = new Database();

		return $database->Delete("admins", "id_admin", intval($id_admin));
	}

	// Récupère un utilisateur à l'aide de ses identifiants.
	static public function Login($login, $password)
	{
		$database = new Database();
		$rech = $database->query(
			"SELECT * FROM admins WHERE email=:email AND password=:password",
			array(
				"email" => $login,
				"password" => $password,
			)
		);
		
		if($rech !== false)
		{
			$data = $rech->fetch();
			
			if($data != null) // Connexion réussi
				return new Admin($data);
		}

		return false;
	}
}