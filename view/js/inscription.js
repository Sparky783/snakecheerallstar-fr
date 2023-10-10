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
	messageBoxModal: null,
	paymenbtWaittingModal: null,

	init: function () {
		$('#tuteurs').hide();
		$('#authorisation').hide();
		$('#payment').hide();
		$('#validation').hide();

		this.messageBoxModal = new bootstrap.Modal("#messageBoxModal");
		this.paymenbtWaittingModal = new bootstrap.Modal("#paymentPayPalWaitting");

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

		$('#adherents .next-button').click(function(){
			InscriptionManager.changeStep('tuteurs');
		});
		
		InscriptionManager.addAdherentDom();
	},

	initTuteurs: function () {
		$('.tuteursAddBtn').click(function(){
			InscriptionManager.addTuteurDom();
		});

		$('#tuteurs .next-button').click(function(){
			InscriptionManager.changeStep('authorisation');
		});
		
		InscriptionManager.addTuteurDom();
	},

	initAuthorisation: function () {
		$('#authorisation .next-button').click(function(){
			InscriptionManager.validInformation();
		});
	},

	initPayment: function () {
		$('#paymentDetails .paymentOption').hide();

		$('#paymentOptions button').click(function(){
			let target = $(this).data('target');

			if(target === 'optionEnLigne') {
				$('#payment .next-button').attr('disabled', true);
			} else {
				$('#payment .next-button').attr('disabled', false);
			}

			$('#paymentDetails .paymentOption').hide();
			$('#' + target).show();
			$('#paymentOptions button').removeClass('active');
			$(this).addClass('active');
		});

		$('#paymentResult').hide();

		$('#payment .next-button').click(function(){
			InscriptionManager.validPayment();
		});
		$('#payment .next-button').attr('disabled', true);
	},

	addAdherentDom: function () {
		let dom = `
			<form class='adherent card'>
				<div class='card-header'>
					<span class='adherent-title'>Adhérent</span>
					<button class='remove-button btn btn-danger' type='button'><i class='fas fa-trash'></i></button>
				</div>
				<div class='row card-body'>
					<div class='col-sm-6 mb-3'>
						<label for='nomInput'>Nom</label>
						<input class='form-control' name='lastname' type='text'>
					</div>
					<div class='col-sm-6 mb-3'>
						<label for='prenomInput'>Prénom</label>
						<input class='form-control' name='firstname' type='text'>
					</div>
					<div class='col-sm-6 mb-3'>
						<label for='birthdayInput'>Date de naissance</label>
						<input class='form-control' name='birthday' type='date'>
					</div>
					<div class='col-sm-6 mb-3'>
						<label for='medicineInfoInput'>Traitement médical</label>
						<input class='form-control' name='medicineInfo' type='text' placeolder="si nécessaire">
					</div>
					<div class='col-sm-12 mb-3'>
						<div class='form-check form-switch'>
							<input id='passSportInput_` + this.adherentIndex + `' class='form-check-input' type='checkbox' name='passSport' role="switch" />
							<label class='form-check-label' for='passSportInput_` + this.adherentIndex + `'>Pass Sport <small>(Sous présentation d'un justificatif obligatoire)</small></label>
							<input id='passSportCodeInput_` + this.adherentIndex + `' class='form-control passSportCodeInput' name='passSportCode' type='text' placeholder="Entrer le code">
						</div>
						<small class='form-text text-muted'>
						</small>
					</div>
					<div class='col-sm-6 mb-3'>
						<label for='socialSecurityNumberInput'>Numéro de sécurité sociale</label>
						<input class='form-control' name='socialSecurityNumber' type='text' placeholder='Ex: 1 85 05 78 006 084 36'>
					</div>
					<div class='col-sm-6 mb-3'>
						<label for='doctorNameInput'>Nom du édecine traitant</label>
						<input class='form-control' name='doctorName' type='text'>
					</div>
					<div class='col-sm-6 mb-3'>
						<label for='nameEmergencyContactInput'>Nom de la personne à contacter en cas d'urgence</label>
						<input class='form-control' name='nameEmergencyContact' type='text'>
					</div>
					<div class='col-sm-6 mb-3'>
						<label for='phoneEmergencyContactInput'>Numéro de la personne à contacter en cas d'urgence</label>
						<input class='form-control' name='phoneEmergencyContact' type='text' placeholder='Ex: 0123456789'>
					</div>

					<!--<div class='col-sm-12 mb-3'>
						<div class='form-check form-check-inline'>
							<label class='form-check-label'>Adhésion Sportmut (optionnel)</label>
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

		let currentIndex = this.adherentIndex;
		let passSportSwitch = newDom.find('#passSportInput_' + this.adherentIndex);

		passSportSwitch.change(function() {
			let passSportCode = $('#passSportCodeInput_' + currentIndex);

			if($(this).is(":checked")) {
				passSportCode.addClass('active');
			} else {
				passSportCode.removeClass('active');
			}
		});

		$('#adherentsList').append(newDom);

		this.adherentIndex ++;
	},

	addTuteurDom: function () {
		let dom = `
			<form class='tuteur card'>
				<div class='card-header'>
					<span class='tuteur-header'>
						Statut :
						<select class='form-select' name='status'>
							<option value='adherent'>Adhérent</option>
							<option value='father'>Père</option>
							<option value='mother'>Mère</option>
							<option value='tutor'>Tuteur</option>
						</select>
					</span>
					<button class='remove-button btn btn-danger' type='button'><i class='fas fa-trash'></i></button>
				</div>
				<div class='row card-body'>
					<div class='col-sm-6  mb-3'>
						<label for='nomInput'>Nom</label>
						<input class='form-control' name='lastname' type='text'>
					</div>
					<div class='col-sm-6  mb-3'>
						<label for='prenomInput'>Prénom</label>
						<input class='form-control' name='firstname' type='text'>
					</div>
					<div class='col-sm-6  mb-3'>
						<label for='emailInput'>E-mail</label>
						<input class='form-control' name='email' type='email' placeholder='Ex: monemail@gmail.fr'>
					</div>
					<div class='col-sm-6  mb-3'>
						<label for='phoneInput'>Téléphone</label>
						<input class='form-control' name='phone' type='text' placeholder='Ex: 0123456789'>
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
			return;
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
				$('#stepValidation').addClass('active');
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
					InscriptionManager.messageBox(response.message);
				}
			}
		});
	},

	validPayment: function () {
		let selectedMethod = $('#paymentOptions button.active');

		if (selectedMethod === undefined) {
			this.messageBox("Veuillez sélectionner une méthode de paiement.");
			return;
		}

		let data = {
			method: selectedMethod.data('target')
		};

		let nbDeadlines = $("#optionCheque input[name='deadlinesInput']:checked").val();

		if(nbDeadlines !== undefined) {
			data.deadlines = nbDeadlines;
		}

		$.ajax({
			url: urlApi + 'inscription-validate-payment',
			type: 'POST',
			data: data,
			success: function(response) {
				if (response.result) {
					$('#validationEmailMessage').html(response.message);
					InscriptionManager.changeStep('validation');
					InscriptionManager.isPayed = true;
				} else {
					InscriptionManager.messageBox(response.message);
				}
			}
		});
	},

	payPalCreateOrder: function(data, actions) {
		return actions.order.create({
			purchase_units: [{
				amount: {
					currency_code: 'EUR',
					value: InscriptionManager.amountToPay
				},
				description: 'Cotisation Snake Cheer All Star saison ' + saison
			}]
		});
	},

	payPalOnApporve: function(data, actions) {
		return actions.order.capture().then(function(details) {
			InscriptionManager.paymenbtWaittingModal.show();

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
			$('#paypalButtonContainer').hide();
			$('#paymentResult').html('<div class="alert alert-success text-center"><i class="fas fa-check-circle"></i> ' + response.message + '</div>');
			$('#payment .next-button').attr('disabled', false);
		} else {
			$('#paymentResult').html('<div class="alert alert-danger text-center"><i class="fas fa-exclamation-triangle"></i> ' + response.message + '</div>');
		}

		$('#paymentResult').show();
		this.paymenbtWaittingModal.hide();
	},

	messageBox: function (message) {
		$('#messageBox').html(message);
		this.messageBoxModal.show();
	}
};