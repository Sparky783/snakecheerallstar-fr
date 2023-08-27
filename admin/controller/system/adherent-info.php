<?php
use System\WebSite;
use System\ToolBox;
use System\Session;
use Snake\Adherent;
use Snake\EPaymentType;
use Snake\EUniformOption;
use Snake\Payment;

// ==== Access security ====
if (!ToolBox::searchInArray($session->admin_roles, ['admin', 'webmaster', 'member'])) {
	WebSite::redirect('login', true);
}
// =========================

global $gmm;

$session = Session::getInstance();
$adherent = Adherent::getById((int)$gmm->getValue('id'));

// Stock les infos de l'adhérent dans la session pour le réutiliser dans l'API.
$session->selectedAdherent = serialize($adherent);

$destsBillHtml = "<option value='snake'>" . TITLE ."</option>"; // Modal send bill
$htmlName = "{$adherent->getFirstname()} {$adherent->getLastname()}";

// Info élève
$birthday = ToolBox::formatDate($adherent->getBirthday(), false);
$age = ToolBox:: age($adherent->getBirthday());

$html = <<<HTML
	<h5>Elève</h5><hr />
	<p>
		Date de naissance : $birthday ($age ans)<br />
	HTML;

switch ($adherent->getUniformOption()) {
	case EUniformOption::Rent:
		$html .= "Tenue loué<br />";
		break;

	case EUniformOption::Buy:
		$html .= "Tenue acheté<br />";
		break;

	default:
		$html .= "Possède déjà la tenue<br />";
		break;
}

if ($adherent->hasMedicine()) {
	$html .= "Traitement médical : " . $adherent->getMedicineInfo() . "<br />";
} else {
	$html .= "Pas de traitement médical.<br />";
}

$priceHtml = "";

switch ($adherent->getPayment()->getMethod()) {
	case EPaymentType::Internet:
		$priceHtml = $adherent->getPayment()->getFinalAmount() . "€ - Par internet";
		break;

	case EPaymentType::Espece:
		$priceHtml = $adherent->getPayment()->getFinalAmount() . "€ - Espèce";
		break;

	case EPaymentType::Cheque:
		$deadlines = $adherent->getPayment()->getDeadlines();
		$nbDealines = $adherent->getPayment()->getNbDeadlines();
		$priceHtml = $adherent->getPayment()->getFinalAmount() . "€ - " . $nbDealines ." Chèques";

		if ($nbDealines > 1) {
			$priceHtml .= "<ul>";

			foreach($deadlines as $deadline)
				$priceHtml .= "<li>" . $deadline . " €</li>";

			$priceHtml .= "</ul>";
		}
		break;

	case EPaymentType::Virement:
		$priceHtml = $adherent->getPayment()->getFinalAmount() . "€ - Virement";
		break;
}

if(ToolBox::searchInArray($session->admin_roles, ['admin', 'tresorier', 'secretaire'])) {
	$html .= "Cotisation: " . $priceHtml . "<br />";
}

$html .= "</p>";

// Info tuteurs
$tuteurs = $adherent->getTuteurs();
$html .= "<h5>Tuteurs</h5><hr />";

foreach ($tuteurs as $tuteur) {
	$html .= "
		<div class='alert alert-info'>
			(" . $tuteur->getStatus() . ") " . $tuteur->getFirstname() . " " . $tuteur->getLastname() . "<br />
			" . $tuteur->getEmail() . "<br />
			" . $tuteur->getPhone() . "
		</div>
	";

	$destsBillHtml .= "<option value='" . $tuteur->getId() . "'>" . $tuteur->getFirstname() . " " . $tuteur->getLastname() ."</option>";
}

// Actions
$actionsHtml = '<button id="addTuteurButton" class="dropdown-item" data-toggle="modal" data-target="#addTuteurModal">Ajouter un tuteur</button>';

if(ToolBox::searchInArray($session->admin_roles, ['admin', 'tresorier', 'secretaire'])) {
	$actionsHtml .= '<button class="dropdown-item" data-toggle="modal" data-target="#sendBillModal">Envoyer une facture</button>';
}

if(ToolBox::searchInArray($session->admin_roles, ['admin', 'secretaire'])) {
	$actionsHtml .= '<button class="dropdown-item" data-toggle="modal" data-target="#sendRecapModal">Envoyer le récapitulatif d\'inscription</button>';
}
?>