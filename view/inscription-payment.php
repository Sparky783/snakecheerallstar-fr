<?php
use Snake\SnakeTools;

global $router;
?>

<section id='payment' class='row'>
	<div id='paymentMenu' class='col-md-12 inscription-menu'>
		<h2>Paiement de la cotisation</h2>
	</div>

	<div class='col-md-4 guide'>
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
				<p class='alert alert-info'>
					<b>En ligne :</b> grâce au service sécurisé PayPal, vous pouvez payer instantanément votre cotisation sans bouger de chez vous. Et ça, c'est cool !!!
				</p>
			</div>
		</div>
	</div>

	<div class='col-md-8'>
		<div id='paymentPrice'>
			<p class='text-center'>
				Voici le montant à régler pour l'ensemble des cotisations de cette saison. <br />
				<i>Ce montant comprend le prix de la tenue</i>
			</p>
			<p class='text-center'>
				<span class='price'><span class='price-amount'></span>€ à payer</span>
			</p>
			<div id='paymentreductions'><span class='title'>Réduction(s) appliquée(s) :</span>
				<ul></ul>
			</div>
		</div>

		<div id='paymentOptions'>
			<h5>Choisissez un moyen de paiement.</h5>
			<div class='row'>
				<div class='col-sm-4'><button class='btn btn-snake btn-block' data-target='optionEspece'>Espèce<br /><small>(La totalité uniquement)</small></button></div>
				<div class='col-sm-4'><button class='btn btn-snake btn-block' data-target='optionCheque'>Chèque<br /><small>(jusqu'à 4 fois)</small></button></div>
				<div class='col-sm-4'><button class='btn btn-snake btn-block' data-target='optionEnLigne'>En ligne<br /><small>(rapide et simple)</small></button></div>
			</div>
		</div>
		<div id='paymentDetails'>
			<div id='optionEspece' class='paymentOption'>
				Réglez en espèce la <b>totalité</b> de la cotisation auprès de nos coach ou d'un membre du bureau.
			</div>

			<div id='optionCheque' class='paymentOption'>
				Réglez la cotisation en une ou plusieurs fois. Choisissez une option suivante:
				<div class='form-group text-center'>
					<div class='custom-control custom-radio'>
						<input id='inputRadioEcheance1' class='custom-control-input' name='deadlinesInput' type='radio' value='1' checked>
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

			<div id='optionEnLigne' class='paymentOption'>
				Payez par carte bancaire directement depuis notre site internet.<br />
				Système sécurisé par PayPal.

				<div id='paypalButtonContainer'></div>

				<script src='https://www.paypal.com/sdk/js?client-id=<?= PAYPAL_CLIENT_ID ?>&currency=EUR'></script>
				<script>
					let saison = '<?= SnakeTools::getCurrentSaison() ?>';
					paypal.Buttons({
						createOrder: InscriptionManager.payPalCreateOrder,
						onApprove: InscriptionManager.payPalOnApporve
					}).render('#paypalButtonContainer');
				</script>

				<div id='paymentResult'></div>
			</div>
		</div>

		<div class='text-center'>
			<button class='next-button btn btn-primary btn-lg mt-4 mb-4' type='button'>Valider et continuer</button>
		</div>
	</div>
</section>

<div id='paymentPayPalWaitting' class='modal fade' tabindex='-1' aria-labelledby='paymentPayPalWaitting' aria-hidden='true'>
	<div class='modal-dialog' role='document'>
		<div class="modal-content">
			<p class='text-center'>
				<span class='spinner-border text-light' role='status'></span><br />
				Validation en cours ...
			</p>
		</div>
	</div>
</div>