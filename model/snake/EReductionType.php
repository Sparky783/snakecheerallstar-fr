<?php
namespace Snake;

/**
 * Représente un type de réduction.
 */
enum EReductionType: int
{
	case None = 0;
	case Percentage = 1;
	case Amount = 2;
}