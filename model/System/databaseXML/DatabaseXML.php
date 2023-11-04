<?php
include_once("DxmlTable.php");

class DatabaseXML
{
	// ATTRIBUTS
	private $_path_databases = "content";	// Chemin d'accès au base de données. 
	private $_name_database = "";		// Nom de la base de données en cours.
	
	
	/**
	 * Instancie un objet DatabaseXML.
	 */
	public function __construct($database = null)
	{
		if($database != null)
			$this->_path_databases = $database;
		
		// Crée le dossier accueillant les bases de données si c'est la première utilisation.
		if(!file_exists($this->_path_databases))
			mkdir($this->_path_databases);
	}
	
	
	/**
	 * Vérifie si la base de données XML existe.
	 * @param {$name_database} : Nom de la base de données à vérifier.
     * @return : Retourne True si la base de données existe sinon False.
	 */
	static public function DatabaseExists($name_database)
	{
		if(file_exists($this->_path_databases . "/" . self::FormatName($name_database)))
			return true;
		return false;
	}
	
	/**
	 * Crée une nouvelle base de données XML et se connecte dessus.
	 * @param {$name_database} : Nom de la base de données à créer.
	 */
	public function CreateDatabase($name_database)
	{
		// Crée le dossier accueillant les bases de données si c'est la première utilisation.
		if(!file_exists($this->_path_databases))
			mkdir($_path_databases);
		
		// Crée la base de données si elle n'existe pas
		$name_database = self::FormatName($name_database);
		if(!file_exists($this->_path_databases . "/" . $name_database))
			mkdir($this->_path_databases . "/" . $name_database);
		
		// Se connect à la base de données.
		$this->LoadDatabase($name_database);
	}
	
	/**
	 * Se connect à une base de données.
	 * @param {$name_database} : Nom de la base de données sur laquelle se connecter.
     * @return : Retourne True si le chargement réussi sinon False.
	 */
	public function LoadDatabase($name_database)
	{
		$name_database = self::FormatName($name_database);
		if(file_exists($this->_path_databases . "/" . $name_database))
		{
			$this->_name_database = $name_database;
			return true;
		}
		return false;
	}
	
	/**
	 * Vide une base de données.
	 * @param {$name_database} : Nom de la base de données à vider.
     * @return : Retourne True si l'opération a réussi sinon False.
	 */
	public function TruncateDatabase()
	{
		if($this->_name_database != "")
		{
			$name_database = $this->_name_database;
			
			$this->DropDatabase();
			mkdir($this->_path_databases . "/" . $name_database);
			
			$this->_name_database = $name_database;
			
			return true;
		}
		return false;
	}
	
	/**
	 * Supprime une base de données.
	 * @param {$name_database} : Nom de la base de données à supprimer.
     * @return : Retourne True si la suppression a réussi sinon False.
	 */
	public function DropDatabase()
	{
		if($this->_name_database != "")
		{
			self::RemoveDir($this->_path_databases . "/" . $this->_name_database);
			$this->_name_database = "";
			return true;
		}
		return false;
	}
	
	/**
	 * Création d'une table dans la base de données si elle n'existe pas.
	 * @param {$name_table} : Nom de la table à créer.
     * @return : Retourne la table créé sinon False.
	 */
	public function CreateTable($name_table, $fields = array())
	{
		if($this->_name_database != ""){
			$table = new DxmlTable($this->_path_databases, $this->_name_database, $name_table);
			if($table->Create($fields))
				$table->Commit();
			return $table;
		}
		return false;
	}
	
	/**
	 * Récupère une table dans la base de données.
	 * @param {$name_table} : Nom de la table.
     * @return : Retourne la table sinon False.
	 */
	public function GetTable($name_table)
	{
		$path = $this->_path_databases . "/" . $this->_name_database . "/" . self::FormatName($name_table) . ".xml";
		
		if(file_exists($path))
			return new DxmlTable($this->_path_databases, $this->_name_database, $name_table);
		
		return false;
	}
	
	/**
	 * Retourne la liste des tables de la base de données.
     * @return : Retourne un tableau contenant la liste des tables sinon False.
	 */
	public function GetTablesName()
	{
		if(file_exists($this->_path_databases . "/" . $this->_name_database))
		{
			$list = array();
			$files = scandir($this->_path_databases . "/" . $this->_name_database);
			
			foreach($files as $file)
			{
				if($file != "." and $file != "..")
					$list[] = str_replace(".xml", "", $file); // On supprime l'extention du fichier.
			}
			
			return $list;
		}
		return false;
	}
	
	/**
	 * Join deux table entre elle.
	 * @param {$name_table_A} : Nom de la table A.
	 * @param {$column_table_A} : Colonne de la table A.
	 * @param {$name_table_B} : Nom de la table B.
	 * @param {$column_table_B} : Colonne de la table B.
     * @return : Retourne un tableau contenant les données de la jointure.
	 */
	public function InnerJoin($name_table_A, $column_table_A, $name_table_B, $column_table_B)
	{
		$name_table_A = self::FormatName($name_table_A);
		$name_table_B = self::FormatName($name_table_B);
		$column_table_A = self::FormatName($column_table_A);
		$column_table_B = self::FormatName($column_table_B);
		
		$tableA = $this->GetTable($name_table_A)->SelectAll();
		$tableB = $this->GetTable($name_table_B)->SelectAll();
		
		$data = array();
		foreach($tableA as $ligneA)
		{
			foreach($tableB as $ligneB)
			{
				if($ligneA[$column_table_A] == $ligneB[$column_table_B])
				{
					$new_ligne = array();
					
					foreach($ligneA as $keyA => $valueA)
						$new_ligne[$keyA . "_" . $name_table_A] = $valueA;
						
					foreach($ligneB as $keyB => $valueB)
						$new_ligne[$keyB . "_" . $name_table_B] = $valueB;
						
					$data[] = $new_ligne;
				}
			}
		}
		
		if(!empty($data))
		{
			// Création de la table
			$table = new DxmlTable($this->_path_databases, $this->_name_database, $name_table_A . '-' . $name_table_B);
			$fields = array();
			$ligne = $data[0];
			
			foreach($ligne as $key => $value)
				$fields[] = $key;
			
			$table->Create($fields);
			
			// Insertion des données
			foreach($data as $row)
				$table->insert($row);
			
			return $table;
		}
			
		return false;
	}
	
	/**
	 * Supprime la table de la base de données.
	 * @param {$name_table} : Nom de la table à supprimer.
     * @return : Retourne True si la table a été supprimé sinon False.
	 */
	public function DropTable($name_table)
	{
		return $this->GetTable(self::FormatName($name_table))->Drop();
	}
	
	
	/**
	 * Format les noms pour éviter les erreurs.
	 * @param {$name} : Nom à formater.
     * @return : Nom formaté.
	 */
	static public function FormatName($name)
	{
		$name = str_replace(
			array(' ', '.', '\\', '/', ':', '*', '!', '?', '"', '<', '>', '|', '`', '\''),
			'_',
			$name
		);
		
		$name = str_replace(
			array('@','à','â','ä','á','ã','å','î','ï','ì','í','ô','ö','ò','ó','õ','ø','ù','û','ü','ú','é','è','ê','ë','ç','ÿ','ñ','ý'),
			array('a','a','a','a','a','a','a','i','i','i','i','o','o','o','o','o','o','u','u','u','u','e','e','e','e','c','y','n','y'),
			$name
		);
		$name = str_replace(
			array('À','Â','Ä','Á','Ã','Å','Î','Ï','Ì','Í','Ô','Ö','Ò','Ó','Õ','Ø','Ù','Û','Ü','Ú','É','È','Ê','Ë','Ç','Ÿ','Ñ','Ý'),
			array('A','A','A','A','A','A','I','I','I','I','O','O','O','O','O','O','U','U','U','U','E','E','E','E','C','Y','N','Y'),
			$name
		);
		
		return $name;
	}
	
	
	// Supprime un fichier ou un dossier.
	private static function RemoveDir($path) { 
		$files = array_diff(scandir($path), array('.','..'));
		
		foreach ($files as $file) { 
			(is_dir($path . "/" .$file)) ? RemoveDir($path . "/" .$file) : unlink($path . "/" .$file); 
		} 
		
		return rmdir($path); 
	}
}
?>