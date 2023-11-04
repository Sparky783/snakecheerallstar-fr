<?php
namespace Snake;

/**
 * Choix fait par l'adhérent pour l'achat/location de la tenue.
 */
enum EUniformOption: int
{
	case None = 0;
	case Rent = 1;
	case Buy = 2;
}