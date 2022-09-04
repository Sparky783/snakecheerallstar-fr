<?php
require_once(ABSPATH . "model/system/Database.php");

// ======================================================================
// ==== Représent un token de connexion pour l'espace administrateur ====
// ==== pour une connexion automatique (Keep login) =====================
// ======================================================================

class AdminToken
{
	// == ATTRIBUTS ==
	private $id = null;
	private $id_admin = "";
	private $token = "";

	
	// == METHODES PRIMAIRES ==
	public function __construct($dbData = null)
	{
		if($dbData != null)
		{
			$this->id = intval($dbData['id_admin']);
			$this->id_admin = intval($dbData['id_admin']);
			$this->token = $dbData['token'];
		}
	}

	// == METHODES GETTERS ==
	public function GetId()
	{
		return $this->id;
	}

	public function GetIdAdmin()
	{
		return $this->id_admin;
	}

	public function GetToken()
	{
		return $this->token;
	}

	// == METHODES SETTERS ==
	public function SetId($id = null)
	{
		$this->id = intval($id);
	}

	public function SetIdAdmin($id_admin)
	{
		$this->id_admin = intval($id_admin);
	}

	public function SetToken($token)
	{
		$this->token = $token;
	}

	// == AUTRES METHODES ==
	public function SaveToDatabase()
	{
		$database = new Database();
		$result = false;

		if($this->id == null) // Insert
		{
			$id = $database->Insert(
				"admin_tokens",
				array(
					"id_admin" => $this->id_admin,
					"token" => $this->token
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
				"admin_tokens", "id_token", $this->id,
				array(
					"id_admin" => $this->id_admin,
					"token" => $this->token
				)
			);
		}

		return $result;
	}


	// ==============================================================================
	// ==== Fonctions statiques =====================================================
	// ==============================================================================
	// Récupère un token à l'aide de son ID.
	static public function GetById($id_token)
	{
		$database = new Database();

		$rech = $database->Query(
			"SELECT * FROM admin_tokens WHERE id_token=:id_token",
			array("id_token" => intval($id_token))
		);

		if($rech != null)
		{
			$data = $rech->fetch();

			return new Token($data);
		}
		
		return false;
	}

	// Récupère la liste des tokens d'un utilisateur.
	static public function GetByAdmin($id_admin)
	{
		$database = new Database();

		$tokens = $database->Query(
			"SELECT * FROM admin_tokens WHERE id_admin=:id_admin",
			array("id_admin" => intval($id_admin))
		);

		if($tokens != null)
		{
			$list = array();

			while($data = $tokens->fetch())
				$list[] = new Token($data);

			return $list;
		}
		
		return false;
	}

	// Retourne la liste des utilisateurs du site.
	static public function GetList()
	{
		$database = new Database();

		$tokens = $database->Query("SELECT * FROM admin_tokens");

		if($tokens != null)
		{
			$list = array();

			while($data = $tokens->fetch())
				$list[] = new Token($data);

			return $list;
		}
		
		return false;
	}

	// Supprime un utilisateur de la base de donénes.
	static public function RemoveFromDatabase($id_token)
	{
		$database = new Database();

		return $database->Delete("admin_tokens", "id_token", intval($id_token));
	}

	// Génère un mot de passe
	static public function GenerateRandomToken()
	{
		return hash("sha512", random_bytes(256));
	}
}