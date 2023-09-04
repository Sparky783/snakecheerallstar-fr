<?php
namespace Snake;

use DateTime;
use System\ToolBox;
use Snake\Section;
use Snake\Payment;
use Snake\Tuteur;
use Snake\EmailTemplates;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once ABSPATH. 'model/PHPMailer/src/Exception.php';
require_once ABSPATH. 'model/PHPMailer/src/PHPMailer.php';
require_once ABSPATH. 'model/PHPMailer/src/SMTP.php';

/**
 * Outil pour la gestion du site des Snake.
 */
class SnakeTools
{
	/**
	 * Détermine la bonne section pour l'adhérent suivant ça date de naissance.
	 * 
	 * @param DateTime $birthday Date de naissance de l'adhérent
	 * @return Section|false Section dans laquelle l'adhérent doit aller. Retourne False si aucune section ne correspond.
	 */
	public static function findSection(DateTime $birthday): Section|false
	{
		$year = (int)$birthday->format('Y');

		$sections = Section::getList();
		$currentDiff = 9999;
		$selectedSection = false;

		foreach ($sections as $section) {
			$diff = $section->getMaxYear() - $year;

			if ($diff >= 0) {
				if ($diff < $currentDiff) {
					$currentDiff = $diff;
					$selectedSection = $section;
				}
			}
		}

		return $selectedSection;
	}

	/**
	 * Retourne une saison en fonction d'une date.
	 * 
	 * @param string $date Date au format "AAAA-MM-DD"
	 * @return string Format: "YYYY-YYYY"
	 */
	public static function getSaison(string $date): string
	{
		$year = (int)(explode("-", $date)[0]);
		$month = (int)(explode("-", $date)[1]);

		if ($month <= 7) {
			return ($year - 1) . '-' . $year;
		}

		return $year . '-' . ($year + 1);
	}
	
	/**
	 * Retourne la saison en cours.
	 * 
	 * @return string Format: "YYYY-YYYY"
	 */
	public static function getCurrentSaison(): string
	{
		return self::getSaison(date('Y-m-d'));
	}
	
	/**
	 * Retourne la saison précédente.
	 * 
	 * @return string Format: "YYYY-YYYY"
	 */
	public static function getPreviousSaison(): string 
	{
		$year = (int)date('Y');
		$year--;
		
		return self::getSaison($year . date('-m-d'));
	}

	/**
	 * Crée les échéances (montants) de paiement.
	 * 
	 * @param float $totPrice Montant total à découper en échéance.
	 * @param int $number Nombre d'échéances souhaité.
	 * @return array|false Liste des montants pour chaque échéance. Retourne False en cas d'erreur.
	 */
	public static function makeDeadlines(float $totPrice, int $number): array|false
	{
		if($number <= 0 || $totPrice <= 0) {
			return false;
		}

		$deadlines = [];

		if ($number === 1) {
			$deadlines[] = $totPrice;
		} else {
			$monthlyPrice = round($totPrice / $number);
			
			for ($i = 0; $i < $number - 1; $i++) {
				$deadlines[] = $monthlyPrice;
			}
			
			$deadlines[] = $totPrice - ($monthlyPrice * ($number - 1)); // last month
		}
		
		return $deadlines;
	}

	/**
	 * Format le numéro de la facture pour quelle soit à la taille souhaité.
	 * Exemple: 15 => "00015"
	 * 
	 * @param int $billNumber Numéro de la facture 
	 * @param int $numberSize Nombre de charactère devant à apparaitre sur la facture pour le numéro.
	 * @return string|false Retourne le numéro formaté, sinon False en cas d'erreur.
	 */
	public static function formatBillNumber(int $billNumber, int $numberSize): string|false
	{
		$billNumber = $billNumber . ''; // Convertie en string
		$result = '';

		if (strlen($billNumber) > $numberSize) {
			return false;
		}

		for ($i = $numberSize - strlen($billNumber); $i > 0; $i--) {
			$result .= '0';
		}

		$result .= $billNumber;

		return $result;
	}

	/**
	 * Envoie une facture au tuteur sélectionné.
	 * 
	 * @param Payment $payment Paiement à afficher sur la facture.
	 * @param Tuteur $tuteur Tuteur à qui envoyer le mail. Si le tuteur n'est pas précisé, le mail sera envoyé au bureau du club.
	 * @return bool Retourne True si l'E-mail à été envoyé, sinon False.
	 */
	public static function sendBill(Payment $payment, Tuteur $tuteur = null): bool
	{
		// Destinataire
		if($tuteur === null) {
			$tuteur = new Tuteur();
			$tuteur->setFirstname(TITLE);
			$tuteur->setEmail(EMAIL_CONTACT);
			$tuteur->setPhone('00 00 00 00 00');
		}

		// Concaténation des infos
		$number = self::formatBillNumber($payment->getId() + 100, 5);

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
			$mail->Body    = EmailTemplates::billHtml($number, $payment, $tuteur);
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
		
		// Documents poour les adhérents
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

	public static function convertPaymentAmountToWords(float $amount): string
	{
		$entier = floor($amount);
		$decimal = $amount - floor($amount);

		$price = ToolBox::convertNumberToString($entier) . ' euros';

		if ($decimal > 0) {
			$decimal = floor($decimal * 100);
			$price .= ' ' . ToolBox::convertNumberToString($decimal) . ' cts';
		}

		return $price;
	}
}