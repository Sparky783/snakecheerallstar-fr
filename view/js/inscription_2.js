(function($) {
	$.fn.InscriptionManager = function(options = {}) {
		var defaults = {};
		var Options = $.extend(defaults, options);
		var Element = $(this);

		var StepTuteurs = {
			templateTuteur: null,
	
			Init: function () {
				this.templateTuteur = $("#templateTuteur");
				$("#templateTuteur").remove();
				this.templateTuteur.removeAttr("id");
				
				$("#tuteurs").append();
				
				$("#addTuteurButton").click(function(){
					StepTuteurs.AddTuteur();
				});
				this.AddTuteur();
				
				$("#validButton").click(function(){
					StepTuteurs.ValidForm();
				});
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
					tuteurs: []
				};
				
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
					url: Options.UrlApi + "inscription_validate_tuteurs",
					type: "POST",
					data: data,
					success: function(response) {
					console.log(response);
						if(response.result)
							location.reload();
						else
							alert(response.message);
					}
				});
			}
		};
		
		StepTuteurs.Init();
	};
})(jQuery);