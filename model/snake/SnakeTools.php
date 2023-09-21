<?php
namespace Snake;

use DateTime;
use System\ToolBox;
use Snake\Section;

/**
 * Outil pour la gestion du site des Snake.
 */
class SnakeTools
{
	/**
	 * Détermine la bonne section pour l'adhérent suivant ça date de naissance.
	 * 
	 * @param DateTime $birthday Date de naissance de l'adhérent
	 * @return Section|false Section dans laquelle l'adhérent doit aller. Retourne False si aucune section ne correspond.
	 */
	public static function findSection(DateTime $birthday): Section|false
	{
		$year = (int)$birthday->format('Y');

		$sections = Section::getList();
		$currentDiff = 9999;
		$selectedSection = false;

		foreach ($sections as $section) {
			$diff = $section->getMaxYear() - $year;

			if ($diff >= 0) {
				if ($diff < $currentDiff) {
					$currentDiff = $diff;
					$selectedSection = $section;
				}
			}
		}

		return $selectedSection;
	}

	/**
	 * Retourne une saison en fonction d'une date.
	 * 
	 * @param string $date Date au format "AAAA-MM-DD"
	 * @return string Format: "YYYY-YYYY"
	 */
	public static function getSaison(string $date): string
	{
		$year = (int)(explode("-", $date)[0]);
		$month = (int)(explode("-", $date)[1]);

		if ($month <= 7) {
			return ($year - 1) . '-' . $year;
		}

		return $year . '-' . ($year + 1);
	}
	
	/**
	 * Retourne la saison en cours.
	 * 
	 * @return string Format: "YYYY-YYYY"
	 */
	public static function getCurrentSaison(): string
	{
		return self::getSaison(date('Y-m-d'));
	}
	
	/**
	 * Retourne la saison précédente.
	 * 
	 * @return string Format: "YYYY-YYYY"
	 */
	public static function getPreviousSaison(): string 
	{
		$year = (int)date('Y');
		$year--;
		
		return self::getSaison($year . date('-m-d'));
	}

	/**
	 * Crée les échéances (montants) de paiement.
	 * 
	 * @param float $totPrice Montant total à découper en échéance.
	 * @param int $number Nombre d'échéances souhaité.
	 * @return array|false Liste des montants pour chaque échéance. Retourne False en cas d'erreur.
	 */
	public static function makeDeadlines(float $totPrice, int $number): array|false
	{
		if($number <= 0 || $totPrice <= 0) {
			return false;
		}

		$deadlines = [];

		if ($number === 1) {
			$deadlines[] = $totPrice;
		} else {
			$monthlyPrice = round($totPrice / $number);
			
			for ($i = 0; $i < $number - 1; $i++) {
				$deadlines[] = $monthlyPrice;
			}
			
			$deadlines[] = $totPrice - ($monthlyPrice * ($number - 1)); // last month
		}
		
		return $deadlines;
	}

	/**
	 * Format le numéro de la facture pour quelle soit à la taille souhaité.
	 * Exemple: 15 => "00015"
	 * 
	 * @param int $billNumber Numéro de la facture 
	 * @param int $numberSize Nombre de charactère devant à apparaitre sur la facture pour le numéro
	 * @return string|false Retourne le numéro formaté, sinon False en cas d'erreur
	 */
	public static function formatBillNumber(int $billNumber, int $numberSize): string|false
	{
		$billNumber = $billNumber . ''; // Convertie en string
		$result = '';

		if (strlen($billNumber) > $numberSize) {
			return false;
		}

		for ($i = $numberSize - strlen($billNumber); $i > 0; $i--) {
			$result .= '0';
		}

		$result .= $billNumber;

		return $result;
	}

	/**
	 * Convertie le montant des paiements en toute lettre.
	 * 
	 * @param float $amount Montant à convertir
	 * @return string Montant en toute lettre 
	 */
	public static function convertPaymentAmountToWords(float $amount): string
	{
		$entier = floor($amount);
		$decimal = $amount - floor($amount);

		$price = ToolBox::convertNumberToString($entier) . ' euros';

		if ($decimal > 0) {
			$decimal = floor($decimal * 100);
			$price .= ' ' . ToolBox::convertNumberToString($decimal) . ' cts';
		}

		return $price;
	}
}