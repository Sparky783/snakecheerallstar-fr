<?php
namespace Snake;

/**
 * Etapes de l'inscription
 */
enum EInscriptionStep: int
{
	case Information = 1;
	case Payment = 2;
	case Validation = 3;
}