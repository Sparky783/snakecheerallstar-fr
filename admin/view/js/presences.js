$(document).ready(function(){
	Presences.Init();
});

var Presences = {
	Init: function () {
		$("#selectedSection").change(function(){
			Presences.SelectSection($("#selectedSection").val());
		});
	
		$("#traitement").hide();
		$("#retour").hide();
		this.SelectSection($("#selectedSection").val());
	},
	
	SelectSection: function (id_section) {
		$("#tableAdherents tbody").html("");

		$.ajax({
			url: api_url + "presences_list/" + id_section,
			type: "GET",
			success: function(data) {
				$("#content").html(data.html);
				$("#content .presence-button").each(function(){
					$(this).PresencesRadio();
				});

				$("#validatePresences").click(function(){
					$("#presences").hide();
					$("#traitement").show();
		
					var status = [];
					$("#tableAdherents tbody tr").each(function(){
						status.push({
							id: $(this).data("id"),
							state: $(this).find(".presence-button").attr("value")
						});
					});
		
					$.ajax({
						url: api_url + "validate_presences",
						type: "POST",
						data: {
							section: $("#selectedSection").val(),
							status: status
						},
						success: function(data) {
							$("#traitement").hide();
							$("#retour").show();
						}
					});
				});
			}
		});
	}
};

(function($) {
	$.fn.PresencesRadio = function() {
		var Element = $(this);

		var Radio = {
			Init: function () {
				Element.find("button").click(function(){
					Element.find("button").each(function(){
						$(this).removeClass("btn-snake");
						$(this).removeClass("btn-justify");
						$(this).removeClass("btn-warning");
						$(this).removeClass("btn-danger");
					});

					switch($(this).data("type"))
					{
						case "present":
							$(this).addClass("btn-snake");
							break;

						case "justify":
							$(this).addClass("btn-justify");
							break;

						case "absent":
							$(this).addClass("btn-danger");
							break;

						case "late":
							$(this).addClass("btn-warning");
							break;
					}

					Element.attr("value", $(this).data("type"));
				});
			},
		};

		Radio.Init();
	};
})(jQuery);