$(document).ready(function(){
	AddAdherents.Init();
});

var AddAdherents = {
	templateAdherent: null,
	templateTuteur: null,

	Init: function () {
		this.InitAdherents();
		this.InitTuteurs();

		$("#validButton").click(function(){
			AddAdherents.ValidForm();
		});
	},

	InitAdherents: function () {
		this.templateAdherent = $("#templateAdherent");
		this.templateAdherent.find(".traitementInfo").hide();

		$("#templateAdherent").remove();
		this.templateAdherent.removeAttr("id");
		
		$("#adherents").append();
		
		$("#btnAddAdherent").click(function(){
			AddAdherents.AddAdherent();
		});

		this.AddAdherent();
	},

	InitTuteurs: function () {
		this.templateTuteur = $("#templateTuteur");

		$("#templateTuteur").remove();
		this.templateTuteur.removeAttr("id");
		
		$("#tuteurs").append();
		
		$("#btnAddTuteur").click(function(){
			AddAdherents.AddTuteur();
		});

		this.AddTuteur();
	},

	AddAdherent: function () {
		var adherent = this.templateAdherent.clone();
		adherent.find(".remove-button").click(function(){
			adherent.remove();
		});
		
		$('#adherents').append(adherent);
	},

	AddTuteur: function () {
		var tuteur = this.templateTuteur.clone();
		tuteur.find(".remove-button").click(function(){
			tuteur.remove();
		});
		
		$('#tuteurs').append(tuteur);
	},

	ValidForm: function () {
		var data = {
			adherents: [],
			tuteurs: [],
			payment: {
				mode: $("#addAdherent").find("select[name='paymentInput']").children("option:selected").val(),
				deadlines: $("#addAdherent").find("input[name='deadlinesInput']:checked").val(),
				remise: {
					type: $("#addAdherent").find("select[name='remiseTypeInput']").children("option:selected").val(),
					amount: $("#addAdherent").find("input[name='remiseInput']").val()
				}
			}
		};

		$('#adherents .adherent').each(function(){
			data.adherents.push({
				firstname: $(this).find("input[name='prenomInput']").val(),
				lastname: $(this).find("input[name='nomInput']").val(),
				birthday: $(this).find("input[name='birthdayInput']").val(),
				medicine: $(this).find("input[name='medicineInput']:checked").val(),
				infoMedicine: $(this).find("input[name='traitementInfoInput']").val(),
				tenue: $(this).find("input[name='tenueInput']:checked").val(),
				sportmut: $(this).find("input[name='sportmutInput']").val()
			});
		});

		$('#tuteurs .tuteur').each(function(){
			data.tuteurs.push({
				status: $(this).find("select[name='statusInput']").children("option:selected").val(),
				firstname: $(this).find("input[name='prenomInput']").val(),
				lastname: $(this).find("input[name='nomInput']").val(),
				email: $(this).find("input[name='emailInput']").val(),
				phone: $(this).find("input[name='phoneInput']").val()
			});
		});

		$.ajax({
			url: api_url + "adherents_add",
			type: "POST",
			data: data,
			success: function(response) {
				alert(response.message);
				
				if(response.result)
					location.reload();
			}
		});
	}
};