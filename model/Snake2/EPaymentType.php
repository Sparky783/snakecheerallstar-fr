<?php
namespace Snake;

/**
 * Méthodes de paiement possible.
 */
enum EPaymentType: int
{
	case None = 0;
	case Espece = 1;
	case Cheque = 2;
	case Internet = 3;
	case Virement = 4;
}