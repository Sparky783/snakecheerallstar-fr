<?php
namespace Snake;

require_once ABSPATH. 'model/PHPMailer/src/Exception.php';
require_once ABSPATH. 'model/PHPMailer/src/PHPMailer.php';
require_once ABSPATH. 'model/PHPMailer/src/SMTP.php';

use System\ToolBox;
use System\Admin;
use Snake\SnakeTools;
use Snake\Adherent;
use Snake\Tuteur;
use Snake\Payment;
use Snake\EmailTemplates;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Outil pour la gestion du site des Snake.
 */
class SnakeMailer
{
	/**
	 * Envoie un E-mail de communication aux membres depuis l'espace d'admin.
	 * 
	 * @param string $subject Sujet du message
	 * @param string $message Message à envoyer
	 * @param Tuteur[] $destinataires Liste des destinataires
	 * @return bool Retourne True si l'E-mail à été envoyé, sinon False.
	 */
	public static function sendMessage(string $subject, string $message, array $tuteurs): bool
	{
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

			//Attachments
			if (isset($_FILES['files']['name']) && is_array($_FILES['files']['name'])) {
				$nb = count($_FILES['files']['name']);

				for ($i = 0; $i < $nb; $i++) {
					if($_FILES['files']['error'][$i] == UPLOAD_ERR_OK) {
						$mail->addAttachment($_FILES['files']['tmp_name'][$i], $_FILES['files']['name'][$i]);
					}
				}
			}

			//Recipients
			$mail->setFrom(EMAIL_WEBSITE, 'Contact | ' . TITLE);

			if (ENV === 'PROD') {
				$mail->addAddress(EMAIL_CONTACT, 'Snake Cheer All Star');
				$mail->addBCC(EMAIL_WABMASTER, 'Snake Cheer All Star - Admin');

				if (count($tuteurs) > 0) {
					foreach ($tuteurs as $tuteur) {
						$mail->addBCC($tuteur->getEmail(), "{$tuteur->getFirstname()} {$tuteur->getLastname()}");
					}
				}
			} else { // ENV DEV
				$mail->addAddress(EMAIL_WABMASTER, 'Snake Cheer All Star');
			}

			// Content
			$html = "<p>" . str_replace("\n", "<br />", $message) . "</p>";

			$mail->isHTML(true); // Set email format to HTML
			$mail->Subject = $subject;
			$mail->Body    = EmailTemplates::standardHtml($subject, $html);
			$mail->AltBody = EmailTemplates::standardText($subject);

			$mail->send();

			return true;
		}
		catch (Exception $e) { }
			
		return false;
	}

	/**
	 * Envoie un E-mail au bureau du club. Utilisé par les adhérents ou les parents pour contacter le bureau.
	 * 
	 * @param string $name Nom de la personne souhaitant contacter le bureau.
	 * @param string $email E-mail de la personne souhaitant contacter le bureau.
	 * @param string $message Message à envoyer au bureau du club.
	 * @return bool Retourne True si l'E-mail à été envoyé, sinon False.
	 */
	public static function sendContact(string $name, string $email, string $message): bool
	{
		$mail = new PHPMailer(true); // Passing `true` enables exceptions

		try {
			// Server settings
			$mail->isSMTP();
			$mail->Host = SMTP_HOST;
			$mail->SMTPAuth = SMTP_AUTH;
			$mail->Username = SMTP_USERNAME;
			$mail->Password = SMTP_PASSWORD;
			$mail->SMTPSecure = SMTP_SECURE;
			$mail->Port = SMTP_PORT;
			$mail->CharSet = 'utf-8';

			// Recipients
			$mail->setFrom(EMAIL_WEBSITE, 'Contact | ' . TITLE);
			if (ENV === 'PROD') {
				$mail->addAddress(EMAIL_CONTACT, TITLE);
			} else { // ENV DEV
				$mail->addAddress(EMAIL_WABMASTER, TITLE);
			}

			// Content
			$mail->isHTML(true); // Set email format to HTML

			$sujet = 'Nouveau message';
			$html = <<<HTML
				<p>
					<b>Vous avez reçu un message de {$name} ({$email}).</b>
					<br /><br />
					<u>Message :</u>
					<br /><br />
					{$message}
				</p>
				HTML;

			$mail->Subject = $sujet;
			$mail->Body    = EmailTemplates::standardHtml($sujet, $html);
			$mail->AltBody = EmailTemplates::standardText($sujet);

			$mail->send();
			
			return true;
		} catch (Exception $e) { }
			
		return false;
	}

	/**
	 * Envoie une facture au tuteur sélectionné.
	 * 
	 * @param Payment $payment Paiement à afficher sur la facture.
	 * @param Tuteur $tuteur Tuteur à qui envoyer le mail. Si le tuteur n'est pas précisé, le mail sera envoyé au bureau du club.
	 * @return bool Retourne True si l'E-mail à été envoyé, sinon False.
	 */
	public static function sendBill(Inscription $inscription, Tuteur $tuteur = null): bool
	{
		// Destinataire
		if($tuteur === null) {
			$tuteur = new Tuteur();
			$tuteur->setFirstname(TITLE);
			$tuteur->setEmail(EMAIL_CONTACT);
			$tuteur->setPhone('00 00 00 00 00');
		}

		// Concaténation des infos
		$number = SnakeTools::formatBillNumber($inscription->getPayment()->getId() + 100, 5);

		// E-mail Facture
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
			$mail->setFrom(EMAIL_WEBSITE, 'Facture | ' . TITLE);
			$mail->addAddress($tuteur->getEmail(), $tuteur->getLastname() . ' ' . $tuteur->getFirstname());
			$mail->addAddress(EMAIL_WABMASTER, $tuteur->getLastname() . ' ' . $tuteur->getFirstname());

			//Content
			$mail->isHTML(true); // Set email format to HTML
			$mail->Subject = "Facture d'inscription - " . TITLE;
			$mail->Body    = EmailTemplates::billHtml($number, $inscription, $tuteur);
			$mail->AltBody = EmailTemplates::standardText("Facture d'inscription - " . TITLE);

			$mail->send();

			return true;
		}
		catch (Exception $e) { }

		return false;
	}
	
	/**
	 * Envoie le récapitulatif d'inscription au tuteur sélectionné.
	 * 
	 * @param Inscription $inscription Informations de l'inscription.
	 * @param Tuteur|null $tuteur Tuteur à qui envoyer le mail. Si le tuteur n'est pas précisé, le mail sera envoyé au bureau du club.
	 * @return bool Retourne True si l'E-mail à été envoyé, sinon False.
	 */
	public static function sendRecap(Inscription $inscription, Tuteur $tuteur = null): bool
	{
		// Destinataire
		if($tuteur === null) {
			$tuteur = new Tuteur();
			$tuteur->setFirstname(TITLE);
			$tuteur->setEmail(EMAIL_CONTACT);
			$tuteur->setPhone('00 00 00 00 00');
		}

		// Détail du paiement de la cotisation
		$payment = $inscription->getPayment();
		$paymentContent = '';

		if ($payment->getMethod() !== EPaymentType::Internet) {
			$amount = $payment->getFinalAmount();
			$paymentContent = "<p>Le paiement de la cotisation d'un montant de {$amount} €";

			switch ($payment->getMethod()) {
				case EPaymentType::Espece:
					$paymentContent .= " à régler en espèce et en <b>totalité</b>.</p>";
					break;
	
				case EPaymentType::Cheque:
					$amountWords = SnakeTools::convertPaymentAmountToWords($payment->getFinalAmount());

					if ($payment->getNbDeadlines() > 1) {
						$nombres = ['deux', 'trois', 'quatre'];
						$mot = $nombres[$payment->getNbDeadlines() - 2];

						if ((int)date('d') > 15) {
							$startMonthNumber = (int)date('m') + 1;

							if ($startMonthNumber === 13) {
								$startMonthNumber = 1;
							}
						} else {
							$startMonthNumber = (int)date('m');
						}


						$paymentContent .= " à régler par chèque en " . $mot . " fois, soit:</p>";
						$paymentContent .= "<ul>";
	
						$i = 1;
						foreach ($payment->getDeadlines() as $deadline) {
							$deadlineWords = SnakeTools::convertPaymentAmountToWords($deadline);
							$monthWord = ToolBox::monthToWord($startMonthNumber);
							$paymentContent .= "<li>Chèque n°{$i} de {$deadline} € ({$deadlineWords}) avec inscrit \"{$monthWord}\" au dos.</li>";

							$startMonthNumber ++;
							$i ++;

							if ($startMonthNumber === 13) {
								$startMonthNumber = 1;
							}
						}
	
						$paymentContent .= "</ul>";
					} else {
						$paymentContent .= " à régler par chèque en une fois, soit {$amount} € ({$amountWords}).</p>";
					}

					$paymentContent .= "<p>Merci de bien vouloir mettre les chèques à l'ordre de \"Snake Cheer All Star\".</p>";
					break;
			}

			$paymentContent = "</p>";
		}
		
		// Documents pour les adhérents
		$url = URL;
		$adherents = $inscription->getAdherents();
		$adherentsContent = '';

		if ($adherents !== null) {
			foreach ($adherents as $adherent) {
				$adherentsContent .= "<p>Pour {$adherent->getFirstname()} {$adherent->getLastname()} :</p><ul>";

				if ($adherent->hasMedicine()) {
					$adherentsContent .= "<li>Formulaire d'autorisation médical à remplir <a href='{$url}/content/dossier_inscription/afld.pdf' title='' target='_blank'>disponible ici</a></li>";
				}

				// Questionnaire de santé en fonction de l'age
				$age = ToolBox::age($adherent->getBirthday());
				
				if ($age < 18) {
					$adherentsContent .= "<li>Questionnaire de santé (Mineur) <a href='{$url}/content/dossier_inscription/questionnaire_sante_mineur.pdf' title='' target='_blank'>disponible ici</a> (obligatoire)</li>";
				} else {
					$adherentsContent .= "<li>Questionnaire de santé (Majeur) <a href='{$url}/content/dossier_inscription/questionnaire_sante_majeur.pdf' title='' target='_blank'>disponible ici</a> (obligatoire)</li>";
				}

				$adherentsContent .= "</ul><br /><br />";
			}
		}

		$saison = $adherents[0]->getSection()->getSaison();

		$signature = TITLE;
		$mailContent = <<<HTML
			<p>
				Bonjour,
				<br /><br />
				Afin de finaliser votre inscription pour la saison de cheerleading {$saison}, veuillez remettre les éléments suivants à votre coach ou à l'un des membres du bureau :
			</p>
			{$paymentContent}
			<p>
				Pour chaque adhérent, veuillez fournir les éléments suivants :
			</p>
			<ul>
				<li>Dossier d'inscription <a href="{$url}/content/dossier_inscription/dossier_snake.pdf" title='' target='_blank'>disponible ici</a></li>
				<li>Formulaire de la FFFA <a href="{$url}/content/dossier_inscription/licence_FFFA.pdf" title='' target='_blank'>disponible ici</a> <b>(Attention le certificat médical doit être rempli sur cette feuille par le médecin)</b></li>
				<li>Autorisation parentale en cas d'accident <a href="{$url}/content/dossier_inscription/autorisation_parentale.pdf" title='' target='_blank'>disponible ici</a></li>
				<li>Formulaire de Sportmut <a href="{$url}/content/dossier_inscription/sportmut.pdf" title='' target='_blank'>disponible ici</a> (même si vous n'y adhérez pas)</li>
				<li>Photocopie de la pièce d'identité</li>
				<li>Photo d'identité</li>
			</ul>
			<p>
				<br /><br />
				Cordialement,<br />
				{$signature}
			</p>
		HTML;

		$sujet = "Récapitulatif d'inscription " . TITLE;
			

		// ================================================
		// E-mail récapitulatif
		// ================================================
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
			$mail->setFrom(EMAIL_WEBSITE, 'Récapitulatif d\'inscription | ' . TITLE);

			if (ENV === 'PROD') {
				$mail->addAddress($tuteur->getEmail(), $tuteur->getLastname() . ' ' . $tuteur->getFirstname());
				$mail->addAddress(EMAIL_WABMASTER, $tuteur->getLastname() . ' ' . $tuteur->getFirstname());
			} else { // ENV DEV
				$mail->addAddress(EMAIL_WABMASTER, $tuteur->getLastname() . ' ' . $tuteur->getFirstname());
			}
			
			//Content
			$mail->isHTML(true); // Set email format to HTML
			$mail->Subject = $sujet;
			$mail->Body    = EmailTemplates::standardHTML($sujet, $mailContent);
			$mail->AltBody = EmailTemplates::standardText($sujet);
			$mail->send();
			
			return true;
		}
		catch (Exception $e) { }

		return false;
	}

	
	/**
	 * Envoie les informations de connexion au nouvel administrateur.
	 * 
	 * @param Admin $admin Administrateur concerné
	 * @param string $password Mot de passe de l'administrateur.
	 * @return bool Retourne True si l'E-mail à été envoyé, sinon False.
	 */
	public static function sendNewAdminAccount(Admin $admin, string $password): bool
	{
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
			
			return true;
		} catch (Exception $e) { }

		return false;
	}

	/**
	 * Envoie le nouveau mot de passe de l'administrateur.
	 * 
	 * @param Admin $admin Administrateur concerné
	 * @param string $password Nouveau mot de passe de l'administrateur.
	 * @return bool Retourne True si l'E-mail à été envoyé, sinon False.
	 */
	public static function sendNewAdminPassword(Admin $admin, string $password): bool
	{
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

			$mail->send();

			return true;
		} catch (Exception $e) { }

		return false;
	}
	
	/**
	 * Envoie un E-mail d'information pour le surclassement de l'adhérent.
	 * 
	 * @param Adherent $adherent Adhérent surclassé
	 * @param float $oldPrice Montant de l'ancienne cotisation
	 * @param float $newPrice Montant de la nouvelle cotisation
	 * @param Tuteur $tuteur Tuteur à qui envoyer le mail.
	 * @return bool Retourne True si l'E-mail à été envoyé, sinon False.
	 */
	public static function sendSurclassementInformation(Adherent $adherent, float $oldPrice, float $newPrice, Tuteur $tuteur = null): bool
	{
		// Destinataire
		if($tuteur === null) {
			$tuteur = new Tuteur();
			$tuteur->setFirstname(TITLE);
			$tuteur->setEmail(EMAIL_CONTACT);
			$tuteur->setPhone('00 00 00 00 00');
		}

		// Détail du paiement de la cotisation
		$paymentContent = '<p>';

		if ((int)$oldPrice === 0) {
			// La première cotisation n'a jamais été payé.
			$amountWords = SnakeTools::convertPaymentAmountToWords($newPrice);

			$paymentContent .= <<<HTML
				{$newPrice} €  ({$amountWords})
			HTML;
		} else {
			// Ajustement du paiement de la cotisation
			$price = $newPrice - $oldPrice;
			$amountWords = SnakeTools::convertPaymentAmountToWords($price);

			$paymentContent .= <<<HTML
				{$newPrice} €
				<br /><br />
				Un premier paiement de {$oldPrice} € à déjà été perçu.<br />
				Le montant final s'élève donc à <b>{$price} €</b> ({$amountWords})
			HTML;
		}
		$paymentContent .= "</p>";
		
		// Génération de l'E-mail.
		$signature = TITLE;
		$mailContent = <<<HTML
			<p>
				Bonjour,
				<br /><br />
				Nous vous informons le surclassement de {$adherent->getFirstname()} {$adherent->getLastname()} dans la section {$adherent->getSection()->getName()} (saison {$adherent->getSection()->getSaison()}).<br />
				Ce surclassement entraine une correction du montant de cotisation au club. Voici le nouveau montant:
			</p>
			{$paymentContent}
			<p>
				Nous vous invitons à remettre le paiement à l'un de nos coach ou à l'un des membres du bureau.<br />
				Dans le cas d'un chèque, merci de bien vouloir le mettre à l'ordre de \"Snake Cheer All Star\".
				<br /><br />
				Cordialement,<br />
				{$signature}
			</p>
		HTML;

		$sujet = "Surclassement - " . TITLE;
		
		// ================================================
		// E-mail récapitulatif
		// ================================================
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
			$mail->setFrom(EMAIL_WEBSITE, $sujet);

			if (ENV === 'PROD') {
				$mail->addAddress($tuteur->getEmail(), $tuteur->getLastname() . ' ' . $tuteur->getFirstname());
				$mail->addAddress(EMAIL_WABMASTER, $tuteur->getLastname() . ' ' . $tuteur->getFirstname());
			} else { // ENV DEV
				$mail->addAddress(EMAIL_WABMASTER, $tuteur->getLastname() . ' ' . $tuteur->getFirstname());
			}
			
			// Content
			$mail->isHTML(true); // Set email format to HTML
			$mail->Subject = $sujet;
			$mail->Body    = EmailTemplates::standardHTML($sujet, $mailContent);
			$mail->AltBody = EmailTemplates::standardText($sujet);
			$mail->send();
			
			return true;
		}
		catch (Exception $e) { }

		return false;
	}

	/**
	 * Envoie un E-mail d'information pour le sousclassement de l'adhérent.
	 * 
	 * @param Adherent $adherent Adhérent surclassé
	 * @param float $oldPrice Montant de l'ancienne cotisation
	 * @param float $newPrice Montant de la nouvelle cotisation
	 * @param Tuteur $tuteur Tuteur à qui envoyer le mail.
	 * @return bool Retourne True si l'E-mail à été envoyé, sinon False.
	 */
	public static function sendSousclassementInformation(Adherent $adherent, float $oldPrice, float $newPrice, Tuteur $tuteur = null): bool
	{
		// Destinataire
		if ($tuteur === null) {
			$tuteur = new Tuteur();
			$tuteur->setFirstname(TITLE);
			$tuteur->setEmail(EMAIL_CONTACT);
			$tuteur->setPhone('00 00 00 00 00');
		}

		// Détail du paiement de la cotisation
		$paymentContent = '<p>';

		if ((int)$oldPrice === 0) {
			// La première cotisation n'a jamais été payé.
			$amountWords = SnakeTools::convertPaymentAmountToWords($newPrice);

			$paymentContent .= <<<HTML
				{$newPrice} €  ({$amountWords})
				<br /><br />
				Nous vous invitons à remettre le paiement à l'un de nos coach ou à l'un des membres du bureau.<br />
				Dans le cas d'un chèque, merci de bien vouloir le mettre à l'ordre de "Snake Cheer All Star".
			HTML;
		} else {
			// Ajustement du paiement de la cotisation
			$price = $oldPrice - $newPrice;
			$amountWords = SnakeTools::convertPaymentAmountToWords($price);

			$paymentContent .= <<<HTML
				{$newPrice} €
				<br /><br />
				Un premier paiement de {$oldPrice} € à déjà été perçu.<br />
				Nous devons vous remettre le montant de <b>{$price} €</b> ({$amountWords})
			HTML;
		}
		$paymentContent .= "</p>";
		
		// Génération de l'E-mail.
		$signature = TITLE;
		$mailContent = <<<HTML
			<p>
				Bonjour,
				<br /><br />
				Nous vous informons le sousclassement de {$adherent->getFirstname()} {$adherent->getLastname()} dans la section {$adherent->getSection()->getName()} (saison {$adherent->getSection()->getSaison()}).<br />
				Ce sousclassement entraine une correction du montant de cotisation au club. Voici le nouveau montant:
			</p>
			{$paymentContent}
			<p>
				Cordialement,<br />
				{$signature}
			</p>
		HTML;

		$sujet = "Sousclassement - " . TITLE;
		
		// ================================================
		// E-mail récapitulatif
		// ================================================
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
			$mail->setFrom(EMAIL_WEBSITE, $sujet);

			if (ENV === 'PROD') {
				$mail->addAddress($tuteur->getEmail(), $tuteur->getLastname() . ' ' . $tuteur->getFirstname());
				$mail->addAddress(EMAIL_WABMASTER, $tuteur->getLastname() . ' ' . $tuteur->getFirstname());
			} else { // ENV DEV
				$mail->addAddress(EMAIL_WABMASTER, $tuteur->getLastname() . ' ' . $tuteur->getFirstname());
			}
			
			// Content
			$mail->isHTML(true); // Set email format to HTML
			$mail->Subject = $sujet;
			$mail->Body    = EmailTemplates::standardHTML($sujet, $mailContent);
			$mail->AltBody = EmailTemplates::standardText($sujet);
			$mail->send();
			
			return true;
		}
		catch (Exception $e) { }

		return false;
	}
}