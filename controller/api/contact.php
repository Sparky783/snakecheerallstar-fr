<?php
use ApiCore\Api;
use Common\ReCaptcha;
use Snake\SnakeMailer;

$app->post('/contact', function($args) {
	// ReCaptcha
	$secret = '6LeNSLcUAAAAAKYgk_tGwoFD4sEwV2sRKZAKnxHL'; // A modifier
	$responseCaptcha = null;
	$reCaptcha = new ReCaptcha($secret);

	if (ENV !== 'DEV' && $args['g-recaptcha-response']) {
		$responseCaptcha = $reCaptcha->verifyResponse(
			$_SERVER['REMOTE_ADDR'],
			$args['g-recaptcha-response']
		);
	}

	// Récupération des données
	$name = strip_tags($args['name']);
	$email = strip_tags($args['email']);
	$message = strip_tags($args['message']);

	$test = true;
	if (!preg_match('/^[a-zéèàêâùïüëçA-Z -]{2,40}$/', $name)) {
		$test = false;
	}

	if (!preg_match('/^[a-z0-9._-]+@[a-z0-9._-]+\.[a-z]{2,6}$/', $email)) {
		 $test = false;
	}

	if ($message === '') {
		$test = false;
	}

	if ($test) {
		if (($responseCaptcha !== null && $responseCaptcha->success) || ENV === 'DEV') {
			if (SnakeMailer::sendContact($name, $email, $message)) {
				$reponse = [
					'error' => false,
					'message' => "Merci {$name}!<br />Votre message à bien été envoyé."
				];
			} else {
			    $reponse = [
					'error' => true,
					'message' => 'Désolé, une erreur est survenue.'
				];
			}

		} else {
			$reponse = [
				'error' => true,
				'message' => "Le controle anti-robot n'est pas dévérouillé."
			];
		}
	} else {
		$reponse = [
			'error' => true,
			'message' => "L'un des champs n'est pas correctement rempli."
		];
	}

	API::sendJSON($reponse);
});
?>