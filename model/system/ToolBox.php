<?php
require_once(ABSPATH . "model/system/LogManager.php");

// =========================================================================
// ==== Boite à outils du site. Fonctions statique a utiliser au besoin ====
// =========================================================================

class ToolBox
{
	// Regarde si le fichier existe à partir d'une URL et non pas du chemin depuis le répertoire courant.
	static public function UrlExists(string $url) : bool
	{
		try {
			if(file_Get_contents($url))
				return true;
			return false;
		} catch (Exception $e) {
			LogManager::AddLine("Error : Error with the URL for the ToolBox::UrlExists() function.");
		}
	}
	
	// Regarde si le fichier existe à partir d'une URL et non pas du chemin depuis le répertoire courant.
	static public function RemoveFile(string $path) : bool
	{
		if(file_exists($path))
		{
			unlink($path);
			return true;
		}
		
		return false;
	}
	
	// Supprime un dossier et tout son contenu, retourne true ou false suivant le succès de l'opération.
	static public function DeleteDirectory(string $folder) : bool
	{
		try {
			if(!self::DeleteAllInDirectory($folder))
				return false;
			if(!rmdir($folder))
				return false;
			return true;
		} catch (Exception $e) {
			LogManager::AddLine("Error : Error with the function rmdir() in the ToolBox::DeleteDirectory() function.");
		}
	}
	
	// Supprimer tous les fichiers et dossiers contenus dans le dossier passé en paramètre.
	static public function DeleteAllInDirectory(string $folder) : bool
	{
		try {
			if($ouverture = opendir($folder)) {
				while(false !== ($fichier = readdir($ouverture))) {
					if($fichier != "." and $fichier != "..") {
						if(is_dir($folder."/".$fichier)) {
							if(self::DeleteAllFilesInDirectory($folder."/".$fichier))
								rmdir($folder."/".$fichier);
							else
								return false;
						} else {
							if(!unlink($folder."/".$fichier))
								return false;
						}
					}
				}
				closedir($ouverture);
				return true;
			} else {
				return false;
			}
		} catch (Exception $e) {
			LogManager::AddLine("Error : Error in the ToolBox::DeleteAllInDirectory() function.");
		}
		
	}
	
	// Supprimer tous les fichiers contenus dans le dossier passé en paramètre (supprime aussi les fichiers des sous-dossiers).
	static public function DeleteAllFilesInDirectory(string $folder) : bool
	{
		try {
			if($ouverture = opendir($folder)) {
				while(false !== ($fichier = readdir($ouverture))) {
					if($fichier != "." and $fichier != "..") {
						if(is_dir($folder."/".$fichier)) {
							if(!self::DeleteAllFilesInDirectory($folder."/".$fichier))
								return false;
						} else {
							if(!unlink($folder."/".$fichier))
								return false;
						}
					}
				}
				closedir($ouverture);
				return true;
			} else {
				return false;
			}
		} catch (Exception $e) {
			LogManager::AddLine("Error : Error in the ToolBox::DeleteAllFilesInDirectory() function.");
		}
	}
	
	// Regarde si le chemin passé en paramètre est un dossier, et s'il n'existe pas, il le créé.
	static public function IsDirectoryOrCreateIt(string $path) : bool
	{
		try {
			if(!is_dir($path))
				if(!mkdir($path, 0777, true))
					return false;
			return true;
		} catch (Exception $e) {
			LogManager::AddLine("Error : Error in the ToolBox::IsDirectoryOrCreateIt() function.");
		}
	}
	
	// Récursive pour copier un dossier ainsi que son contenu. Retourne le nombre de fichier copié sinon FALSE.
	static public function CopyDirectory(string $origine, string $destination)
	{
		try {
			if($test = scandir($origine)) {
				$nbr_files = 0;
				foreach($test as $val) {
					if($val != "." and $val != "..") {
						if(is_dir($origine."/".$val)) {
							self::IsDirectoryOrCreateIt($destination."/".$val);
							$nbr_files += CopyDirectory($origine."/".$val, $destination."/".$val);
						} else {
							if(copy($origine."/".$val, $destination."/".$val)) {
								$nbr_files ++;
							} else {
								if(!file_exists($origine."/".$val)) {
									throw new exception($origine."/".$val." doesn't exist!");
								}
							}
						}
					}
				}
				return $nbr_files;
			} else {
				return false;
			}
		} catch (Exception $e) {
			LogManager::AddLine("Error : Error in the ToolBox::CopyDirectory() function.");
		}
	}
	
	// Déplace un dossiers et ses fichiers. Retourne le nombre de fichiers déplacés sinon FALSE.
	static public function MoveDirectory(string $origine, string $destination)
	{
		$nbr_files = self::CopyDirectory($origine, $destination);

		if($nbr_files !== false)
		{
			if(self::DeleteDirectory($origine))
				return $nbr_files;
		}

		return false;
	}
	
	// Retourne un tableau contenant tous les chemins vers les fichiers contenus dans le chemin du dossier passé en paramètre.
	static public function GetAllPathFilesFromDir(string $pathDir) : array
	{
		try {
			$out = array();
			$dir = opendir(realpath($pathDir));

			while($file = readdir($dir))
			{
				if(($file!=".") && ($file!="..")) 
					$out[] = realpath($pathDir.'/'.$file);
			}

			closedir($dir);
			return $out;
		} catch (Exception $e) {
			LogManager::AddLine("Error : Error in the ToolBox::GetAllPathFilesFromDir() function.");
		}
	}
	
	// Récupère le nom de la page courante dans l'URL (Ex: index.php).
	static public function GetCurrentPageName() : string
	{
		return substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
	}
	
	// Récupère l'URL complète de la page courante.
	static public function GetCurrentUrl() : string
	{
		$pageURL = 'http';

		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
			$pageURL .= "://";

		if ($_SERVER["SERVER_PORT"] != "80")
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		else
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		
		return $pageURL;
	}		
		
	// Format une date à partir :
	//   -  d'un timestamp (nb Entier de sec depuis la date UNIX),
	//   -  d'une date au format : YYYY/MM/DD 
	//   -  d'une date au format DateTime SQL : YYYY-MM-DD hh:mm:ss
	//   -  d'une date au format ISO : YYYY-MM-DDThh:mm:ss+00000 
	// Format de sortie : 5 Février 2012 ( à 06h34)
	static public function FormatDate($time = null, $displayHour = true) : string
	{
		if($time == null)
			$time = time();

		if(gettype($time) == "object" && get_class($time) == "DateTime")
			$time = $time->format("Y-m-d H:i:s");

		if(preg_match("#^\d{5,}$#", "" . $time)) {
			$jour = date("j", intval($time));
			$mois = date("m", intval($time));
			$annee = date("Y", intval($time));
			$heure = date("G", intval($time));
			$minute = date("i", intval($time));
		}
		else if(preg_match("#^\d{4}[/-]\d{2}[/-]\d{2}([T ]\d{2}:\d{2}:\d{2})?#", "" . $time))
		{
			$annee = substr($time, 0, 4);
			$mois = substr($time, 5, 2);
			$jour = substr($time, 8, 2);
			$heure = substr($time, 11, 2);
			$minute = substr($time, 14, 2);
		}

		if(intval($jour)!=0 and intval($mois)!=0 and intval($annee)!=0)
		{
			switch($mois) {
				case '01': $mois = "Janvier"; break;
				case '02': $mois = "Février"; break;
				case '03': $mois = "Mars"; break;
				case '04': $mois = "Avril"; break;
				case '05': $mois = "Mai"; break;
				case '06': $mois = "Juin"; break;
				case '07': $mois = "Juillet"; break;
				case '08': $mois = "Août"; break;
				case '09': $mois = "Septembre"; break;
				case '10': $mois = "Octobre"; break;
				case '11': $mois = "Novembre"; break;
				case '12': $mois = "Décembre"; break;
				default: $mois = "Mois inconnu";
			}

			if(substr($jour, 0, 1) == "0") 
				$jour = substr($jour, 1, 1);
			
			if($displayHour && intval($heure) !== false && intval($minute) !== false)
				return $jour." ".$mois." ".$annee." à ".$heure."h".$minute;
			
			return $jour." ".$mois." ".$annee;
		}

		return false;
	}
	
	// Converti la balise HTML <br /> en \n
	static public function Br2nl(string $string) : string
	{
		return preg_replace("/\<br(\s*)?\/?\>/i", "\n", $string);
	}
	
	// Supprime les accents d'une chaine de caractères. (Work with Unicode)
	static public function StripAccents(string $string) : string
	{
		return str_replace(
			array('à','â','ä','á','ã','å','î','ï','ì','í','ô','ö','ò','ó','õ','ø','ù','û','ü','ú','é','è','ê','ë','ç','ý','ÿ','ñ','À','Á','Â','Ã','Ä','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ñ','Ò','Ó','Ô','Õ','Ö','Ù','Ú','Û','Ü','Ý'),
			array('a','a','a','a','a','a','i','i','i','i','o','o','o','o','o','o','u','u','u','u','e','e','e','e','c','y','y','n','A','A','A','A','A','C','E','E','E','E','I','I','I','I','N','O','O','O','O','O','U','U','U','U','Y'),
			$string
		);
	}
	
	// Calcul l'age d'une personne grâce à la date de naissance. (Date américaine)
	static public function Age(string $birthday) : int // Format YYYY-MM-DD
	{
		list($annee, $mois, $jour) = explode('-', $birthday);
		$annee = intval($annee);
		$mois = intval($mois);
		$jour = intval($jour);

		$today = array(
			'jour' => intval(date('d')),
			'mois' => intval(date('m')),
			'annee' => intval(date('Y'))
		);
		
		$age = $today['annee'] - $annee;
		
		if($today['mois'] <= $mois) 
		{
			if($mois == $today['mois'])
			{
				if($today['jour'] < $jour)
					$age--;
			}
			else
				$age--;
		}

		return $age;
	}

	// Calcul l'age d'une personne grâce à la date de naissance. (Date américaine)
	static public function StringToBool(string $string) : bool
	{
		$string = mb_strtolower($string);
		
		if($string == "true" || $string == "1")
			return true;

		return false;
	}

	// Calcul l'age d'une personne grâce à la date de naissance. (Date américaine)
	static public function BoolToString(bool $bool) : string
	{
		if($bool)
			return "true";

		return "false";
	}

	// Recherche des valeurs dans un tableau de valeurs.
	static public function SearchInArray(array $searchArray, array $values, $allValues = false) : bool
	{
		if($allValues)
		{
			$result = true;

			foreach($values as $value)
				$result = $result & in_array($value, $searchArray);

			return $result;
		}
		else
		{
			foreach($searchArray as $item)
			{
				if(in_array($item, $values))
					return true;
			}

			return false;
		}
	}
	
	// Génère un mot de passe
	static public function GeneratePassword($nbChar = 8)
	{
		if($nbChar >= 4)
		{
			$chars = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
						   'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
						   '0','1','2','3','4','5','6','7','8','9',
						   '!','?','#');

			$nbChars = count($chars) - 1;
			$pass = "";

			for($i = 0; $i < $nbChar; $i ++)
				$pass .= $chars[random_int(0, $nbChars)];

			return $pass;
		}
		else
			return false;
	}

	// Génère un mot de passe
	static public function GenerateRandomToken()
	{
		return hash("sha512", random_bytes(256));
	}
}
?>