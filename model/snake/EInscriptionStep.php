<?php
namespace Snake;

/**
 * Etapes de l'inscription
 */
enum EInscriptionStep {
	case Information;
	case Payment;
	case Validation;
}