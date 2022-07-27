$(document).ready(function(){
	ProfilManager.Init();
});

var ProfilManager = {
	Init: function () {	
		$("#alertSuccess").hide();
		$("#alertError").hide();
	
		$('#formUpdateInfos').submit(function(){
			$.ajax({
				url: api_url + "profil_update_infos",
				type: "POST",
				data: {
					name: $("#nameInput").val()
				},
				success: function(response) {
					ProfilManager.DisplayAlert(response.type, response.message);
					
					if(response.type)
						$("#entete").html($("#nameInput").val());
				}
			});
	
			return false;
		});
		
		$('#collapseUpdatePassword').submit(function(){
			$.ajax({
				url: api_url + "profil_update_password",
				type: "POST",
				data: {
					old_password: $("#passwordOldInput").val(),
					new_password: $("#passwordNewInput").val(),
					confirm_password: $("#passwordConfirmInput").val()
				},
				success: function(response) {
					ProfilManager.DisplayAlert(response.type, response.message);
				}
			});
	
			return false;
		});
	},
	
	DisplayAlert: function(type, message) {
		if(type == "success")
		{
			$("#alertSuccess").html(message);
			$("#alertSuccess").show();
			setTimeout(
				function(){
					$("#alertSuccess").fadeOut();
				},
				3000
			);
		}
		else if(type == "error")
		{
			$("#alertError").html(message);
			$("#alertError").show();
			setTimeout(
				function(){
					$("#alertError").fadeOut();
				},
				3000
			);
		}
	}
}