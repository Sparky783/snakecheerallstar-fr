<?php
if(ToolBox::SearchInArray($session->admin_roles, array("admin")))
{
	$app->Post("/admins_list", function($args) {
		include_once(ABSPATH . "model/system/Admin.php");

		$admins = Admin::GetList();
		$list = array();
		
		foreach($admins as $admin)
			$list[] = $admin->ToArray();

		API::SendJSON(array('admins' => $list));
	});

	$app->Post("/admin_add", function($args) {
		include_once(ABSPATH . "model/PHPMailer/src/PHPMailer.php");
		include_once(ABSPATH . "model/PHPMailer/src/SMTP.php");
		include_once(ABSPATH . "model/snake/SnakeTools.php");
		include_once(ABSPATH . "model/EmailTemplates.php");
		include_once(ABSPATH . "model/system/Admin.php");

		$password = SnakeTools::GeneratePassword();

		$admin = new Admin();
		$admin->SetEmail($args['email']);
		$admin->SetPassword($password);
		$admin->SetName($args['name']);
		$admin->SetRoles($args['roles']);
		
		if($admin->SaveToDatabase())
		{
			// E-mail d'information
			$resultEmail = false;
			$mail = new PHPMailer(true); // Passing `true` enables exceptions

			try {
				//Server settings
				$mail->isSMTP();
				$mail->Host = SMTP_HOST;
				$mail->SMTPAuth = SMTP_AUTH;
				$mail->Adminname = SMTP_USERNAME;
				$mail->Password = SMTP_PASSWORD;
				$mail->SMTPSecure = SMTP_SECURE;
				$mail->Port = SMTP_PORT;
				$mail->CharSet = 'utf-8';

				//Recipients
				$mail->setFrom(EMAIL_WEBSITE, 'Nouveau compte | ' . TITLE);
				$mail->addAddress($admin->GetEmail(), $admin->GetName());
				$mail->addAddress(EMAIL_WABMASTER,  $admin->GetName());

				//Content
				$subject = "Nouveau compte - " . TITLE;
				$message = "
					Coucou " .  $admin->GetName() . ",
					<br /><br />
					Te voici nouveau membre du conseil, voici tes identifiants afin que tu puisse te connecter à l'espace d'administration.
					Cette interface en ligne vous offre plein d'outils vous permettant de gérer le club ainsi que d'afficher ls information essentiel sur les adhérents.
					<br /><br />
					Identifiant: " . $admin->GetEmail() . "<br />
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

	$app->Post("/admin_edit", function($args) {
		include_once(ABSPATH . "model/system/Admin.php");
		
		$admin = Admin::GetById($args['id_admin']);
		$admin->SetEmail($args['email']);
		$admin->SetName($args['name']);
		$admin->SetRoles($args['roles']);

		API::SendJSON($admin->SaveToDatabase());
	});

	$app->Post("/admin_remove", function($args) {
		include_once(ABSPATH . "model/Admin.php");
		
		API::SendJSON(Admin::RemoveFromDatabase($args['id_admin']));
	});

	$app->Post("/reinit_admin_password", function($args) {
		include_once(ABSPATH . "model/system/ToolBox.php");
		include_once(ABSPATH . "model/system/Admin.php");
		include_once(ABSPATH . "model/PHPMailer/src/PHPMailer.php");
		include_once(ABSPATH . "model/PHPMailer/src/SMTP.php");
		include_once(ABSPATH . "model/EmailTemplates.php");
		
		$password = ToolBox::GeneratePassword();

		$admin = Admin::GetById($args['id_admin']);
		$admin->SetPassword(sha1(sha1(AUTH_SALT) . sha1($password)));
		$admin->SaveToDatabase();

		// E-mail d'information
		$resultEmail = false;
		$mail = new PHPMailer(true); // Passing `true` enables exceptions

		try {
			//Server settings
			$mail->isSMTP();
			$mail->Host = SMTP_HOST;
			$mail->SMTPAuth = SMTP_AUTH;
			$mail->adminname = SMTP_adminNAME;
			$mail->Password = SMTP_PASSWORD;
			$mail->SMTPSecure = SMTP_SECURE;
			$mail->Port = SMTP_PORT;
			$mail->CharSet = 'utf-8';

			//Recipients
			$mail->setFrom(EMAIL_WEBSITE, 'Reinitialisation du mot de passe | ' . TITLE);
			if(ENV == "PROD")
				$mail->addAddress($admin->GetEmail(), $admin->GetName());
			else // ENV DEV
				$mail->addAddress(EMAIL_WABMASTER, $admin->GetName());

			//Content
			$subject = "Réinitialisation du mot de passe - " . TITLE;
			$message = "
				Bonjour " . $admin->GetName() . ",
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