$(document).ready(function(){
	Home.Init();
});

var Home = {
	Init: function () {
		$("#selectSaison").change(function(){
			Home.SelectSection($("#selectSaison").val());
		});
	
		this.SelectSection($("#selectSaison").val());
	},
	
	SelectSection: function (saison) {
		$.ajax({
			url: api_url + "change_saison",
			type: "POST",
			data: {
				"saison" : saison
			},
			success: function() {
				$("#headerSaison").html(saison);
			}
		});
	}
};