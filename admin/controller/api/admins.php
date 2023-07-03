<?php
use ApiCore\Api;
use System\ToolBox;
use System\Admin;
use Common\EmailTemplates;

if(ToolBox::SearchInArray($session->admin_roles, array("admin")))
{
	$app->Post("/admins_list", function($args) {
		$admins = Admin::GetList();
		$list = array();
		
		foreach($admins as $admin)
			$list[] = $admin->ToArray();

		API::SendJSON(array('admins' => $list));
	});

	$app->Post("/admin_add", function($args) {
		$password = ToolBox::GeneratePassword();

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
			$error = null;

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

				if(ENV == "PROD")
					$mail->addAddress($admin->GetEmail(), $admin->GetName());
				else // ENV DEV
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
				$mail->Body    = EmailTemplates::standardHtml($subject, $message);
				$mail->AltBody = EmailTemplates::standardText("Nouveau compte - " . TITLE);

				$mail->send();
				$resultEmail = true;
			}
			catch (Exception $e)
			{
				$error = array(
					"source" => "Server",
					"error" => $e->getMessage()
				);
			}

			if($resultEmail)
				API::SendJSON($admin->GetId());
			else
				API::SendJSON($error);
		}
		else
		{
			API::SendJSON(false);
		}
	});

	$app->Post("/admin_edit", function($args) {
		$admin = Admin::GetById($args['id_admin']);
		$admin->SetEmail($args['email']);
		$admin->SetName($args['name']);
		$admin->SetRoles($args['roles']);

		API::SendJSON($admin->SaveToDatabase());
	});

	$app->Post("/admin_remove", function($args) {
		API::SendJSON(Admin::RemoveFromDatabase($args['id_admin']));
	});

	$app->Post("/reinit_admin_password", function($args) {
		$password = ToolBox::GeneratePassword();

		$admin = Admin::GetById($args['id_admin']);
		$admin->SetPassword(sha1(sha1(AUTH_SALT) . sha1($password)));
		$admin->SaveToDatabase();

		// E-mail d'information
		$resultEmail = false;
		$mail = new PHPMailer(true); // Passing `true` enables exceptions
		$error = false;

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
			$mail->Body    = EmailTemplates::standardHtml($subject, $message);
			$mail->AltBody = EmailTemplates::standardText("Réinitialisation du mot de passe - " . TITLE);

			$resultEmail = $mail->send();
		}
		catch (Exception $e)
		{
			$error = array(
				"source" => "Server",
				"error" => $e->getMessage()
			);
		}

		if($resultEmail)
			API::SendJSON($admin->GetId());
		else
			API::SendJSON($error);
	});
}
?>