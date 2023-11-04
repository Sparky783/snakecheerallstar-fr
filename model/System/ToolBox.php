<?php
namespace System;

use DateTime;
use Exception;
use ErrorException;
use InvalidArgumentException;

/**
 * This class is use for all generic stuff.
 */
class ToolBox
{
	// ==== OTHER METHODS ====
	/**
	 * Check if the URL file exists. 
	 * 
	 * @param string $url URL of the file.
	 * @return bool Return True if the file exist, else False.
	 */
	public static function urlExists(string $url): bool
	{
		try {
			if (file_Get_contents($url)) {
				return true;
			}

			return false;
		} catch (Exception $e) {
			LogManager::addLine('Error : Error with the URL for the ToolBox::urlExists() function.');
		}

		return false;
	}

	/**
	 * Remove a file from the server.
	 * Avoid warning if the file does not exist.
	 * 
	 * @param string $filePath Path to th e file to remove.
	 * @return bool Return True if the file is removed, else False.
	 */
	public static function removeFile(string $filePath): bool
	{
		if (file_exists($filePath)) {
			unlink($filePath);
			return true;
		}

		return false;
	}
	
	/**
	 * Remove a folder and all its content.
	 * 
	 * @param string $folder Path of the folder to remove.
	 * @return bool Return True if the folder is removed, else False.
	 */
	public static function removeDirectory(string $folderPath): bool
	{
		try {
			if (!self::removeDirectoryContent($folderPath)) {
				return false;
			}

			if (!rmdir($folderPath)) {
				return false;
			}

			return true;
		} catch (Exception $e) {
			LogManager::addLine('Error : Error with the function rmdir() in the ToolBox::RemoveDirectory() function.');
		}

		return false;
	}
	
	/**
	 * Remove all files and folders in directory.
	 * 
	 * @param string $folderPath Path to the directory to clear.
	 * @return bool return True if the folder is clear, else False.
	 */
	public static function removeDirectoryContent(string $folderPath): bool
	{
		try {
			if ($ouverture = opendir($folderPath)) {
				while (false !== ($file = readdir($ouverture))) {
					if ($file != '.' and $file != '..') {
						if (is_dir($folderPath . '/' . $file)) {
							if (self::removeDirectoryContent($folderPath . '/' . $file)) {
								rmdir($folderPath . '/' . $file);
							} else {
								return false;
							}
						} else {
							if (!unlink($folderPath . '/' . $file)) {
								return false;
							}
						}
					}
				}

				closedir($ouverture);
				return true;
			} else {
				return false;
			}
		} catch (Exception $e) {
			LogManager::addLine('Error : Error in the ToolBox::removeDirectoryContent() function.');
		}

		return false;
	}
	
	/**
	 * Remove all files in directory (file only).
	 * 
	 * @param string $folderPath Path to the folder to clear.
	 * @param bool Define if the sub-folders must be clear.
	 * @return bool Return True if the directory is clear, else False.
	 */
	public static function removeDirectoryFiles(string $folderPath, bool $subFolders): bool
	{
		try {
			if ($ouverture = opendir($folderPath)) {
				while (false !== ($file = readdir($ouverture))) {
					if ($file !== '.' and $file !== '..') {
						if (is_dir($file . '/' . $file) && $subFolders) {
							if(!self::removeDirectoryFiles($folderPath . '/' . $file, $subFolders)) {
								return false;
							}
						} else {
							if(!unlink($folderPath . '/' . $file)) {
								return false;
							}
						}
					}
				}

				closedir($ouverture);
				return true;
			} else {
				return false;
			}
		} catch (Exception $e) {
			LogManager::addLine('Error : Error in the ToolBox::removeDirectoryFiles() function.');
		}

		return false;
	}
	
	/**
	 * Make a folder if it does not exist.
	 * 
	 * @param string $path Path of the folder.
	 * @return bool Return True if the folder exist or if the folder was created, else False.
	 */
	public static function makeDirectory(string $path) : bool
	{
		try {
			if (!is_dir($path)) {
				if (!mkdir($path, 0777, true)) {
					return false;
				}
			}

			return true;
		} catch (Exception $e) {
			LogManager::addLine('Error : Error in the ToolBox::makeDirectory() function.');
		}

		return false;
	}
	
	/**
	 * Copy a folder with all its contents.
	 * 
	 * @param string $source Path of the folder to copy.
	 * @param string $destination Path where the copy must be.
	 * @return int|bool Return the number of copied files, else False.
	 */
	public static function copyDirectory(string $source, string $destination): int|bool
	{
		try {
			if ($elements = scandir($source)) {
				$nbr_files = 0;

				foreach ($elements as $file) {
					if ($file !== '.' and $file !== '..') {
						if (is_dir($source . '/' . $file)) {
							self::makeDirectory($destination . '/' . $file);
							$nbr_files += self::copyDirectory($source . '/' . $file, $destination . '/'. $file);
						} else {
							if (copy($source . '/' . $file, $destination . '/' . $file)) {
								$nbr_files ++;
							} else {
								if (!file_exists($source . '/' . $file)) {
									throw new Exception($source . '/' . $file . " doesn't exist!");
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
			LogManager::addLine('Error : Error in the ToolBox::copyDirectory() function.');
		}

		return false;
	}
	
	/**
	 * Move the directory to another place.
	 * 
	 * @param string $source Path of the folder to move.
	 * @param string $destination Path where the folder must be.
	 * @return bool Return True if succeed, else False.
	 */
	public static function moveDirectory(string $source, string $destination): bool
	{
		if (!is_dir($source) ||!is_dir($destination)) {
			return false;
		}

		return rename($source, $destination);
	}
	
	/**
	 * Get all file path from a directory. Included files into subfolders.
	 * 
	 * @param string $folderPath Path of the folder to scan.
	 * @return array|false List of all file paths, else False if an error occured.
	 */
	public static function getAllPathFilesFromDir(string $folderPath): array|false
	{
		try {
			$filePaths = [];
			$dir = opendir(realpath($folderPath));

			while ($file = readdir($dir)) {
				if ($file !== '.' && $file !== '..') {
					$filePaths[] = realpath($folderPath . '/' . $file);
				}
			}

			closedir($dir);
			return $filePaths;
		} catch (Exception $e) {
			LogManager::addLine('Error : Error in the ToolBox::getAllPathFilesFromDir() function.');
		}

		return false;
	}
	
	/**
	 * Get the name of the current page in URL (Ex: index.php).
	 * 
	 * @return string Name of the page.
	 */
	public static function getCurrentPageName(): string
	{
		return substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], '/') + 1);
	}
	
	// Récupère l'URL complète de la page courante.
	static public function getCurrentUrl() : string
	{
		$isHttps = false;
		$pageURL = 'http';

		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
			$isHttps = true;
			$pageURL .= 's';
		}
		
		$pageURL .= '://';

		if ($_SERVER['SERVER_PORT'] != '80' && !$isHttps) {
			$pageURL .= $_SERVER['SERVER_NAME'] . ':'  .$_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
		} else {
			$pageURL .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		}
		
		return $pageURL;
	}		
		
	/**
	 * Format une date à partir :
	 *   -  d'un timestamp (nb Entier de sec depuis la date UNIX),
	 *   -  d'une date au format : YYYY/MM/DD 
	 *   -  d'une date au format DateTime SQL : YYYY-MM-DD hh:mm:ss
	 *   -  d'une date au format ISO : YYYY-MM-DDThh:mm:ss+00000
	 * 
	 * @param mixed $time Time the format.
	 * @param bool $displayHour Define if hour must be display.
	 * @return string Return the formated date (Ex: 5 Février 2012 ( 06h34)).
	 */
	public static function formatDate(mixed $time = null, bool $displayHour = true): string
	{
		if ($time == null) {
			$time = time();
		}

		if (gettype($time) === 'object' && get_class($time) === 'DateTime') {
			$time = $time->format('Y-m-d H:i:s');
		}

		if (preg_match("#^\d{5,}$#", '' . $time)) {
			$jour = date('j', (int)$time);
			$mois = date('m', (int)$time);
			$annee = date('Y', (int)$time);
			$heure = date('G', (int)$time);
			$minute = date('i', (int)$time);
		} else if (preg_match("#^\d{4}[/-]\d{2}[/-]\d{2}([T ]\d{2}:\d{2}:\d{2})?#", '' . $time)) {
			$annee = substr($time, 0, 4);
			$mois = substr($time, 5, 2);
			$jour = substr($time, 8, 2);
			$heure = substr($time, 11, 2);
			$minute = substr($time, 14, 2);
		}

		if ((int)$jour !== 0 && (int)$mois !== 0 && (int)$annee !== 0) {
			switch($mois) {
				case '01': $mois = 'Janvier'; break;
				case '02': $mois = 'Février'; break;
				case '03': $mois = 'Mars'; break;
				case '04': $mois = 'Avril'; break;
				case '05': $mois = 'Mai'; break;
				case '06': $mois = 'Juin'; break;
				case '07': $mois = 'Juillet'; break;
				case '08': $mois = 'Août'; break;
				case '09': $mois = 'Septembre'; break;
				case '10': $mois = 'Octobre'; break;
				case '11': $mois = 'Novembre'; break;
				case '12': $mois = 'Décembre'; break;
				default:
					throw new ErrorException('The month does not exist.');
			}

			if (substr($jour, 0, 1) == '0') {
				$jour = substr($jour, 1, 1);
			}
			
			if ($displayHour && (int)$heure !== false && (int)$minute !== false) {
				return $jour . ' ' . $mois . ' ' . $annee . ' ' . $heure . 'h' . $minute;
			}
			
			return $jour . ' ' . $mois . ' ' . $annee;
		}

		return false;
	}
	
	/**
	 * Convert HTML <br /> to \n.
	 * 
	 * @param string $string String to convert.
	 * @return string Converted string.
	 */
	public static function br2nl(string $string): string
	{
		return preg_replace("/\<br(\s*)?\/?\>/i", '\n', $string);
	}
	
	/**
	 * Remove all accents.
	 * (Works with Unicode)
	 * 
	 * @param string $string string where accents mest be removed.
	 * @return string Return the string without accent.
	 */
	public static function stripAccents(string $string): string
	{
		return str_replace(
			['à','â','ä','á','ã','å','î','ï','ì','í','ô','ö','ò','ó','õ','ø','ù','û','ü','ú','é','è','ê','ë','ç','ý','ÿ','ñ','À','Á','Â','Ã','Ä','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ñ','Ò','Ó','Ô','Õ','Ö','Ù','Ú','Û','Ü','Ý'],
			['a','a','a','a','a','a','i','i','i','i','o','o','o','o','o','o','u','u','u','u','e','e','e','e','c','y','y','n','A','A','A','A','A','C','E','E','E','E','I','I','I','I','N','O','O','O','O','O','U','U','U','U','Y'],
			$string
		);
	}
	
	/**
	 * Determine the age form birthday.
	 * 
	 * @param DateTime $birthday Birthday of the person.
	 * @return int Age of the person.
	 */
	public static function age(DateTime $birthday): int
	{
		$annee = (int)$birthday->format('Y');
		$mois = (int)$birthday->format('m');
		$jour = (int)$birthday->format('d');

		$today = [
			'jour' => (int)date('d'),
			'mois' => (int)date('m'),
			'annee' => (int)date('Y')
		];
		
		$age = $today['annee'] - $annee;
		
		if ($today['mois'] <= $mois) {
			if ($mois === $today['mois']) {
				if ($today['jour'] < $jour) {
					$age--;
				}
			} else {
				$age--;
			}
		}

		return $age;
	}

	/**
	 * Convert string to bool.
	 * 
	 * @param string $string Boolean string ("true"/"false", "1"/"0").
	 * @return bool Corresponding boolean.
	 */
	public static function stringToBool(string $string): bool
	{
		$string = mb_strtolower($string);
		
		if($string === 'true' || $string === '1') {
			return true;
		}

		return false;
	}

	/**
	 * Convert bool to string.
	 * 
	 * @param string $string Boolean to convert.
	 * @return bool Corresponding boolean string ("true"/"false").
	 */
	public static function boolToString(bool $bool): string
	{
		if ($bool) {
			return 'true';
		}

		return 'false';
	}

	/**
	 * Search a list of item into an array.
	 * 
	 * @param array $searchArray List of item to search.
	 * @param array $values List where items must be found.
	 * @param bool $allValues Define if all values must be found or not.
	 * @return bool Return true if item(s) was found, else False.
	 */
	public static function searchInArray(array $searchArray, array $values, bool $allValues = false): bool
	{
		if ($allValues) {
			$result = true;

			foreach ($values as $value) {
				$result = $result & in_array($value, $searchArray);
			}

			return $result;
		} else {
			foreach ($searchArray as $item) {
				if (in_array($item, $values)) {
					return true;
				}
			}

			return false;
		}
	}
	
	/**
	 * Generate a strong password.
	 * 
	 * @param int $nbChar Length of the password.
	 * @return string Generated password.
	 */
	public static function generatePassword(int $nbChar = 16): string
	{
		if ($nbChar <= 0) {
			throw new InvalidArgumentException("The password's length must be positive.");
		}

		$chars = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
						'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
						'0','1','2','3','4','5','6','7','8','9',
						'!','?','#','-','_'];

		$nbChars = count($chars) - 1;
		$pass = '';

		for ($i = 0; $i < $nbChar; $i ++) {
			$pass .= $chars[random_int(0, $nbChars)];
		}

		return $pass;
	}

	/**
	 * Format the phone number to be display.
	 * 
	 * @param string $phone Numberphone to format.
	 * @return string The formated number phone.
	 */
	public static function formatNumberPhone(string $phone): string
	{
		return $phone;
	}

	/**
	 * Convert an integer to words.
	 * Ex: 1232 => "mille deux cent trente deux"
	 */
	public static function convertNumberToString(int $number): string
	{
		$lettresUnite = ['', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf', 'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf'];
		$lettresDizaine = ['', '', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante', 'quatre-vingt', 'quatre-vingt'];
	
		if ($number === 0) {
            return 'zéro';
        }

		if ($number < 0) {
            return 'moins ' . self::convertNumberToString(abs($number));
        }

        if ($number < 20) {
            return $lettresUnite[$number];
        } else if ($number < 70) {
            $dizaine = floor($number / 10);
            $unite = $number % 10;
            $lettreDizaine = $lettresDizaine[$dizaine];

            if ($unite == 1 || $unite == 11) {
                $lettreUnite = 'et ' . $lettresUnite[$unite];
            } else {
                $lettreUnite = $lettresUnite[$unite];
            }

            return $lettreDizaine . ' ' . $lettreUnite;
        } else if ($number < 100) {
            $dizaine = floor($number / 10);
            $unite = $number % 10;

			$lettreDizaine = $lettresDizaine[$dizaine];

			if ($dizaine == 7 || $dizaine == 9) {
				if ($dizaine == 7 && $unite == 1) {
					$lettreUnite = 'et ' . $lettresUnite[$unite + 10];
				} else {
					$lettreUnite = $lettresUnite[$unite + 10];
				}
			} else {
				$lettreUnite = $lettresUnite[$unite];
			}

            return $lettreDizaine . ' ' . $lettreUnite;
        } else if ($number < 1000) {
            $centaine = floor($number / 100);
            $reste = $number % 100;
            $lettreCentaine = $centaine == 1 ? 'cent' : $lettresUnite[$centaine] . ' cent';

            return $lettreCentaine . ($reste > 0 ? ' ' . self::convertNumberToString($reste) : '');
        } else {
            $suffixes = ['mille', 'million', 'milliard', 'billion', 'billiard', 'trillion']; // Continuer pour d'autres échelles

            foreach ($suffixes as $exposant => $suffixe) {
                $base = pow(1000, $exposant + 1);

                if ($number < $base * 1000) {
                    $partieEntiere = floor($number / $base);
                    $reste = $number % $base;

					if ($partieEntiere == 1) {
                    	$partieEnLettres = $exposant == 0 ? $suffixe : self::convertNumberToString($exposant) . ' ' . $suffixe;
					} else {
						$partieEnLettres = self::convertNumberToString($partieEntiere) . ' ' . $suffixe . 's';
					}

                    return $partieEnLettres . ($reste != 0 ? ' ' . self::convertNumberToString($reste) : '');
                }
            }
        }

        return "Nombre non pris en charge";
	}

	public static function monthToWord(int $monthNumber): string
	{
		switch($monthNumber) {
			case 1: return 'Janvier';
			case 2: return 'Février';
			case 3: return 'Mars';
			case 4: return 'Avril';
			case 5: return 'Mai';
			case 6: return 'Juin';
			case 7: return 'Juillet';
			case 8: return 'Août';
			case 9: return 'Septembre';
			case 10: return 'Octobre';
			case 11: return 'Novembre';
			case 12: return 'Décembre';
			default:
				throw new ErrorException("The month ({$monthNumber}) does not exist.");
		}
	}
}
?>