<?php
namespace Snake;

use System\Database;
use System\ToolBox;
use Snake\Section;
use Snake\Payment;
use Snake\Tuteur;

class SnakeTools
{
	// Trouve la bonne section pour l'adhérent.
	static public function FindSection(string $birthday)
	{
		$selectedSection = false; // Default result

		list($annee, $mois, $jour) = explode('-', $birthday);
		$age = intval(date("Y")) - intval($annee);

		$database = new DataBase();
		$result = $database->Query(
			"SELECT * FROM sections WHERE saison=:saison",
			array("saison" => self::GetCurrentSaison())
		);

		if($result != null)
		{
			$currentDiff = 9999;

			while($data = $result->fetch())
			{
				$section = new Section($data);
				$diff = $age - $section->GetMinAge();

				if($diff >= 0)
				{
					if($diff < $currentDiff)
					{
						$currentDiff = $diff;
						$selectedSection = $section;
					}
				}
			}
		}

		return $selectedSection;
	}

	// Retourne une saison au format souhaité (Par défaut : YYYY-YYYY).
	// $date : format "AAAA-MM-DD"
	static public function GetSaison($date, $separateur = "-")
	{
		$year = intval(explode("-", $date)[0]);
		$month = intval(explode("-", $date)[1]);

		if($month <= 7)
			return ($year - 1) . $separateur . $year;
		else
			return $year . $separateur . ($year + 1);
	}
	
	// Retourne la saison en cours au format souhaité (Par défaut : YYYY-YYYY).
	static public function GetCurrentSaison($separateur = "-")
	{
		return self::GetSaison(date('Y-m-d'), $separateur);
	}
	
	// Retourne la saison précédente au format souhaité (Par défaut : YYYY-YYYY).
	static public function GetPreviousSaison($separateur = "-")
	{
		$year = intval(date('Y'));
		$year--;
		
		return self::GetSaison($year . date('-m-d'), $separateur);
	}

	// Crée les échéances de paiement.
	static public function MakeDeadlines($price, $number)
	{
		if($number > 0 && $price > 0)
		{
			$deadlines = array();

			if($number == 1)
			{
				$deadlines[] = $price;
			}
			else
			{
				$monthlyPrice = round($price / $number);
				
				for($i = 0; $i < $number - 1; $i++)
					$deadlines[] = $monthlyPrice;
				
				$deadlines[] = $price - ($monthlyPrice * ($number - 1)); // last month
			}
			
			return $deadlines;
		}
		
		return false;
	}
	
	// Retourne le nombre d'ahérents inscrit par section.
	static public function NbBySection($saison = null)
	{
		if($saison == null)
			$saison = self::GetCurrentSaison();

		$database = new DataBase();
		$sections = array();
		$rech = $database->Query("SELECT * FROM sections");

		if($rech)
		{
			while($data = $rech->fetch())
				$sections[$data['id_section']] = 0;

			$result = $database->Query(
				"SELECT * FROM adherents INNER JOIN sections ON adherents.id_section = sections.id_section WHERE saison=:saison",
				array("saison" => $saison)
			);

			if($result)
			{
				while($data = $result->fetch())
				{
					if(!isset($sections[$data['id_section']]))
						$sections[$data['id_section']] = 0;
						
					$sections[$data['id_section']] ++;
				}
			}
	
			return $sections;
		}
		
		return false;
	}

	// Format le numéro de la facture pour quelle soit à la taille souhaité.
	static public function FormatBillNumber($number, $size)
	{
		$number = $number . "";
		$result = "";

		if(strlen($number) < $size)
		{
			for($i = $size - strlen($number); $i > 0; $i--)
				$result .= "0";
		}

		$result .= $number;

		return $result;
	}

	// Envoie une facture au tuteur sélectionné.
	static public function SendBill(Payment $payment, Tuteur $tuteur = null)
	{
		// Destinataire
		if($tuteur == null)
		{
			$tuteur = new Tuteur();
			$tuteur->SetFirstname(TITLE);
			$tuteur->SetEmail(EMAIL_CONTACT);
			$tuteur->SetPhone("00 00 00 00 00");
		}

		// Concaténation des infos
		$number = self::FormatBillNumber($payment->GetId() + 100, 5);

		// E-mail Facture
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
			$mail->setFrom(EMAIL_WEBSITE, 'Facture | ' . TITLE);
			$mail->addAddress($tuteur->GetEmail(), $tuteur->GetLastname() . " " . $tuteur->GetFirstname());
			$mail->addAddress(EMAIL_WABMASTER, $tuteur->GetLastname() . " " . $tuteur->GetFirstname());

			//Content
			$mail->isHTML(true); // Set email format to HTML
			$mail->Subject = "Facture d'inscription - " . TITLE;
			$mail->Body    = EmailTemplates::BillHTML($number, $payment, $tuteur);
			$mail->AltBody = EmailTemplates::TextFormat("Facture d'inscription - " . TITLE);

			$mail->send();
			$resultEmail = true;
		}
		catch (Exception $e) { }

		return $resultEmail;
	}
	
	// Envoie le récapitulatif d'inscription au tuteur sélectionné.
	static public function SendRecap(Payment $payment, Tuteur $tuteur = null)
	{
		// Destinataire
		if($tuteur == null)
		{
			$tuteur = new Tuteur();
			$tuteur->SetFirstname(TITLE);
			$tuteur->SetEmail(EMAIL_CONTACT);
			$tuteur->SetPhone("00 00 00 00 00");
		}

		// E-mail Facture
		$list = "";

		// Détail du paiement de la cotisation
		if($payment->GetMethod() == Payment::$METHODS['Espece'])
		{
			$list .= "<p>Paiement cotisation en espèce (En totalité, soit " . $payment->GetFinalAmount() . " €).</p>";
		}
		else if ($payment->GetMethod() == Payment::$METHODS['Cheque'])
		{
			if($payment->GetNbDeadlines() > 1)
			{
				$list .= "<p>Paiement cotisation en " . $payment->GetNbDeadlines() . " fois par chèque (Veuillez apporter l'ensemble des chèques) :</p>";
				$list .= "<ul>";

				$i = 1;
				foreach($payment->GetDeadlines() as $deadline)
				{
					$list .= "<li>Chèque " . $i . " de " . $deadline . " €</li>";
					$i ++;
				}

				$list .= "</ul>";
			}
			else
			{
				$list .= "<p>Paiement cotisation par chèque en une fois, soit " . $payment->GetFinalAmount() . " €.</p>";
			}
		}
		
		$adherents = $tuteur->GetAdherents();

		if($adherents != null) {
			foreach($adherents as $adherent)
			{
				$age = ToolBox::Age($adherent->GetBirthday()->format("Y-m-d"));

				$list .= "<p>Pour " . $adherent->GetFirstname() . " " . $adherent->GetLastname() . " :</p><ul>";

				if($adherent->GetMedicine())
					$list .= "<li>Formulaire d'autorisation médical pour " . $adherent->GetFirstname() . " à remplir <a href='" . URL . "/content/afld.pdf' title='' target='_blank'>disponible ici</a></li>";


				// Questionnaire de santé en fonction de l'age
				if($age < 18)
					$list .= "<li>Questionnaire de santé (Mineur) <a href='" . URL . "/content/questionnaire_sante_mineur.pdf' title='' target='_blank'>disponible ici</a> (obligatoire)</li>";
				else
					$list .= "<li>Questionnaire de santé (Majeur) <a href='" . URL . "/content/questionnaire_sante_majeur.pdf' title='' target='_blank'>disponible ici</a> (obligatoire)</li>";

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
			$mail->setFrom(EMAIL_WEBSITE, 'Récapitulatif d\'inscription | ' . TITLE);

			if(ENV == "PROD") {
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
			
			$resultEmail = true;
		}
		catch (Exception $e) { }

		return $resultEmail;
	}
}