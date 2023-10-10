<?php
namespace System;
use Exception;
use PDO;
use PDOStatement;
use System\Erreur;

/**
 * Tool to manage database connection and queries.
 */
class Database
{
	// ==== ATTRIBUTS ====
	/**
	 * @var PDO|null $_bdd Object to manage database.
	 */
	private ?PDO $_bdd = null;
	
	/**
	 * @var array $_error Last error.
	 */
	private array $_error;

	// ==== CONSTRUCTOR ====
	public function __construct()
	{
		try {
			$this->_bdd = new PDO(
				'mysql:host=' . DB_HOST . '; dbname=' . DB_NAME,
				DB_USER,
				DB_PASSWORD,
				[PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '" . DB_CHARSET . "'"]
			);
		} catch(Exception $e) {
			var_dump($e->getMessage());
			new Erreur($e);
		}
	}
	
	// == GETTERS ==
	/**
	 * Get an instance of database.
	 * 
	 * @return PDO|null Instance of the database.
	 */
	public function getDatabase(): PDO|null
	{
		return $this->_bdd;
	}

	/**
	 * Get the last error.
	 * 
	 * @return array The last error.
	 */
	public function getError(): array
	{
		return $this->_error;
	}
	
	// == OTHER METHODS ==
	/**
	 * Execute a SQL query.
	 * 
	 * @param string Query to send.
	 * @param array List of data to send into the query.
	 * @return PDOStatement|false Result of the query, or False if an error occured.
	 */
	public function query(string $requete, array $variables = []): PDOStatement|false
	{
		if($this->_bdd !== null && !empty($requete)) {
			// Prepare variables
			foreach ($variables as &$variable) {
				if (is_bool($variable)) {
					$variable = (int)$variable;
				}
			}

			try {
				$req = $this->_bdd->prepare($requete);
				$req->execute($variables);

				if ($req->errorCode() === '00000') {
					return $req;
				} else {
					$this->_error = $req->errorInfo();
				}
			} catch(Exception $e) {
				var_dump($e->getMessage());
				new Erreur($e);
			}
		}

		return false;
	}
	
	/**
	 * Insert a new row in a table and return the created ID.
	 * 
	 * @param string $tableName Table into insert data.
	 * @param array $variables Associative array with columns (keys) and data (values).
	 * @return int|false Return the created ID if the process succeed, else return False.
	 */
	public function insert(string $tableName, array $variables): int|false
	{
		$requete = "INSERT INTO `$tableName` (";

		foreach ($variables as $key => $value) {
			$requete .= "`$key`,";
		}

		$requete = substr($requete, 0, strlen($requete) - 1);
		$requete .= ') VALUES (';

		foreach ($variables as $key => $value) {
			$requete .= ":$key,";
		}

		$requete = substr($requete , 0, strlen($requete) - 1);
		$requete .= ');';

		$result = $this->query($requete, $variables);

		if ($result !== false) {
			if($result->errorCode() === '00000') {
				return $this->_bdd->lastInsertId();
			} else {
				$this->_error = $result->errorInfo();
			}
		}

		return false;
	}

	/**
	 * Update a row into a table.
	 * 
	 * @param string $tableName Name of the table where thr row is.
	 * @param string $idColumnName Name of the column ID to find the row.
	 * @param int|string $idValue Value of the ID to modify.
	 * @param array $variables List of values to update. ID cannot be modify.
	 * @return bool Return True if the process succeed, else False.
	 */
	public function update(string $tableName, string $idColumnName, int|string $idValue, array $variables): bool
	{
		$requete = "UPDATE `$tableName` SET";

		foreach ($variables as $key => $value) {
			$requete .= "`$key`=:$key,";
		}

		$requete = substr($requete , 0, strlen($requete) - 1);
		$requete .= " WHERE `$idColumnName`=:$idColumnName;";

		$variables[$idColumnName] = $idValue;
		
		$result = $this->query($requete, $variables);

		if ($result !== false) {
			if($result->errorCode() === '00000') {
				return true;
			} else {
				$this->_error = $result->errorInfo();
			}
		}

		return false;
	}

	/**
	 * Remove a row into a table.
	 * 
	 * @param string $tableName Name of the table where the row is.
	 * @param string $idColumnName Name of the column ID to find the row.
	 * @param int|string $idValue Value of the ID to remove.
	 * @return bool Return True if the process succeed, else False.
	 */
	public function delete(string $tableName, string $idColumnName, int|string $idValue): bool
	{
		$requete = "DELETE FROM `$tableName` WHERE `$idColumnName`=:value;";
		$variables = ['value' => $idValue];

		$result = $this->query($requete, $variables);

		if ($result !== false) {
			if($result->errorCode() === '00000') {
				return true;
			} else {
				$this->_error = $result->errorInfo();
			}
		}

		return false;
	}
	
	/**
	 * Generate the SQL code corresponding to the database with all data.
	 * 
	 * @param string $filePath Path to the output file.
	 * @param array $excludedTables List of tables that does not be into the file.
	 * @return bool|int Return True if the process succeed, else return the error code. Refert to file_put_contents() function.
	 */
	public function saveDatabase(string $filePath, array $excludedTables = array()): bool|int
	{
		$result = $this->_bdd->query('SHOW TABLES');
		$tables = [];

		while ($row = $result->fetch()) {
			if (!in_array($row[0], $excludedTables)) {
				$tables[] = $row[0];
			}
		}
		
		$result->closeCursor();

		// $code will contains all the script
		// We disable constraints on foreign keys.
		$code = "-- BDD " . DB_NAME . " sauvegarde le " . date("d/m/Y H:i:s");
		$code .= "\n\n";
		$code .= "SET FOREIGN_KEY_CHECKS=0;\n";
		$code .= "SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\";\n";
		$code .= "SET AUTOCOMMIT=0;\n";
		$code .= "START TRANSACTION;";
		$code .= "\n\n";

		foreach ($tables as $table) {			
			// We drop the current table. To be sure.
			$code .= "DROP TABLE IF EXISTS `$table`;\n";

			// We generate table structure.
			$result = $this->_bdd->query("SHOW CREATE TABLE $table");
			$retour = $result->fetch(PDO::FETCH_ASSOC);
			$code .= "{$retour['Create Table']};\n\n";
			$result->closeCursor();
			
			// We write all data contains into the table.
			$result = $this->_bdd->query("SELECT * FROM $table");

			while ($row = $result->fetch(PDO::FETCH_ASSOC)) {				
				$code .= "INSERT INTO `$table` VALUES(";

				// And for each row, we take all columns.
				foreach ($row as $fieldValue) {
					// On purifie la valeur du champ
					$fieldValue = addslashes($fieldValue);
					$fieldValue = preg_replace("/\r\n/", "\\r\\n", $fieldValue);

					if (is_null($fieldValue)) {
						$code .= 'NULL';
					} else {
						$code .= '"' . $fieldValue . '", ';
					}
				}

				// We remove the last ',' at the and of the line.
				$code = mb_substr($code, 0, -2) . ");\n";
			}

			$result->closeCursor();
			$code .= "\n";
		}

		// To finish we enable constraints on foreign keys.
		$code .= "SET FOREIGN_KEY_CHECKS=1;\n";
		$code .= "COMMIT;";
		
		// And save it into a file.
		return file_put_contents($filePath, $code);
	}
}