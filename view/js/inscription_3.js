(function($) {
	$.fn.InscriptionManager = function(options = {}) {
		var defaults = {};
		var Options = $.extend(defaults, options);
		var Element = $(this);

		var StepAuthorization = {
			Init: function () {
				$("#validButton").click(function(){
					StepAuthorization.ValidForm();
				});
			},
			
			ValidForm: function () {
				$.ajax({
					url: Options.UrlApi + "inscription_validate_authorization",
					type: "POST",
					data: {
						authorization: $(".authorization-validation input[name='validInput']").is(':checked'),
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