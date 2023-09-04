<?php
use ApiCore\Api;
use System\Session;
use Snake\SnakeTools;
use Snake\SimplePayPal;

/*
$app->Post("/create_paypal_order", function($args) {
	global $router;
	$session = Session::getInstance();

	$return_url = $router->GetUrl("inscription", array(
		"paypalResponse" => "validated"
	));

	$cancel_url = $router->GetUrl("inscription", array(
		"paypalResponse" => "canceled"
	));

	$paypal = new SimplePayPal(array(
		"url" => PAYPAL_URL,
		"client_id" => PAYPAL_CLIENT_ID,
		"secret" => PAYPAL_SECRET
	));

	$data = array(
		"intent" => "CAPTURE",
		"purchase_units" => array(
			array(
				"amount" => array(
					"currency_code" => "EUR",
					"value" => "" . $session->inscription->GetPayment()->GetFinalAmount()
				),
				"description" => "Cotisation Snake Cheer All Star saison " . SnakeTools::GetCurrentSaison()
			)
		),
		"application_context" => array(
			"brand_name" => "Snake Cheer All Star",
			"return_url" => $return_url,
			"cancel_url" => $cancel_url,
			"landing_page" => "BILLING"
		)
	);
	
	$response = $paypal->CreateOrder($data);

	API::SendJSON(json_decode($response));
});
*/

$app->post("/approve_paypal_order", function($args) {
	$session = Session::getInstance();
	
	$paypal = new SimplePayPal([
		'url' => PAYPAL_URL,
		'client_id' => PAYPAL_CLIENT_ID,
		'secret' => PAYPAL_SECRET
	]);
	
	$orderId = $args['orderID'];
	$response = $paypal->showOrderDetails($orderId);
	$purchaseUnit = $response->purchase_units[0];

	$currency = $purchaseUnit->payments->captures[0]->amount->currency_code;
	$amount = $purchaseUnit->payments->captures[0]->amount->value;
	$status = $purchaseUnit->payments->captures[0]->status;

	$amountExpected = number_format($session->inscription->getPayment()->getFinalAmount(), 2);

	$result = true;
	$message = '';
	
	// Valide le paiement PayPal
	$validPayment = true;
	$validPayment = $validPayment && $currency === 'EUR';
	$validPayment = $validPayment && $amount === $amountExpected;
	$validPayment = $validPayment && $status === 'COMPLETED';

	if ($validPayment) {
		$session->inscription->getPayment()->setIsDone(true);
		$message = 'Le paiement à bien été effectué.';
	} else {
		$result = false;
		$message = 'Les informations de paiement ne correspondent pas aux information de la cotisation.<br />Le paiement à été annulé.';
	}

	API::SendJSON([
		'result' => $result,
		'message' => $message,
		'responsePayPal' => $response
	]);
});
?>