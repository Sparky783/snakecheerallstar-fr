<?php

use Snake\EPaymentType;
use System\WebSite;
use System\ToolBox;
use Snake\Payment;

// ==== Access security ====
if (!ToolBox::searchInArray($session->admin_roles, ['admin', 'webmaster', 'tresorier'])) {
	WebSite::redirect('login', true);
}
// =========================

$amountEspece = 0;
$amountCheque = 0;
$amountInternet = 0;
$nbEspece = 0;
$nbCheque = 0;
$nbInternet = 0;
$fraisPaypal = 0;

$payments = Payment::getList($session->selectedSaison);

if ($payments !== false) {
	foreach ($payments as $payment) {
		switch ($payment->getMethod()) {
			case EPaymentType::Espece:
				$amountEspece += $payment->getFinalAmount();
				$nbEspece ++;
				break;

			case EPaymentType::Cheque:
				$amountCheque += $payment->getFinalAmount();
				$nbCheque ++;
				break;

			case EPaymentType::Internet:
				$amountInternet += $payment->getFinalAmount();
				$nbInternet ++;
				break;
		}
	}

	// Applique les frait de paypal pour calculer le montant.
	$fraisPaypal = ($nbInternet * 0.25) + ($amountInternet * 0.034);
}

$totalOfPayments = $nbInternet + $nbCheque + $nbEspece;
$totalOfAmount = $amountInternet + $amountCheque + $amountEspece;

$paymentHtml = <<<HTML
	<tr>
		<td>Internet (PayPal)</td>
		<td>{$nbInternet}</td>
		<td>{$amountInternet} € (Frais: {$fraisPaypal}€)</td>
	</tr>
	<tr>
		<td>Cheque</td>
		<td>{$nbCheque}</td>
		<td>{$amountCheque} €</td>
	</tr>
	<tr>
		<td>Espèce</td>
		<td>{$nbEspece}</td>
		<td>{$amountEspece} €</td>
	</tr>
	<tr>
		<th>TOTAL</th>
		<th>{$totalOfPayments}</th>
		<th>{$totalOfAmount} €</th>
	</tr>
	HTML;
?>