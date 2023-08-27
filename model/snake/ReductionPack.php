<?php
namespace Snake;

use Snake\Reduction;
use Snake\EReductionType;

/**
 * Pack de réductions
 */
class ReductionPack
{
	/**
	 * Retourne une réduction Fratrie (15%).
	 * 
	 * @return Reduction
	 */
	public static function buildFratrieReduction(): Reduction
	{
		$reduction = new Reduction();
		$reduction->setType(EReductionType::Percentage);
		$reduction->setValue(15);
		$reduction->setSujet("Tarif fratrie");

		return $reduction;
	}

	/**
	 * Retourne une réduction Pass Sport (50€).
	 * 
	 * @return Reduction
	 */
	public static function buildPassSportReduction(): Reduction
	{
		$reduction = new Reduction();
		$reduction->setType(EReductionType::Amount);
		$reduction->setValue(50);
		$reduction->setSujet('Pass Sport');

		return $reduction;
	}
}