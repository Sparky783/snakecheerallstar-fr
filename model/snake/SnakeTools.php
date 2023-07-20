<?php
namespace Snake;

use System\Database;
use System\ToolBox;
use Snake\Section;
use Snake\Payment;
use Snake\Tuteur;

/**
 * Outil pour la gestion du site des Snake.
 */
class SnakeTools
{
	/**
	 * Détermine la bonne section pour l'adhérent suivant ça date de naissance.
	 * 
	 * @param string $birthday Date de naissance de l'adhérent
	 * @return Section|false Section dans laquelle l'adhérent doit aller. Retourne False si aucune section ne correspond.
	 */
	public static function findSection(string $birthday): Section|false
	{
		list($annee, $mois, $jour) = explode('-', $birthday);
		$age = (int)(date("Y")) - (int)$annee;

		$sections = Section::getList();
		$currentDiff = 9999;
		$selectedSection = false;

		foreach ($sections as $section) {
			$diff = $age - $section->getMinAge();

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
	 * Retourne le nombre d'ahérents inscrit par section pour la saison souhaité.
	 * 
	 * @param string $saison Saison ou récupérer les adhérents. Par défaut la saison en cours est sélectionnée.
	 * @return array Liste du nombre d'adhérent par section.
	 */
	public static function nbAdherentsBySection(string $saison = ''): array
	{
		if ($saison === null) {
			$saison = self::getCurrentSaison();
		}

		$database = new DataBase();
		$sections = [];

		$result = $database->query(
			"SELECT * FROM adherents INNER JOIN sections ON adherents.id_section = sections.id_section WHERE saison=:saison",
			['saison' => $saison]
		);

		if ($result) {
			while ($data = $result->fetch()) {
				if(!isset($sections[$data['id_section']])) {
					$sections[$data['id_section']] = 0;
				}
					
				$sections[$data['id_section']] ++;
			}
		}

		return $sections;
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

		if(strlen($billNumber) > $numberSize) {
			return false;
		}

		for($i = $numberSize - strlen($billNumber); $i > 0; $i--) {
			$result .= "0";
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
			$tuteur->setPhone("00 00 00 00 00");
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
			$mail->addAddress($tuteur->GetEmail(), $tuteur->GetLastname() . " " . $tuteur->GetFirstname());
			$mail->addAddress(EMAIL_WABMASTER, $tuteur->GetLastname() . " " . $tuteur->GetFirstname());

			//Content
			$mail->isHTML(true); // Set email format to HTML
			$mail->Subject = "Facture d'inscription - " . TITLE;
			$mail->Body    = EmailTemplates::BillHTML($number, $payment, $tuteur);
			$mail->AltBody = EmailTemplates::TextFormat("Facture d'inscription - " . TITLE);

			$mail->send();

			return true;
		}
		catch (Exception $e) { }

		return false;
	}
	
	/**
	 * Envoie le récapitulatif d'inscription au tuteur sélectionné.
	 * 
	 * @param Payment $payment Paiement pour l'inscription.
	 * @param Tuteur $tuteur Tuteur à qui envoyer le mail. Si le tuteur n'est pas précisé, le mail sera envoyé au bureau du club.
	 * @return bool Retourne True si l'E-mail à été envoyé, sinon False.
	 */
	public static function sendRecap(Payment $payment, Tuteur $tuteur = null): bool
	{
		// Destinataire
		if($tuteur === null) {
			$tuteur = new Tuteur();
			$tuteur->setFirstname(TITLE);
			$tuteur->setEmail(EMAIL_CONTACT);
			$tuteur->setPhone("00 00 00 00 00");
		}

		// E-mail Facture
		$list = "";

		// Détail du paiement de la cotisation
		switch ($payment->getMethod()) {
			case EPaymentType::Espece:
				$list .= "<p>Paiement cotisation en espèce (En totalité, soit " . $payment->getFinalAmount() . " €).</p>";
				break;

			case EPaymentType::Cheque:
				if ($payment->getNbDeadlines() > 1) {
					$list .= "<p>Paiement cotisation en " . $payment->getNbDeadlines() . " fois par chèque (Veuillez apporter l'ensemble des chèques) :</p>";
					$list .= "<ul>";

					$i = 1;
					foreach ($payment->getDeadlines() as $deadline) {
						$list .= "<li>Chèque " . $i . " de " . $deadline . " €</li>";
						$i ++;
					}

					$list .= "</ul>";
				} else {
					$list .= "<p>Paiement cotisation par chèque en une fois, soit " . $payment->getFinalAmount() . " €.</p>";
				}
				break;
		}
		
		$adherents = $tuteur->getAdherents();

		if ($adherents !== null) {
			foreach ($adherents as $adherent) {
				$list .= "<p>Pour " . $adherent->getFirstname() . " " . $adherent->getLastname() . " :</p><ul>";

				if ($adherent->getMedicine()) {
					$list .= "<li>Formulaire d'autorisation médical pour " . $adherent->getFirstname() . " à remplir <a href='" . URL . "/content/afld.pdf' title='' target='_blank'>disponible ici</a></li>";
				}

				// Questionnaire de santé en fonction de l'age
				$age = ToolBox::age($adherent->getBirthday()->format("Y-m-d"));
				if ($age < 18) {
					$list .= "<li>Questionnaire de santé (Mineur) <a href='" . URL . "/content/questionnaire_sante_mineur.pdf' title='' target='_blank'>disponible ici</a> (obligatoire)</li>";
				} else {
					$list .= "<li>Questionnaire de santé (Majeur) <a href='" . URL . "/content/questionnaire_sante_majeur.pdf' title='' target='_blank'>disponible ici</a> (obligatoire)</li>";
				}

				$list .= "</ul><br /><br />";
			}
		}

		$list .= "<p>Pour chaque adhérent, veuillez fournir les éléments suivants :</p>";
		$list .= "<ul>";
		$list .= "<li>Formulaire de la FFFA <a href='" . URL . "/content/licence_FFFA.pdf' title='' target='_blank'>disponible ici</a> <b>(Attention le certificat médical doit être rempli sur cette feuille par le médecin)</b></li>";
		$list .= "<li>Autorisation parentale en cas d'accident <a href='" . URL . "/content/autorisation_parentale.pdf' title='' target='_blank'>disponible ici</a></li>";
		$list .= "<li>Formulaire de Sportmut <a href='" . URL . "/content/sportmut.pdf' title='' target='_blank'>disponible ici</a> (même si vous n'y adhérez pas)</li>";
		$list .= "<li>Déclaration d'accident MDS <a href='" . URL . "/content/declaration_accident.pdf' title='' target='_blank'>disponible ici</a></li>";
		$list .= "<li>Photocopie de la pièce d'identité</li>";
		$list .= "<li>Photo d'identité</li>";
		$list .= "</ul>";
		$list .= "<p>Veuillez remettre l'ensemble des éléments ci-dessus aux coachs ou aux membres du bureau présents pendant les cours.</p>";
		$list .= "<p>Merci de bien vouloir mettre les chèques à l'ordre de Snake Cheer All Star.</p>";


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

			if (ENV === "PROD") {
				$mail->addAddress($tuteur->GetEmail(), $tuteur->GetLastname() . " " . $tuteur->GetFirstname());
				$mail->addAddress(EMAIL_WABMASTER, $tuteur->GetLastname() . " " . $tuteur->GetFirstname());
			} else { // ENV DEV
				$mail->addAddress(EMAIL_WABMASTER, $tuteur->GetLastname() . " " . $tuteur->GetFirstname());
			}
			
			//Content
			$mail->isHTML(true); // Set email format to HTML

			$sujet = "Récapitulatif d'inscription " . TITLE;
			$html = "
				<p>Afin de finaliser l'inscription de votre/vos adhérent pour la saison de cheerleading, veuillez nous fournir les documents suivants :</p>
				".$list."
			";

			$mail->Subject = $sujet;
			$mail->Body    = EmailTemplates::StandardHTML($sujet, $html);
			$mail->AltBody = EmailTemplates::TextFormat($sujet);
			$mail->send();
			
			return true
		}
		catch (Exception $e) { }

		return false;
	}
}