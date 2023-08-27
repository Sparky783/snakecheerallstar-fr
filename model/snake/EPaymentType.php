<?php
namespace Snake;

/**
 * Méthodes de paiement possible.
 */
enum EPaymentType: string
{
	case Espece = 'espece';
	case Cheque = 'cheque';
	case Internet = 'internet';
	case Virement = 'virement';
}