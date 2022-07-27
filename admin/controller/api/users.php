<?php
if(ToolBox::SearchInArray($session->roles, array("admin")))
{
	$app->Post("/users_list", function($args) {
		include_once(ABSPATH . "model/User.php");

		$users = User::GetList();
		$list = array();
		
		foreach($users as $user)
		{
			$list[] = array(
				"id_user" => $user->GetId(),
				"email" => $user->GetEmail(),
				"name" => $user->GetName(),
				"status" => $user->GetStatus()
			);
		}

		API::SendJSON(array('users' => $list));
	});

	$app->Post("/user_add", function($args) {
		include_once(ABSPATH . "model/PHPMailer/src/PHPMailer.php");
		include_once(ABSPATH . "model/PHPMailer/src/SMTP.php");
		include_once(ABSPATH . "model/snake/SnakeTools.php");
		include_once(ABSPATH . "model/EmailTemplates.php");
		include_once(ABSPATH . "model/User.php");

		$password = SnakeTools::GeneratePassword();

		$user = new User();
		$user->SetEmail($args['email']);
		$user->SetPassword($password);
		$user->SetName($args['name']);
		$user->SetStatus($args['status']);
		
		if($user->SaveToDatabase())
		{
			// E-mail d'information
			$resultEmail = false;
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
				$mail->setFrom(EMAIL_WEBSITE, 'Nouveau compte | ' . TITLE);
				$mail->addAddress($user->GetEmail(), $user->GetName());
				$mail->addAddress(EMAIL_WABMASTER,  $user->GetName());

				//Content
				$subject = "Nouveau compte - " . TITLE;
				$message = "
					Coucou " .  $user->GetName() . ",
					<br /><br />
					Te voici nouveau membre du conseil, voici tes identifiants afin que tu puisse te connecter à l'espace d'administration.
					Cette interface en ligne vous offre plein d'outils vous permettant de gérer le club ainsi que d'afficher ls information essentiel sur les adhérents.
					<br /><br />
					Identifiant: " . $user->GetEmail() . "<br />
					Mot de passe: " . $password . "
					<br /><br />
					Pour vous connecter, rendez-vous directement sur l'espace d'administration en cliquant sur ce lien:<br />
					<a href='https://snakecheerallstar.fr/admin.php'>www.snakecheerallstar.fr/admin.php</a>
					<br /><br />
					Pensez à l'enregistrer dans vos favoris afin d'y avoir accès plus rapidement.
					<br /><br />
					Bisous bisous,
					<br /><br />
					Les Snakes
				";

				$mail->isHTML(true); // Set email format to HTML
				$mail->Subject = $subject;
				$mail->Body    = EmailTemplates::StandardHTML($subject, $message);
				$mail->AltBody = EmailTemplates::TextFormat("Nouveau compte - " . TITLE);

				$mail->send();
				$resultEmail = true;
			}
			catch (Exception $e) { }

			if($resultEmail)
				API::SendJSON($id);
			else
				API::SendJSON(false);
		}
	});

	$app->Post("/user_edit", function($args) {
		include_once(ABSPATH . "model/User.php");
		
		$user = User::GetById($args['id_user']);
		$user->SetEmail($args['email']);
		$user->SetName($args['name']);
		$user->SetStatus($args['status']);

		API::SendJSON($user->SaveToDatabase());
	});

	$app->Post("/user_remove", function($args) {
		include_once(ABSPATH . "model/User.php");
		
		API::SendJSON(User::RemoveFromDatabase($args['id_user']));
	});

	$app->Post("/reinit_user_password", function($args) {
		include_once(ABSPATH . "model/system/ToolBox.php");
		include_once(ABSPATH . "model/system/Admin.php");
		include_once(ABSPATH . "model/PHPMailer/src/PHPMailer.php");
		include_once(ABSPATH . "model/PHPMailer/src/SMTP.php");
		include_once(ABSPATH . "model/EmailTemplates.php");
		
		$password = ToolBox::GeneratePassword();

		$user = Admin::GetById($args['id_user']);
		$user->SetPassword(sha1(sha1(AUTH_SALT) . sha1($password)));
		$user->SaveToDatabase();

		// E-mail d'information
		$resultEmail = false;
		$mail = new PHPMailer(true); // Passing `true` enables exceptions

		try {
			//Server settings
			$mail->isSMTP();
			$mail->Host = SMTP_HOST;
			$mail->SMTPAuth = SMTP_AUTH;
			$mail->username = SMTP_userNAME;
			$mail->Password = SMTP_PASSWORD;
			$mail->SMTPSecure = SMTP_SECURE;
			$mail->Port = SMTP_PORT;
			$mail->CharSet = 'utf-8';

			//Recipients
			$mail->setFrom(EMAIL_WEBSITE, 'Reinitialisation du mot de passe | ' . TITLE);
			if(ENV == "PROD")
				$mail->addAddress($user->GetEmail(), $user->GetName());
			else // ENV DEV
				$mail->addAddress(EMAIL_WABMASTER, $user->GetName());

			//Content
			$subject = "Réinitialisation du mot de passe - " . TITLE;
			$message = "
				Bonjour " . $user->GetName() . ",
				<br /><br />
				Voici votre nouveau mot de passe. Retenez-le cette fois_ci ^^. Utilisez un gestionnaire de mot de passe si besoin.
				<br /><br />
				Mot de passe: " . $password . "
				<br /><br />
				A bientôt,
				<br /><br />
				Les Snakes
			";

			$mail->isHTML(true); // Set email format to HTML
			$mail->Subject = $subject;
			$mail->Body    = EmailTemplates::StandardHTML($subject, $message);
			$mail->AltBody = EmailTemplates::TextFormat("Réinitialisation du mot de passe - " . TITLE);

			$mail->send();
			$resultEmail = true;
		}
		catch (Exception $e) { }

		API::SendJSON($resultEmail);
	});
}
?>