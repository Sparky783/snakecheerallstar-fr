<?php
namespace TM\ApplicationBundle\Service\DatabaseXML;

include_once("DatabaseXML.php");

class DxmlTable
{
	// ATTRIBUTS
	private $_path_databases = "";	// Chemin d'accès au base de données. 
	private $_name_database = "";	// Nom de la base de données en cours.
	private $_name_table = "";		// Nom de la table
	private $_xml_file = null;		// Fichier XML associé.
	private $_full_path = "";		// Chemin complet vers le fichier.
	private $_is_exists = false;	// Si le fichier existe ou non.
	private $_schema = array();		// Schema de la table.
	
	
	/**
	 * Instancie un objet DxmlTable.
	 */
	public function __construct($path_database, $name_database, $name_table)
	{
		$this->_path_databases = $path_database;
		$this->_name_database = $name_database;
		$this->_name_table = DatabaseXML::FormatName($name_table);
		$this->_full_path = $this->_path_databases . "/" . $this->_name_database . "/" . $this->_name_table . ".xml";
		
		$this->_xml_file = new \DOMDocument("1.0", "UTF-8");
		$this->_xml_file->preserveWhiteSpace = false;
		$this->_xml_file->formatOutput = true;
		
		if(file_exists($this->_full_path)) {
			$this->_xml_file->load($this->_full_path);
			
			$schema_node = $this->_xml_file->getElementsByTagName('schema_table')[0];
			foreach($schema_node->childNodes as $node)
				$this->_schema[] = $node->nodeName;
			
			$this->_is_exists = true;
		}
	}
	
	/**
	 * Retourne le nom de la table.
     * @return : Retourne le nom de la table.
	 */
	public function GetName()
	{
		return $this->_name_table;
	}
	
	/**
	 * Retourne le tableau représentant la structure de la table.
     * @return : Retourne le tableau représentant la structure de la table.
	 */
	public function GetSchema()
	{
		return $this->_schema;
	}
	 
	/**
	 * Crée le fichier XML de la table avec les champs associés.
	 * @param {$fields} : Liste des champs de la table.
     * @return : Retourne True si une nouvelle table est créé sinon False.
	 */
	public function Create($fields = array())
	{
		if(!$this->_is_exists)
		{
			// Check les champs pour bloquer les champs réservés au systeme
			foreach($fields as $field){
				if(in_array(DatabaseXML::FormatName($field), $this->reservedTags()))
					exit("Le champ " . $field . " est réservé par le système.");
			}
			
			// Création de la table.
			$table = $this->_xml_file->createElement($this->_name_table);
			
			// Ajoute les attributs lié à la table
			$attribute = $this->_xml_file->createAttribute("nextId");
			$attribute->nodeValue = 1;
			$table->appendChild($attribute);
			
			// Création du schema
			$fields[] = "id";
			$schema_child = $this->_xml_file->createElement("schema_table");
			
			foreach($fields as $field){
				$field = DatabaseXML::FormatName($field);
				$element = $this->_xml_file->createElement($field);
				$schema_child->appendChild($element);
				$this->_schema[] = $field;
			}
			
			$table->appendChild($schema_child);
			
			// Préparation à l'accueil du contenu de la table
			$content_table = $this->_xml_file->createElement("content_table");
			$table->appendChild($content_table);
			
			// Création du fichier
			$this->_xml_file->appendChild($table);
			$this->_is_exists = true;
			
			return true;
		}
		return false;
	}
	
	/**
	 * Ajoute une nouvelle ligne dans la table.
	 * @param {$fields} : Liste des champs avec les valeurs à ajouter. Ex : array($field => $value);
     * @return : Retourne l'ID si l'ajout a réussi sinon False.
	 */
	public function Insert($fields)
	{
		if($this->_is_exists)
		{
			// Check les champs pour bloquer les champs réservés au systeme
			foreach($fields as $key => $value){
				if(in_array(DatabaseXML::FormatName($key), $this->reservedTags()))
					exit("Le champ " . $key . " est réservé par le système.");
			}
			
			// Récupèration de la table et création de la ligne
			$table = $this->_xml_file->childNodes[0];
			$ligne = $this->_xml_file->createElement("data_table");
			
			// Affecte et incrémente l'Id
			$id = intval($table->attributes['nextId']->nodeValue);
			
			$attribute = $this->_xml_file->createAttribute("id");
			$attribute->nodeValue = $id;
			$ligne->appendChild($attribute);
			$fields['id'] = $id;
			
			$id ++;
			$table->setAttribute('nextId', $id);
			
			// Insert les données dans la ligne
			foreach($this->_schema as $schema){
				$element = $this->_xml_file->createElement($schema);	// Fonctionnement avec des noeuds.
				
				if(isset($fields[$schema]))
					$element->nodeValue = $fields[$schema];
				
				$ligne->appendChild($element);
			}
			
			$content = $this->_xml_file->getElementsByTagName('content_table')[0]->appendChild($ligne);
			
			return $id - 1;
		}
		return false;
	}
	
	/**
	 * Modifie une ligne dans la table.
	 * @param {$id} : ID de la ligne à modifier.
	 * @param {$fields} : Liste des champs avec les valeurs à ajouter. Ex : array($field => $value);
     * @return : Retourne True si l'ajout a réussi sinon False.
	 */
	public function Update($id, $fields)
	{
		if($this->_is_exists)
		{
			// Check les champs pour bloquer les champs réservés au systeme
			foreach($fields as $key => $value){
				if(in_array(DatabaseXML::FormatName($key), $this->reservedTags()))
					exit("Le champ " . $key . " est réservé par le système.");
			}
			
			// Récupération de la ligne à modifier
			$ligne = $this->getElementById($id);
			
			// Mise à jour de la ligne
			foreach($fields as $key => $value){
				$key = DatabaseXML::FormatName($key);
				
				foreach($ligne->childNodes as $node){
					if($node->nodeName == $key)
						$node->nodeValue = $value;
				}
			}
			
			return true;
		}
		return false;
	}
	
	/**
	 * Supprime une ligne dans la table.
	 * @param {$id} : ID de la ligne à supprimer.
     * @return : Retourne True si la suppression a réussi sinon False.
	 */
	public function Delete($id)
	{
		if($this->_is_exists)
		{
			$ligne = $this->getElementById($id);
			
			if($ligne != null)
			{
				$this->_xml_file->getElementsByTagName('content_table')[0]->removeChild($ligne);
				
				return true;
			}
			return false;
		}
		return false;
	}
	
	/**
	 * Récupère une ligne dans la table.
	 * @param {$id} : ID de la ligne à récupérer.
     * @return : Tableau contenant les données de la ligne.
	 * Ex: array($colonne => $valeur);
	 * Retourne False si erreur.
	 */
	public function Select($id)
	{
		if($this->_is_exists)
		{
			$ligne = $this->getElementById($id);
			$data = array();
			
			foreach($ligne->childNodes as $node) {
				if($node->nodeName != "id")
					$data[$node->nodeName] = $node->nodeValue;
			}
			
			return $data;
		}
		return false;
	}
	
	/**
	 * Récupère une colonne dans la table.
	 * @param {$column} : Colonne de la table à récupérer.
     * @return : Tableau contenant les données de la colonne.
	 * Ex: array($id => $valeur);
	 * Retourne False si erreur.
	 */
	public function SelectColumn($column)
	{
		if($this->_is_exists)
		{
			$content = $this->_xml_file->getElementsByTagName('content_table')[0];
			$data = array();
			
			foreach($content->childNodes as $ligne){
				foreach($ligne->childNodes as $node){
					if($node->nodeName == $column)
						$data[$ligne->attributes['id']->nodeValue] = $node->nodeValue;
				}
			}
			
			return $data;
		}
		return false;
	}
	
	/**
	 * Récupère toutes les lignes de la table.
	 * @param {$fromColumn} : Si à True, tableau de sortie orienté colonne (par defaut) sinon orienté ligne.
     * @return : Tableau contenant les données toutes les lignes de la table.
	 * Ex : array("id" => array($colonne => $valeur)); - Orrienté ligne
	 * ou
	 * Ex : array("colonne" => array($id => $valeur)); - Orrienté colonne
	 * Retourne False si erreur.
	 */
	public function SelectAll($fromColumn = false)
	{
		if($this->_is_exists)
		{
			$content = $this->_xml_file->getElementsByTagName('content_table')[0];
			$data = array();
			
			if($fromColumn)
			{
				foreach($this->_schema as $column)
					$data[$column] = array();
						
				foreach($content->childNodes as $ligne){
					foreach($ligne->childNodes as $column)
						$data[$column->nodeName][$ligne->attributes['id']->nodeValue] = $column->nodeValue;
				}
			}
			else
			{
				foreach($content->childNodes as $ligne){
					$data[$ligne->attributes['id']->nodeValue] = array();
					
					foreach($ligne->childNodes as $column)
						$data[$ligne->attributes['id']->nodeValue][$column->nodeName] = $column->nodeValue;
				}
			}
			
			return $data;
		}
		return false;
	}
	
	/**
	 * Récupère toutes les lignes de la table correspondant à la condition $column $operator $value. Ex: "colonne" = 64.
	 * @param {$column} : Colonne à tester.
	 * @param {$operator} : Opérateur de teste ('=', '!=', '>', '<', '>=', '<=').
	 * @param {$value} : Valeur d'opération.
     * @return : Tableau contenant les données toutes des lignes respectant la condition. Retourne False si erreur.
	 */
	public function SelectWhere($column, $operator, $value)
	{
		if($this->_is_exists)
		{
			$content = $this->_xml_file->getElementsByTagName('content_table')[0];
			$data = array();
			
			foreach($content->childNodes as $ligne)
			{
				foreach($ligne->childNodes as $node)
				{
					if($node->nodeName == $column)
					{
						if (($operator == "=" && $node->nodeValue == ($value . "")) ||
							($operator == "!=" && $node->nodeValue != ($value . "")) ||
							($operator == ">" && floatval($node->nodeValue) > $value) ||
							($operator == ">=" && floatval($node->nodeValue) >= $value) ||
							($operator == "<" && floatval($node->nodeValue) < $value) ||
							($operator == "<=" && floatval($node->nodeValue) <= $value)
						){
							$data_ligne = array();
							
							foreach($ligne->childNodes as $val)
								$data_ligne[$val->nodeName] = $val->nodeValue;
							
							$data[$ligne->attributes['id']->nodeValue] = $data_ligne;		
						}
					}
				}
			}
			
			return $data;
		}
		return false;
	}
	
	/**
	 * Sauvegarde la table dans le fichier XML.
     * @return : Retourne True si l'enregistrement a réussi sinon False.
	 */
	public function Commit()
	{
		if($this->_is_exists)
		{
			$this->_xml_file->save($this->_full_path);
			
			return true;
		}
		return false;
	}
	
	/**
	 * Sauvegarde la table dans le fichier XML. Reprend la fonction Commit()
     * @return : Retourne True si l'enregistrement a réussi sinon False.
	 */
	public function Save()
	{
		return $this->Commit();
	}
	
	/**
	 * Retourne le dernier ID créé.
     * @return : Retourne le dernier ID créé sinon False en cas d'erreur.
	 */
	public function LastId()
	{
		if($this->_is_exists)
			return $this->_xml_file->childNodes[0]->attributes['nextId']->nodeValue - 1;
		
		return false;
	}
	
	/**
	 * Vide le contenu de la table.
     * @return : Retourne True si la table a été vidé sinon False.
	 */
	public function Truncate()
	{
		if($this->_is_exists)
		{
			$old_content_node = $this->_xml_file->getElementsByTagName('content_table')[0];
			$new_content_node = $this->_xml_file->createElement('content_table');
			$old_content_node->tuteurNode->replaceChild($new_content_node, $old_content_node);
			
			return true;
		}
		return false;
	}
	
	/**
	 * Vide le contenu de la table.
     * @return : Retourne True si la table a été vidé sinon False.
	 */
	public function Clear()
	{
		return $this->Truncate();
	}
	
	/**
	 * Supprime la table de la base de données.
     * @return : Retourne True si la table a été supprimé sinon False.
	 */
	public function Drop()
	{
		if($this->_is_exists)
		{
			unlink($this->_full_path);
			return true;
		}
		return false;
	}
	
	
	// Cherche un élément grâce à son ID.
	private function getElementById($id)
	{
		$xpath = new \DOMXPath($this->_xml_file);
		return $xpath->query("//*[@id='$id']")->item(0);
	}
	
	// Retourne un tableau contenant les tags réservé.
	private function reservedTags()
	{
		return array(
			"schema_table",
			"content_table",
			"data_table",
			"id"
		);
	}
}
?>