<?php
// ==== Access security ====
if(!ToolBox::SearchInArray($session->admin_roles, array("admin", "tresorier")))
	WebSite::Redirect("login", true);
// =========================

include_once(ABSPATH . "model/snake/Payment.php");

$amountEspece = 0;
$amountCheque = 0;
$amountInternet = 0;
$nbEspece = 0;
$nbCheque = 0;
$nbInternet = 0;
$fraisPaypal = 0;

$payments = Payment::GetList($session->selectedSaison);

if($payments !== false)
{
	foreach($payments as $payment)
	{
		switch($payment->GetMethod())
		{
			case Payment::$METHODS['Espece']:
				$amountEspece += $payment->GetFinalAmount();
				$nbEspece ++;
				break;

			case Payment::$METHODS['Cheque']:
				$amountCheque += $payment->GetFinalAmount();
				$nbCheque ++;
				break;

			case Payment::$METHODS['Internet']:
				$amountInternet += $payment->GetFinalAmount();
				$nbInternet ++;
				break;
		}
	}

	// Applique les frait de paypal pour calculer le montant.
	$fraisPaypal = ($nbInternet * 0.25) + ($amountInternet * 0.034);
}

$paymentHtml = "
	<tr>
		<td>Internet (PayPal)</td>
		<td>" . $nbInternet . "</td>
		<td>" . $amountInternet . " € (Frais: " . $fraisPaypal . "€)</td>
	</tr>
	<tr>
		<td>Cheque</td>
		<td>" . $nbCheque . "</td>
		<td>" . $amountCheque . " €</td>
	</tr>
	<tr>
		<td>Espèce</td>
		<td>" . $nbEspece . "</td>
		<td>" . $amountEspece . " €</td>
	</tr>
	<tr>
		<th>TOTAL</th>
		<th>" . ($nbInternet + $nbCheque + $nbEspece) . "</th>
		<th>" . ($amountInternet + $amountCheque + $amountEspece) . " €</th>
	</tr>
";
?>