<?php
use ApiCore\Api;
use System\Session;
use Snake\SnakeTools;
use SimplePayPal;

$app->Post("/create_order", function($args) {
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

$app->Post("/approve_order", function($args) {
	$session = Session::getInstance();
	
	$paypal = new SimplePayPal(array(
		"url" => PAYPAL_URL,
		"client_id" => PAYPAL_CLIENT_ID,
		"secret" => PAYPAL_SECRET
	));
	
	$response = $paypal->ShowOrderDetails($args['orderID']);

	$amount = $response->purchase_units[0]->payments->captures[0]->amount;

	$result = true;
	$message = "";

	if ($amount->currency_code == "EUR" &&
		intval($amount->value) == $session->inscription->GetPayment()->GetFinalAmount() &&
		$response->status == "COMPLETED")
	{
		$session->inscription->GetPayment()->SetIsDone(true);
		$message = "Le paiement à bien été effectué.";
	}
	else
	{
		$result = false;
		$message = "Le paiement ne correspond pas à la cotisation.";
	}

	API::SendJSON(array(
		'result' => $result,
		'message' => $message,
		'responsePayPal' => $response
	));
});
?>