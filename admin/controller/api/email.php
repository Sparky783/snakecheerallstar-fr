<?php
if(ToolBox::SearchInArray($session->roles, array("admin", "member")))
{
	$app->Post("/email", function($args) {
		include_once(ABSPATH . "model/system/Session.php");
		include_once(ABSPATH . "model/PHPMailer/src/PHPMailer.php");
		include_once(ABSPATH . "model/PHPMailer/src/SMTP.php");
		include_once(ABSPATH . "model/snake/SnakeTools.php");
		include_once(ABSPATH . "model/snake/Tuteur.php");
		include_once(ABSPATH . "model/EmailTemplates.php");

		$session = Session::GetInstance();

		// Récupération des données
		$subject = strip_tags($args['subject']);
		$message = strip_tags($args['message']);

		if($subject != "" && $message != "")
		{
			$mail = new PHPMailer(true); // Passing `true` enables exceptions
			$send = false;

			try
			{
				//Server settings
				$mail->isSMTP();
				$mail->Host = SMTP_HOST;
				$mail->SMTPAuth = SMTP_AUTH;
				$mail->Username = SMTP_USERNAME;
				$mail->Password = SMTP_PASSWORD;
				$mail->SMTPSecure = SMTP_SECURE;
				$mail->Port = SMTP_PORT;
				$mail->CharSet = 'utf-8';

				//Attachments
				if(isset($_FILES['files']['name']) && is_array($_FILES['files']['name']))
				{
					$nb = count($_FILES['files']['name']);

					for($i = 0; $i < $nb; $i++)
					{
						if($_FILES['files']['error'][$i] == UPLOAD_ERR_OK)
							$mail->addAttachment($_FILES['files']['tmp_name'][$i], $_FILES['files']['name'][$i]);
					}
				}

				//Recipients
				if($args['id_section'] == "all")
					$tuteurs = Tuteur::GetList($session->selectedSaison);
				else
					$tuteurs = Tuteur::GetListBySection($args['id_section']);
				
				$mail->setFrom(EMAIL_WEBSITE, 'Contact | ' . TITLE);

				if(ENV == "PROD")
				{
					$mail->addAddress(EMAIL_CONTACT, "Snake Cheer All Star");
					$mail->addBCC(EMAIL_WABMASTER, "Snake Cheer All Star - Admin");

					if(count($tuteurs) > 0)
					{
						foreach ($tuteurs as $tuteur)
						{
							if($tuteur->GetEmail() != "")
							{
								$mail->addBCC($tuteur->GetEmail(), $tuteur->GetFirstname() . " " . $tuteur->GetLastname());
								$send = true;
							}
						}
					}
				}
				else // ENV DEV
				{
					var_dump($tuteurs);

					$mail->addAddress(EMAIL_WABMASTER, "Snake Cheer All Star");
					$send = true;
				}

				//Content
				$mail->isHTML(true); // Set email format to HTML

				$html = "<p>" . str_replace("\n", "<br />", $message) . "</p>";

				$mail->Subject = $subject;
				$mail->Body    = EmailTemplates::StandardHTML($subject, $html);
				$mail->AltBody = EmailTemplates::TextFormat($subject);

				if($send)
					$mail->send();

				$reponse = array(
					'error' => false,
					'errorMessage' => "",
					'message' => "Votre message à bien été envoyé."
				);
			}
			catch (Exception $e)
			{
				$reponse = array(
					'error' => true,
					'errorMessage' => "Message could not be sent. Mailer Error: " . $mail->ErrorInfo,
					'message' => "Désolé, une erreur est survenue."
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
}
?>