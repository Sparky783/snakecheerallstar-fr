<?php
// ================================
// ==== Controleur Inscription ====
// ================================
// ==== Paiement de la cotisation et de la tenue ====

include_once(ABSPATH . "model/system/Session.php");
include_once(ABSPATH . "model/snake/Inscription.php");

global $router, $gmm;
$session = Session::getInstance();

// Préparation de l'affichage
$script = "";
$html = "
	<div id='inscriptionMenu' class='col-md-12'>
		<div>
			<h2>Paiement de la cotisation</h2>
		</div>
	</div>

	<div id='guide' class='col-md-4'>
		<div class='card card-snake'>
			<div class='card-header'>
				<h4>Guide d'aide</h4>
			</div>
			<div class='card-body'>
				<p class='title'>Quatrième étape</p>
				<p>
					Surement l'étape la moins cool, mais bon ...<br />
					Cette étape vous invite à choisir un moyen permettant de régler la cotisation d'inscription. Cette cotisation inclut :
				</p>
				<ul>
					<li>Le prix de l'inscription</li>
					<li>L'achat de la tenue</li>
					<li>Une remise pour les fratries (Appliqué automatiquement)</li>
				</ul>
				<p>
					Pour s'inscrire plusieurs options sont disponibles :
				</p>
				<p class='alert alert-info'>
					<b>En espèce :</b> vous devrez fournir en totalité le montant de la cotisation indiqué au coach présent au cours ou à un membre du bureau.
				</p>
				<p class='alert alert-info'>
					<b>Par chèque :</b> avec ce mode de paiement vous pouvez payer en plusieurs fois (4 fois max). Pour cela choisissez l'option qui vous convient. Le montant des chèques à fournir sera calculé automatiquement à la validation de cette étape.
				</p>
				<!--<p class='alert alert-info'>
					<b>En ligne :</b> grâce au service sécurisé PayPal, vous pouvez payer instantanément votre cotisation sans bouger de chez vous. Et ça, c'est cool !!!
				</p>-->
			</div>
		</div>
	</div>

	<div id='inscription' class='col-md-8'>
		<div id='payment'>
";

$response = "";
$montant = $session->inscription->GetPayment()->GetFinalAmount();
$adhs = $session->inscription->GetAdherents();

$needUniform = false;
foreach($adhs as $adh)
{
	if(!$adh->GetTenue())
	{
		$needUniform = true;
		break;
	}
}

// Vérifie si tout les membres peuvent être inscrit et retourne le prix sinon retour False;
if($montant !== false)
{
	// Affichage des réductions
	$reductions = "<div id='reductions'><span class='title'>Réduction(s) automatiquement(s) appliquée(s) :</span>";
	if(count($session->inscription->GetPayment()->GetReductions()) > 0)
	{
		$reductions .= "<ul>";

		foreach($session->inscription->GetPayment()->GetReductions() as $reduction)
		{
			if($reduction->GetType() == Reduction::$TYPE['Amount'])
				$reductions .= "<li class='reduction'>-" . $reduction->GetValue() . "€ - " . $reduction->GetSujet() . "</li>";

			if($reduction->GetType() == Reduction::$TYPE['Percentage'])
				$reductions .= "<li class='reduction'>-" . $reduction->GetValue() . "% - " . $reduction->GetSujet() . "</li>";
		}

		$reductions .= "</ul>";
	}
	else
	{
		$reductions .= "<br /><i>Vous ne bénéficiez d'aucune réduction automatique.</i>";
	}

	$reductions .= "
		<div id='reductionOptions'>
			<div id='reductionList'>
				<div class='custom-control custom-switch'>
					<input id='passSportReduction' class='custom-control-input' type='checkbox' name='passSport' />
					<label class='custom-control-label' for='passSportReduction'>Pass sport <small>(Sous présentation d'un justificatif obligatoire)</small></label>
				</div>
			</div>
		</div>
	";
}
else
{
	$session->inscription = new Inscription();
}

$enableBtn = "false";

if($montant !== false)
{
	$html .= "
		<div id='infoPrice'>
			<p class='text-center'>
				Voici le montant à régler pour l'ensemble des cotisations de cette saison.
	";

	if($needUniform)
		$html .= "<br /><i>Ce montant comprend le prix de la tenue</i>";
		
	$html .= "</p>
			<p class='text-center'>
				<span class='price'><span class='price-amount'>" . $montant . "</span>€</span>
			</p>
			" . $reductions . "
		</div>
	";

	if(!$session->inscription->GetPayment()->IsDone())
	{
		$html .= "
			<div id='paymentOptions'>
				<h5>Choisissez un moyen de paiement.</h5>
				<div id='selectOption' class='row selection'>
					<div class='col-sm-6'><button class='btn btn-snake btn-block' data-target='optionEspece'>Espèce<br /><small>(La totalité uniquement)</small></button></div>
					<div class='col-sm-6'><button class='btn btn-snake btn-block' data-target='optionCheque'>Chèque<br /><small>(jusqu'à 4 fois)</small></button></div>
					<!--<div class='col-sm-4'><button class='btn btn-snake btn-block' data-target='optionEnLigne'>En ligne<br /><small>(rapide et simple)</small></button></div>-->
				</div>
			</div>
			<div id='selectDetails' class='details'>
				<div id='optionEspece'>
					Réglez en espèce la <b>totalité</b> de la cotisation auprès de nos coach ou de notre bureau.
				</div>
				<div id='optionCheque'>
					Réglez la cotisation en 1 ou plusieurs fois. Choisissez une option suivante:
					<div class='form-group text-center'>
						<div class='custom-control custom-radio'>
							<input id='inputRadioEcheance1' class='custom-control-input' name='deadlinesInput' type='radio' value='1'>
							<label class='custom-control-label' for='inputRadioEcheance1'>1 fois</label>
						</div>
						<div class='custom-control custom-radio'>
							<input id='inputRadioEcheance2' class='custom-control-input' name='deadlinesInput' type='radio' value='2'>
							<label class='custom-control-label' for='inputRadioEcheance2'>2 fois</label>
						</div>
						<div class='custom-control custom-radio'>
							<input id='inputRadioEcheance3' class='custom-control-input' name='deadlinesInput' type='radio' value='3'>
							<label class='custom-control-label' for='inputRadioEcheance3'>3 fois</label>
						</div>
						<div class='custom-control custom-radio'>
							<input id='inputRadioEcheance4' class='custom-control-input' name='deadlinesInput' type='radio' value='4'>
							<label class='custom-control-label' for='inputRadioEcheance4'>4 fois</label>
						</div>
					</div>
				</div>
				<!--<div id='optionEnLigne'>
					Payez par carte bancaire directement depuis notre site internet.<br />
					Système sécurisé par PayPal.
					<div id='paypal-button-container'></div>
					<script src='https://www.paypal.com/sdk/js?client-id=" . PAYPAL_CLIENT_ID . "&currency=EUR'></script>
					<script>
						paypal.Buttons({
							createOrder: function(data, actions) {
								return actions.order.create({
									purchase_units: [{
										amount: {
											currency_code: 'EUR',
											value: '" . $montant . "'
										},
										description: 'Cotisation Snake Cheer All Star saison " . SnakeTools::GetCurrentSaison() . "'
									}]
								});
							},

							onApprove: function(data, actions) {
								return actions.order.capture().then(function(details) {
									$('#validModal').modal();
									$.ajax({
										url: '" . $router->GetAPI("approve_order") . "',
										type: 'POST',
										data: {
											orderID: data.orderID
										},
										success: function(response) {
											InscriptionPaymentResponse(response);
										}
									});
								});
							}
						}).render('#paypal-button-container');
					</script>

					<div id='validModal' class='modal fade' tabindex='-1' role='dialog' aria-labelledby='validModal' aria-hidden='true'>
						<div class='modal-dialog modal-dialog-centered' role='document'>
							<p class='text-center'>
								<span class='spinner-border text-light' role='status'></span><br />
								Validation en cours ...
							</p>
						</div>
					</div>
				</div>-->
			</div>
		";
	}
	else
	{
		$html .= "
			<div id='resultPayment' class='alert alert-success text-center'>
				<i class='fas fa-check-circle'></i>
				Le paiement à bien été effectué.
			</div>
		";

		$enableBtn = "true";
	}
}
else
{
	$html .= "
		<p class='text-center'>
			L'une des personnes que vous souhaitez inscrire ne rentre dans aucune des catégories.<br /><br />Veuillez modifier l'inscription.<br /><button class='btn btn-snake' onClick='location.reload();'>Modifier</button>
		</p>
	";
}

$html .= "
		</div>
	</div>

	<div id='nextButton' class='col-md-12'>
		<div class='text-center'>
			<button id='validButton' class='btn btn-primary'>Valider et terminer</button>
		</div>
	</div>

	<script type='text/javascript' src='view/js/inscription_4.js'></script>
	<script>
		$(document).ready(function(){
			$('#inscription').InscriptionManager({
				UrlApi: '" . $router->GetAPI("") . "',
				EnableBtn: " . $enableBtn . "
			});
		});
	</script>
";
?>