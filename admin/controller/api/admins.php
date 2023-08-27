<?php
use ApiCore\Api;
use System\ToolBox;
use System\Admin;
use Snake\EmailTemplates;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once ABSPATH. 'model/PHPMailer/src/Exception.php';
require_once ABSPATH. 'model/PHPMailer/src/PHPMailer.php';
require_once ABSPATH. 'model/PHPMailer/src/SMTP.php';

if(ToolBox::searchInArray($session->admin_roles, ['admin'])) {
	$app->post('/admins_list', function($args) {
		$admins = Admin::getList();
		$list = [];
		
		foreach ($admins as $admin) {
			$list[] = $admin->toArray();
		}

		Api::sendJSON(['admins' => $list]);
	});

	$app->post('/admin_add', function($args) {
		$password = ToolBox::generatePassword();

		$admin = new Admin();
		$admin->setEmail($args['email']);
		$admin->setPassword($password);
		$admin->setName($args['name']);
		$admin->setRoles($args['roles']);
		
		if ($admin->saveToDatabase()) {
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

				if (ENV === 'PROD') {
					$mail->addAddress($admin->getEmail(), $admin->getName());
				} else { // ENV DEV
					$mail->addAddress(EMAIL_WABMASTER,  $admin->getName());
				}

				//Content
				$url = URL . '/admin.php';
				$subject = 'Nouveau compte - ' . TITLE;
				$message = <<<HTML
					Coucou {$admin->getName()},
					<br /><br />
					Te voici nouveau membre du conseil, voici tes identifiants afin que tu puisse te connecter à l'espace d'administration.
					Cette interface en ligne vous offre plein d'outils vous permettant de gérer le club ainsi que d'afficher ls information essentiel sur les adhérents.
					<br /><br />
					Identifiant: {$admin->getEmail()}<br />
					Mot de passe: {$password}
					<br /><br />
					Pour vous connecter, rendez-vous directement sur l'espace d'administration en cliquant sur ce lien:<br />
					<a href="{$url}">{$url}</a>
					<br /><br />
					Pensez à l'enregistrer dans vos favoris afin d'y avoir accès plus rapidement.
					<br /><br />
					Bisous bisous,
					<br /><br />
					Les Snakes
				HTML;

				$mail->isHTML(true); // Set email format to HTML
				$mail->Subject = $subject;
				$mail->Body    = EmailTemplates::standardHtml($subject, $message);
				$mail->AltBody = EmailTemplates::standardText('Nouveau compte - ' . TITLE);

				$mail->send();
				$resultEmail = true;
			} catch (Exception $e) {
				$error = [
					'source' => 'Server',
					'error' => $e->getMessage()
				];
			}

			if($resultEmail) {
				Api::sendJSON($admin->getId());
			} else {
				Api::sendJSON($error);
			}
		} else {
			Api::sendJSON(false);
		}
	});

	$app->post('/admin_edit', function($args) {
		$admin = Admin::getById($args['id_admin']);
		$admin->setEmail($args['email']);
		$admin->setName($args['name']);
		$admin->setRoles($args['roles']);

		Api::sendJSON($admin->saveToDatabase());
	});

	$app->post('/admin_remove', function($args) {
		Api::sendJSON(Admin::removeFromDatabase($args['id_admin']));
	});

	$app->post('/reinit_admin_password', function($args) {
		$password = ToolBox::generatePassword();

		$admin = Admin::getById($args['id_admin']);
		$admin->setPassword(sha1(sha1(AUTH_SALT) . sha1($password)));
		$admin->saveToDatabase();

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

			if (ENV === 'PROD') {
				$mail->addAddress($admin->getEmail(), $admin->getName());
			} else { // ENV DEV
				$mail->addAddress(EMAIL_WABMASTER, $admin->getName());
			}

			//Content
			$subject = 'Réinitialisation du mot de passe - ' . TITLE;
			$message = <<<HTML
				Bonjour {$admin->getName()},
				<br /><br />
				Voici votre nouveau mot de passe. Retenez-le cette fois_ci ^^. Utilisez un gestionnaire de mot de passe si besoin.
				<br /><br />
				Mot de passe: {$password}
				<br /><br />
				A bientôt,
				<br /><br />
				Les Snakes
			HTML;

			$mail->isHTML(true); // Set email format to HTML
			$mail->Subject = $subject;
			$mail->Body    = EmailTemplates::standardHtml($subject, $message);
			$mail->AltBody = EmailTemplates::standardText('Réinitialisation du mot de passe - ' . TITLE);

			$resultEmail = $mail->send();
		} catch (Exception $e) {
			$error = [
				'source' => 'Server',
				'error' => $e->getMessage()
			];
		}

		if ($resultEmail) {
			API::sendJSON($admin->getId());
		} else {
			API::sendJSON($error);
		}
	});
}
?>