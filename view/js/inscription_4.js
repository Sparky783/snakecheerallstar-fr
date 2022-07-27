(function($) {
	$.fn.InscriptionManager = function(options = {}) {
		var defaults = {};
		var Options = $.extend(defaults, options);
		var Element = $(this);
		var Price = parseInt($("#infoPrice .price-amount").html()); // Récupération du prix

		var StepAuthorization = {
			Init: function () {
				if(!Options.EnableBtn)
					$("#validButton").attr("disabled", true);

				// Action à mener suite au clique du bouton de validation.
				$("#validButton").click(function(){
					StepAuthorization.ValidForm();
				});

				// Cache les détails.
				$("#selectDetails > div").hide();

				// Action lorsque l'utilisateur sélectionne son moyen de paiement.
				$("#selectOption button").click(function(){
					$("#selectOption button").removeClass('active');
					$(this).addClass('active');

					$("#selectDetails > div").hide();
					$("#" + $(this).data("target")).show();

					if($(this).data("target") == "optionEspece")
						$("#validButton").attr("disabled", false);
					else if($(this).data("target") == "optionCheque")
					{
						var test = true;

						$("#optionCheque input").each(function(){
							if($(this).is(':checked'))
								test = false;
						});

						$("#validButton").attr("disabled", test);
					}
					else if($(this).data("target") == "optionEnLigne")
						$("#validButton").attr("disabled", true);
				});

				// Lorsque l'utilisateur choisi une option en plusieur fois, on active le bouton.
				$("#optionCheque input").each(function(){
					$(this).change(function(){
						$("#validButton").attr("disabled", false);
					});
				});

				// Gestion du bouton PassSport
				$("#passSportReduction").change(function(){
					if($("#passSportReduction").is(":checked"))
						$("#infoPrice .price-amount").html(Price - 50);
					else
						$("#infoPrice .price-amount").html(Price);
				});
			},
			
			ValidForm: function () {
				var met = $("#selectOption button.active").data("target");

				if(Options.EnableBtn)
					met = "optionEnLigne";

				$.ajax({
					url: Options.UrlApi + "inscription_validate_payment",
					type: "POST",
					data: {
						method: met,
						passSport: $("#passSportReduction").is(":checked"),
						deadlines: $("#optionCheque input[name='deadlinesInput']:checked").val()
					},
					success: function(response) {
						if(response.result)
							location.reload();
						else
							alert(response.message);
					}
				});
			}
		};
		
		StepAuthorization.Init();
	};
})(jQuery);

var InscriptionPaymentResponse = function (response) {
	if(response.result)
	{
		$("#paymentOptions").hide();
		$("#selectDetails").hide();
		$("#payment").append("<div id='resultPayment' class='alert alert-success text-center'><i class='fas fa-check-circle'></i> " + response.message + "</div>");
		$("#validButton").attr("disabled", false);
	}
	else
		alert('Error');

	$('#validModal').modal('hide');
};