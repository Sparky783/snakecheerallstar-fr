<?php
use System\WebSite;
use System\ToolBox;
use System\Session;
use Snake\Adherent;
use Snake\Payment;

// ==== Access security ====
if(!ToolBox::SearchInArray($session->admin_roles, array("admin", "webmaster", "member",)))
	WebSite::Redirect("login", true);
// =========================

global $gmm;

$session = Session::GetInstance();
$adherent = Adherent::GetById(intval($gmm->GetValue('id')));

// Stock les infos de l'adhérent dans la session pour le réutiliser dans l'API.
$session->selectedAdherent = serialize($adherent);

$destsBillHtml = "<option value='snake'>" . TITLE ."</option>"; // Modal send bill
$htmlName = $adherent->GetFirstname() . " " . $adherent->GetLastname();

// Info élève
$html = "<h5>Elève</h5><hr /><p>";
$html .= "Date de naissance : " . ToolBox::FormatDate($adherent->GetBirthday(), false) . "<br />";

// Affichage spécifque à la saison 2019 - 2020.
if($session->selectedSaison == "2019-2020")
{
	if($adherent->GetTenue())
		$html .= "Tenue acheté<br />";
	else
		$html .= "Tenue loué<br />";
}
else
{
	if(!$adherent->GetTenue())
		$html .= "Tenue acheté avec la cotisation cette saison.<br />";
}

if($adherent->GetMedicine())
	$html .= "Traitement médical : " . $adherent->GetMedicineInfo() . "<br />";
else
	$html .= "Pas de traitement médical.<br />";

$priceHtml = "";
switch($adherent->GetPayment()->GetMethod())
{
	case Payment::$METHODS['Internet']:
		$priceHtml = $adherent->GetPayment()->GetFinalAmount() . "€ - Par internet";
		break;

	case Payment::$METHODS['Espece']:
		$priceHtml = $adherent->GetPayment()->GetFinalAmount() . "€ - Espèce";
		break;

	case Payment::$METHODS['Cheque']:
		{
			$deadlines = $adherent->GetPayment()->GetDeadlines();
			$nbDealines = $adherent->GetPayment()->GetNbDeadlines();
			$priceHtml = $adherent->GetPayment()->GetFinalAmount() . "€ - " . $nbDealines ." Chèques";

			if($nbDealines > 1)
			{
				$priceHtml .= "<ul>";

				foreach($deadlines as $deadline)
					$priceHtml .= "<li>" . $deadline . " €</li>";

				$priceHtml .= "</ul>";
			}
		}
		break;

	case Payment::$METHODS['Virement']:
		$priceHtml = $adherent->GetPayment()->GetFinalAmount() . "€ - Virement";
		break;
}

if(ToolBox::SearchInArray($session->admin_roles, array("admin", "tresorier", "secretaire")))
	$html .= "Cotisation: " . $priceHtml . "<br />";

$html .= "</p>";

// Info tuteurs
$tuteurs = $adherent->GetTuteurs();

$html .= "<h5>Tuteurs</h5><hr />";
foreach($tuteurs as $tuteur)
{
	$html .= "
		<div class='alert alert-info'>
			(" . $tuteur->GetStatus() . ") " . $tuteur->GetFirstname() . " " . $tuteur->GetLastname() . "<br />
			" . $tuteur->GetEmail() . "<br />
			" . $tuteur->GetPhone() . "
		</div>
	";

	$destsBillHtml .= "<option value='" . $tuteur->GetId() . "'>" . $tuteur->GetFirstname() . " " . $tuteur->GetLastname() ."</option>";
}

// Actions
$actionsHtml = '<button id="addTuteurButton" class="dropdown-item" data-toggle="modal" data-target="#addTuteurModal">Ajouter un tuteur</button>';

if(ToolBox::SearchInArray($session->admin_roles, array("admin", "tresorier", "secretaire")))
	$actionsHtml .= '<button class="dropdown-item" data-toggle="modal" data-target="#sendBillModal">Envoyer une facture</button>';

if(ToolBox::SearchInArray($session->admin_roles, array("admin", "secretaire")))
	$actionsHtml .= '<button class="dropdown-item" data-toggle="modal" data-target="#sendRecapModal">Envoyer le récapitulatif d\'inscription</button>';
?>