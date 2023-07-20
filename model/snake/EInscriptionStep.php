<?php
namespace Snake;

/**
 * Etapes de l'inscription
 */
enum EInscriptionStep {
	case Adherents;
	case Tuteurs;
	case Authorization;
	case Payment;
	case Validation;
}