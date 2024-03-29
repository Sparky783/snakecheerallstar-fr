$(document).ready(function(){
	AdherentInfo.Init();
});

let AdherentInfo = {
	selectedId: null,
	addTuteurModal: null,
	sendBillModal: null,
	sendRecapModal: null,

	Init: function () {
		this.addTuteurModal = new bootstrap.Modal('#addTuteurModal');
		this.sendBillModal = new bootstrap.Modal('#sendBillModal');
		this.sendRecapModal = new bootstrap.Modal('#sendRecapModal');

		$("#addTuteurButton").click(function(){
			$("#addTuteurForm input").val('');
		});
		
		$("#addTuteurForm").submit(function(){
			$.ajax({
				url: api_url + "adherent_info_add_tuteur",
				type: "POST",
				data: {
					status: $("#statusInput").val(),
					firstname: $("#prenomInput").val(),
					lastname: $("#nomInput").val(),
					email: $("#emailInput").val(),
					phone: $("#phoneInput").val()
				},
				success: function(response) {
					alert(response.message);
					location.reload();
				}
			});
			return false;
		});

		$("#sendBillForm").submit(function(){
			$.ajax({
				url: api_url + "adherent_info_send_bill",
				type: "POST",
				data: {
					id_tuteur: $("#destBillInput").val()
				},
				success: function(response) {
					alert(response.message);
					AdherentInfo.sendBillModal.hide();
				}
			});
			return false;
		});
		
		$("#sendRecapForm").submit(function(){
			$.ajax({
				url: api_url + "adherent_info_send_recap",
				type: "POST",
				data: {
					id_tuteur: $("#destRecapInput").val()
				},
				success: function(response) {
					alert(response.message);
					AdherentInfo.sendRecapModal.hide();
				}
			});
			return false;
		});
	},
}