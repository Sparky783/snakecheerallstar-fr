<?php
use ApiCore\Api;
use Common\ReCaptcha;
use Snake\EmailTemplates;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$app->Post("/contact", function($args) {
	// ReCaptcha
	$secret = "6LeNSLcUAAAAAKYgk_tGwoFD4sEwV2sRKZAKnxHL"; // A modifier
	$responseCaptcha = null;
	$reCaptcha = new ReCaptcha($secret);
	if(ENV != "DEV" && $args["g-recaptcha-response"]) {
		$responseCaptcha = $reCaptcha->verifyResponse(
			$_SERVER["REMOTE_ADDR"],
			$args["g-recaptcha-response"]
		);
	}

	// Récupération des données
	$nom = strip_tags($args['name']);
	$email = strip_tags($args['email']);
	$message = strip_tags($args['message']);

	$test = true;
	if(!preg_match("/^[a-zéèàêâùïüëçA-Z -]{2,40}$/", $nom)) $test = false;
	if(!preg_match("/^[a-z0-9._-]+@[a-z0-9._-]+\.[a-z]{2,6}$/", $email)) $test = false;
	if($message == "") $test = false;

	if($test) {
		if(($responseCaptcha != null && $responseCaptcha->success) || ENV == "DEV") {
			$mail = new PHPMailer(true); // Passing `true` enables exceptions
			try {
				//Server settings
				$mail->isSMTP();
				$mail->Host = SMTP_HOST;
				$mail->SMTPAuth = SMTP_AUTH;
				$mail->Username = SMTP_USERNAME;
				$mail->Password = SMTP_PASSWORD;
				$mail->SMTPSecure = SMTP_SECURE;
				$mail->Port = SMTP_PORT;
				$mail->CharSet = 'utf-8';

				//Recipients
				$mail->setFrom(EMAIL_WEBSITE, 'Contact | ' . TITLE);
				if(ENV == "PROD") {
					$mail->addAddress(EMAIL_CONTACT, TITLE);
				} else { // ENV DEV
					$mail->addAddress(EMAIL_WABMASTER, TITLE);
				}

				//Content
				$mail->isHTML(true); // Set email format to HTML

				$sujet = "Nouveau message";
				$html = "
					<p>
						<b>Vous avez reçu un message de ".$nom." (".$email.").</b>
						<br /><br />
						<u>Message :</u>
						<br /><br />
						".$message."
					</p>
				";

				$mail->Subject = $sujet;
				$mail->Body    = EmailTemplates::standardHtml($sujet, $html);
				$mail->AltBody = EmailTemplates::standardText($sujet);

				$mail->send();
				$reponse = array(
					'error' => false,
					'errorMessage' => "",
					'message' => "Merci " . $nom ." ! Votre message à bien été envoyé."
				);
			} catch (Exception $e) {
			    $reponse = array(
					'error' => true,
					'errorMessage' => "Message could not be sent. Mailer Error: " . $mail->ErrorInfo,
					'message' => "Désolé, une erreur est survenue."
				);
			}

		} else {
			$reponse = array(
				'error' => true,
				'errorMessage' => "Le controle anti-robot n'est pas dévérouillé.",
				'message' => "Le controle anti-robot n'est pas dévérouillé."
			);
		}
	} else {
		$reponse = array(
			'error' => true,
			'errorMessage' => "L'un des champs n'est pas correctement rempli.",
			'message' => "L'un des champs n'est pas correctement rempli."
		);
	}

	API::SendJSON($reponse);
});
?>