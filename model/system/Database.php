<?php
require_once(ABSPATH . "model/system/ToolBox.php");
require_once(ABSPATH . "model/system/Erreur.php");

class Database
{
	private $bdd;
	private $error;
	

	// == METHODES PRIMAIRES ==
	public function __construct()
	{
		$this->Connection();
	}

	
	// == METHODES GETTERS ==
	public function GetDatabase() : PDO
	{
		return $this->bdd;
	}

	public function GetError()
	{
		return $this->error;
	}
	
	// == AUTRES METHODES ==
	// Ce connecte à une base de données.
	private function Connection() : bool
	{
		try
		{
			$this->bdd = new PDO("mysql:host=" . DB_HOST . "; dbname=" . DB_NAME, DB_USER, DB_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '" . DB_CHARSET . "'"));
			return true;
		}
		catch(Exception $e)
		{
			new Erreur($e);
		}
		return false;
	}
	
	// Execute une requete SQL en fonction des paramètres envoyés. Retourne un PDO::Statment
	public function Query(string $requete, array $variables = array(""))
	{
		if($this->bdd && $requete)
		{
			// Prepare variables
			foreach($variables as &$variable)
			{
				if(is_bool($variable))
					$variable = intval($variable);
			}

			$req = $this->bdd->prepare($requete);
			$req->execute($variables);

			if($req->errorCode() === '00000')
				return $req;
			else
				$this->error = $req->errorInfo();
		}

		return false;
	}

	// Insert une ligne dans une table et retourne l'id crée.
	public function Insert(string $tableName, array $variables)
	{
		$requete = "INSERT INTO `" . $tableName . "` (";

		foreach ($variables as $key => $value)
			$requete .= "`" . $key . "`,";

		$requete = substr($requete, 0, strlen($requete) - 1);
		$requete .= ") VALUES (";

		foreach ($variables as $key => $value)
			$requete .= ":" . $key . ",";

		$requete = substr($requete , 0, strlen($requete) - 1);
		$requete .= ");";

		$result = $this->Query($requete, $variables);

		if($result !== false)
		{
			if($result->errorCode() === '00000')
				return $this->bdd->lastInsertId();
			else
				$this->error = $result->errorInfo();
		}

		return false;
	}

	// Met à jour une ligne dans une table.
	public function Update(string $tableName, string $idColumnName, $idValue, array $variables) : bool
	{
		$requete = "UPDATE `" . $tableName . "` SET";

		foreach ($variables as $key => $value)
			$requete .= "`" . $key . "`=:" . $key . ",";

		$requete = substr($requete , 0, strlen($requete) - 1);
		$requete .= " WHERE `" . $idColumnName . "`=:" . $idColumnName . ";";

		$variables[$idColumnName] = $idValue;
		
		$result = $this->Query($requete, $variables);

		if($result !== false)
		{
			if($result->errorCode() === '00000')
				return true;
			else
				$this->error = $result->errorInfo();
		}

		return false;
	}

	// Supprime une ligne d'une table.
	public function Delete(string $tableName, string $idColumnName, $idValue) : bool
	{
		$requete = "DELETE FROM `" . $tableName . "` WHERE `" . $idColumnName . "`=:value;";
		$variables = array('value' => $idValue);

		$result = $this->Query($requete, $variables);

		if($result !== false)
		{
			if($result->errorCode() === '00000')
				return true;
			else
				$this->error = $result->errorInfo();
		}

		return false;
	}
	
	// Sauvegarde l'ensemble de la base de données dans un fichier (*.sql) stocké sur le serveur.
	public function SaveDatabase()
	{
		// On liste d'abord l'ensemble des tables
		$result = $this->bdd->query("SHOW TABLES");
		$tables = array();

		// On exclut éventuellement les tables indiqu�es
		while($row = $result->fetch())
			$tables[] = $row[0];
		
		$result->closeCursor();

		// La variable $code contiendra le script de sauvegarde.
		// On englobe le script de backup dans une transaction
		// et on désactive les contraintes de clés étrangères
		$code = "-- BDD ".DB_NAME." sauvegard� le ".date("d/m/Y � H:i:s");
		$code .= "\n\n";
		$code .= "SET FOREIGN_KEY_CHECKS=0;\n";
		$code .= "SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\";\n";
		$code .= "SET AUTOCOMMIT=0;\n";
		$code .= "START TRANSACTION;";
		$code .= "\n\n";

		// On boucle sur l'ensemble des tables à sauvegarder
		foreach($tables as $table)
		{			
			// On ajoute une instruction pour supprimer la table si elle existe déjà
			$code .= "DROP TABLE IF EXISTS `".$table."`;\n";

			// On g�n�re ensuite la structure de la table
			$result = $this->bdd->query("SHOW CREATE TABLE ".$table);
			$retour = $result->fetch(PDO::FETCH_ASSOC);
			$code .= $retour['Create Table'].";\n\n";
			$result->closeCursor();
			
			// On boucle sur l'ensemble des enregistrements de la table
			$result = $this->bdd->query("SELECT * FROM ".$table);
			while($row = $result->fetch(PDO::FETCH_ASSOC))
			{				
				$code .= "INSERT INTO `".$table."` VALUES(";

				// On boucle sur l'ensemble des champs de l'enregistrement
				foreach($row as $fieldValue)
				{
					// On purifie la valeur du champ
					$fieldValue = addslashes($fieldValue);
					$fieldValue = preg_replace("/\r\n/", "\\r\\n", $fieldValue);

					if(is_null($fieldValue))
						$code .= 'NULL';
					else
						$code .= '"'.$fieldValue.'", ';
				}

				// On supprime la virgule à la fin de la requète INSERT
				$code = mb_substr($code, 0, -2).");\n";
			}
			$result->closeCursor();
			$code .= "\n";
		}

		// On valide la transaction et on réactive les contraintes de clés étrangères
		$code .= "SET FOREIGN_KEY_CHECKS=1;\n";
		$code .= "COMMIT;";
		
		// Sauvegarde de la BDD
		$dossier = ABSPATH."/savesDatabase";
		if(ToolBox::IsDirectoryOrCreateIt($dossier."/".date("Y")))
		{
			$dossier .= "/".date("Y");
			$fichier = $dossier."/Database - (".date("Y-m-d")." a ".date("H")."h".date("i").").sql";
			file_put_contents($fichier, $code);
			return $fichier;
		}
	}
}