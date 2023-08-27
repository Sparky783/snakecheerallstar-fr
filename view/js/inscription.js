$(document).ready(function(){
	InscriptionManager.init();
});

function serializeForm(form) {
	let inputs = form.find(':input');
	let values = {};

	inputs.each(function(){
		switch ($(this).attr('type')) {
			case 'checkbox':
				values[this.name] = $(this).prop('checked');
				break;

			default:
				values[this.name] = $(this).val();
				break;
		}
	});

	return values;
}

let InscriptionManager = {
	state: '',
	adherentIndex: 0,
	tuteurIndex: 0,
	hasChanged: true,
	isPayed: false,
	amountToPay: 100,

	init: function () {
		$('#tuteurs').hide();
		$('#authorisation').hide();
		$('#payment').hide();
		$('#validation').hide();

		this.initStepButtons();
		this.initAdherents();
		this.initTuteurs();
		this.initAuthorisation();
		this.initPayment();
	},

	initStepButtons: function () {
		$('#stepAdherents').click(function() {
			InscriptionManager.hasChanged = true;
			InscriptionManager.changeStep('adherents');
		});

		$('#stepTuteurs').click(function() {
			InscriptionManager.hasChanged = true;
			InscriptionManager.changeStep('tuteurs');
		});

		$('#stepAuthorisation').click(function() {
			InscriptionManager.hasChanged = true;
			InscriptionManager.changeStep('authorisation');
		});

		$('#stepPayment').click(function() {
			InscriptionManager.validInformation();
		});
	},

	initAdherents: function () {
		$('.adherentsAddBtn').click(function(){
			InscriptionManager.addAdherentDom();
		});

		$('#adherents .nextButton button').click(function(){
			InscriptionManager.changeStep('tuteurs');
		});
		
		InscriptionManager.addAdherentDom();
	},

	initTuteurs: function () {
		$('.tuteursAddBtn').click(function(){
			InscriptionManager.addTuteurDom();
		});

		$('#tuteurs .nextButton button').click(function(){
			InscriptionManager.changeStep('authorisation');
		});
		
		InscriptionManager.addTuteurDom();
	},

	initAuthorisation: function () {
		$('#authorisation .nextButton button').click(function(){
			InscriptionManager.validInformation();
		});
	},

	initPayment: function () {
		$('#paymentDetails .paymentOption').hide();
		$('#paymentOptions button').removeClass('active');

		$('#paymentOptions button').click(function(){
			$('#paymentDetails .paymentOption').hide();
			$('#' + $(this).data('target')).show();
			$(this).addClass('active');
		});

		$('#paymentResult').hide();

		$('#payment .nextButton button').click(function(){
			InscriptionManager.validPayment();
		});
	},

	addAdherentDom: function () {
		let dom = `
			<form class='adherent card'>
				<div class='card-header'>
					<span class='adherent-title'>Adhérent</span>
					<button class='remove-button btn btn-danger' type='button'><i class='fas fa-trash'></i></button>
				</div>
				<div class='row card-body'>
					<div class='col-sm-6 form-group'>
						<label for='nomInput'>Nom</label>
						<input class='form-control' name='lastname' type='text'>
					</div>
					<div class='col-sm-6 form-group'>
						<label for='prenomInput'>Prénom</label>
						<input class='form-control' name='firstname' type='text'>
					</div>
					<div class='col-sm-6 form-group'>
						<label for='birthdayInput'>Date de naissance</label>
						<input class='form-control' name='birthday' type='date'>
					</div>
					<div class='clearfix'></div>
					<div class='col-sm-6 form-group'>
						<div class='form-check form-check-inline'>
							<label class='form-check-label'>Traitement médical :</label>
							<input class='form-control' name='medecineInfo' type='text'>
						</div>
					</div>
					<div class='col-sm-12 form-group'>
						<div class='custom-control custom-switch'>
							<input id='passSportInput_` + this.adherentIndex + `' class='custom-control-input' type='checkbox' name='passSport' />
							<label class='custom-control-label' for='passSportInput_` + this.adherentIndex + `'>Pass Sport <small>(Sous présentation d'un justificatif obligatoire)</small></label>
							<input id='passSportCodeInput_` + this.adherentIndex + `' class='form-control' name='passSportCode' type='text'>
						</div>
						<small class='form-text text-muted'>
							
						</small>
					</div>
					<!--<div class='col-sm-12 form-group'>
						<div class='form-check form-check-inline'>
							<label class='form-check-label'>Adhésion Sportmut (optionnel) :</label>
						</div>
						<div class='form-check form-check-inline'>
							<input class='form-check-input inputRadioSportmut1' name='sportmut' type='radio' value='yes'>
							<label class='form-check-label inputRadioSportmut1'>Oui</label>
						</div>
						<div class='form-check form-check-inline'>
							<input class='form-check-input inputRadioSportmut2' name='sportmut' type='radio' value='no'>
							<label class='form-check-label inputRadioSportmut2'>Non</label>
						</div>
						<small class='form-text text-muted'>
							La Sportmut est une assurance pour les sportifs. Si elle est choisi, elle viens remplacer l'assurance de la FFFA (Fédération Française de Football Américain).
							La Sportmut est à rajouter en plus du pris de la cotisation, ce qui n'est pas le cas pour l'assurancede la fédération.<br />
							Pour plus d'information, consultez le document d'adhésion disponible <a href='' title=''>ici</a>.
						</small>
					</div>
					<div class='col-sm-12'>
						<div class='row form-group'>
							<label class='col-sm-3' for='inputIdCard'>Copie de la carte d'identité :</label>
							<input class='col-sm-9 form-control-file inputIdCard' name='idCardInput' type='file'>
							<small class='col-sm-12'>5 Mo Maximum. Format accepté: PDF, JPG, PNG, GIF, BMP</small>
						</div>
						<div class='row'>
							<label class='col-sm-3' for='inputPhoto'>Photo d'identité :</label>
							<input class='col-sm-9 form-control-file inputPhoto' name='photoInput' type='file'>
							<small class='col-sm-12'>5 Mo Maximum. Format accepté: PDF, JPG, PNG, GIF, BMP</small>
						</div>
					</div>-->
				</div>
			</form>
		`;

		let newDom = $(dom);
		newDom.find('.remove-button').click(function(){
			$(this).closest('.adherent').remove();
		});

		$('#adherentsList').append(newDom);

		this.adherentIndex ++;
	},

	addTuteurDom: function () {
		let dom = `
			<form class='tuteur card'>
				<div class='card-header'>
					<span class='tuteur-title'>
						Statut :
						<select class='tuteur-title' name='status'>
							<option value='adherent'>Adhérent</option>
							<option value='father'>Père</option>
							<option value='mother'>Mère</option>
							<option value='tutor'>Tuteur</option>
						</select>
					</span>
					<button class='remove-button btn btn-danger' type='button'><i class='fas fa-trash'></i></button>
				</div>
				<div class='row card-body'>
					<div class='col-sm-6 form-group'>
						<label for='nomInput'>Nom</label>
						<input class='form-control' name='lastname' type='text'>
					</div>
					<div class='col-sm-6 form-group'>
						<label for='prenomInput'>Prénom</label>
						<input class='form-control' name='firstname' type='text'>
					</div>
					<div class='col-sm-6 form-group'>
						<label for='emailInput'>E-mail</label>
						<input class='form-control' name='email' type='email'>
					</div>
					<div class='col-sm-6 form-group'>
						<label for='phoneInput'>Téléphone</label>
						<input class='form-control' name='phone' type='text'>
					</div>
					<div class='col-sm-12'>
						<small class='form-text text-muted'>
							Nous vous enverrons par E-mail les informations relative au club et aux activités de votre ou vos enfants.
							Votre numéro de téléphone nous permettra de vous contacter en cas de problème ou de retard de l'un de nos coachs.
						</small>
					</div>
				</div>
			</form>
		`;

		let newDom = $(dom);
		newDom.find('.remove-button').click(function(){
			$(this).closest('.tuteur').remove();
		});

		$('#tuteursList').append(newDom);

		this.tuteurIndex;
	},

	changeStep: function (state) {
		if (this.isPayed) {
			state = 'validation';
		}
		
		$('#steps .step').removeClass('active');
		$('#adherents').hide();
		$('#tuteurs').hide();
		$('#authorisation').hide();
		$('#payment').hide();
		$('#validation').hide();

		switch (state) {
			case 'adherents':
				$('#adherents').show();
				$('#stepAdherents').addClass('active');
				break;

			case 'tuteurs':
				$('#tuteurs').show();
				$('#stepTuteurs').addClass('active');
				break;
				
			case 'authorisation':
				$('#authorisation').show();
				$('#stepAuthorisation').addClass('active');
				break;

			case 'payment':
				$('#payment').show();
				$('#stepPayment').addClass('active');
				break;

			case 'validation':
				$('#validation').show();
				break;
		}
	},

	validInformation: function () {
		if(!this.hasChanged) {
			InscriptionManager.changeStep('payment');
		}

		// Adherents
		let adherentsData = [];
		$('#adherentsList form').each(function () {
			adherentsData.push(serializeForm($(this)));
		});

		// Tuteurs
		let tuteursData = [];
		$('#tuteursList form').each(function () {
			tuteursData.push(serializeForm($(this)));
		});

		// Authorisation
		let authorisationData = serializeForm($('#authorisationForm'));

		let data = {
			adherents: adherentsData,
			tuteurs: tuteursData,
			authorisation: authorisationData,
			passSport: $("#passSportReduction").is(":checked"),
		};

		$.ajax({
			url: urlApi + 'inscription-set-informations',
			type: 'POST',
			data: data,
			success: function(response) {
				if (response.result) {
					InscriptionManager.amountToPay = response.amountToPay;
					$("#paymentPrice .price-amount").html(response.amountToPay);
					$("#paymentreductions ul").html("");

					response.reductions.forEach(function(reduction){
						$("#paymentreductions ul").append("<li class='reduction'>-" + reduction.value + (reduction.type === 1 ? "%" : "€") + " : " + reduction.sujet + "</li>");
					});

					InscriptionManager.changeStep('payment');
				} else {
					alert(response.message);
				}
			}
		});
	},

	validPayment: function () {
		let selectedMethod = $('#paymentOptions button.active');

		if (selectedMethod === undefined) {
			alert("Veuillez sélectionner une méthode de paiement.");
			return;
		}

		let data = {
			method: selectedMethod.data('target')
		};

		let nbDeadlines = $("#optionCheque input[name='deadlinesInput']:checked").val();
		
		console.log(nbDeadlines);

		if(nbDeadlines !== undefined) {
			data.deadlines = nbDeadlines;
		}

		console.log(data);

		$.ajax({
			url: urlApi + 'inscription-validate-payment',
			type: 'POST',
			data: data,
			success: function(response) {
				if (response.result) {
					$('#validationEmailMessage').html(response.message);
					InscriptionManager.changeStep('validation');
				} else {
					alert(response.message);
				}
			}
		});
	},

	payPalCreateOrder: function(data, actions) {
		console.log("Call create function");
		return actions.order.create({
			purchase_units: [{
				amount: {
					currency_code: 'EUR',
					value: InscriptionManager.amountToPay
				},
				description: 'Cotisation Snake Cheer All Star saison <?= SnakeTools::getCurrentSaison() ?>'
			}]
		});
	},

	payPalOnApporve: function(data, actions) {
		return actions.order.capture().then(function(details) {
			$('#paymentPayPalWaitting').modal();

			$.ajax({
				url: urlApi + 'approve_paypal_order',
				type: 'POST',
				data: {
					orderID: data.orderID
				},
				success: function(response) {
					InscriptionManager.validPaypalPayment(response);
				}
			});
		});
	},

	validPaypalPayment: function (response) {
		if (response.result) {
			$('#paymentOptions').hide();
			$('#selectDetails').hide();
			$('#paymentResult').html('<div class="alert alert-success text-center"><i class="fas fa-check-circle"></i> ' + response.message + '</div>');
			$('#payment .nextButton button').attr('disabled', false);
		} else {
			$('#paymentResult').html('<div class="alert alert-danger text-center"><i class="fas fa-check-circle"></i> ' + response.message + '</div>');
			alert('Error');
		}
	
		$('#paymentPayPalWaitting').modal('hide');
	}
};