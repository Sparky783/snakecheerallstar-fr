<?php
namespace Snake;

/**
 * Méthodes de paiement possible.
 */
enum EPaymentType
{
	case Espece;
	case Cheque;
	case Internet;
	case Virement;
}