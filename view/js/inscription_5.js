(function($) {
	$.fn.InscriptionManager = function(options = {}) {
		var defaults = {};
		var Options = $.extend(defaults, options);
		var Element = $(this);

		var StepConfirmation = {
			Init: function () {
				$("#validButton").click(function(){
					var result = false;
					$.ajax({
						url: Options.UrlApi + "close_inscription",
						type: "POST",
						async: false,
						success: function() {
							result = true;
						}
					});

					return result;
				});
			}
		};
		
		StepConfirmation.Init();
	};
})(jQuery);