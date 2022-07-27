$(document).ready(function(){
	CandidatManager.Init();
});

var CandidatManager = {
	candidats: null,
	selectedCandidat : null,

	Init: function () {		
		$("#addCandidatButton").click(function(){
			CandidatManager.selectedCandidat = null;
			$('#addCandidatModal').find("input, textarea, select").val("");
			$('#addCandidatModal').modal();
		});
		
		$('#addCandidatModal').find("form").submit(function(){
			$.ajax({
				url: api_url + "ag_candidat_add",
				type: "POST",
				data: {
					lastname: $('#addCandidatModal').find("#lastnameInput").val(),
					firstname: $('#addCandidatModal').find("#firstnameInput").val()
				},
				success: function() {
					CandidatManager.Refresh();
					$('#addCandidatModal').modal('hide');
				}
			});
	
			return false;
		});
	
		$('#editCandidatModal').find("form").submit(function(){
			$.ajax({
				url: api_url + "ag_candidat_edit",
				type: "POST",
				data: {
					id_candidat: CandidatManager.selectedCandidat.id_candidat,
					lastname: $('#editCandidatModal').find("#lastnameInput").val(),
					firstname: $('#editCandidatModal').find("#firstnameInput").val()
				},
				success: function() {
					CandidatManager.Refresh();
					$('#editCandidatModal').modal('hide');
				}
			});
	
			return false;
		});
	
		$('#removeCandidatModal').find("form").submit(function(){
			$.ajax({
				url: api_url + "ag_candidat_remove",
				type: "POST",
				data: {
					id_candidat: CandidatManager.selectedCandidat.id_candidat
				},
				success: function() {
					CandidatManager.Refresh();
					$('#removeCandidatModal').modal('hide');
				}
			});
	
			return false;
		});
	
		this.Refresh();
		this.GetResults();
	},
	
	Refresh: function () {
		$.ajax({
			url: api_url + "ag_candidats_list",
			type: "POST",
			success: function(data) {
				CandidatManager.candidats = data.candidats;
				
				$("#nbCandidats").html(CandidatManager.candidats.length + "");
				
				$("#tableCandidats tbody").html("");
				
				for(var i = 0; i < CandidatManager.candidats.length; i ++)
					CandidatManager.AddCandidat(CandidatManager.candidats[i]);
			}
		});
	},
	
	AddCandidat: function(candidat) {
		var actions = $("<td class='text-right'><div class='btn-group'></div></td>");
		actions.find("div").append("<button class='modify-candidat btn btn-secondary' data-id='" + candidat.id_candidat + "'><i class='fas fa-pen'></i></button> ");
		actions.find("div").append("<button class='remove-candidat btn btn-danger' data-id='" + candidat.id_candidat + "'><i class='fas fa-trash-alt'></i></button>");
		
		actions.find(".modify-candidat").click(function(){
			CandidatManager.selectedCandidat = candidat;
			
			$('#editCandidatModal').find("#lastnameInput").val(CandidatManager.selectedCandidat.lastname);
			$('#editCandidatModal').find("#firstnameInput").val(CandidatManager.selectedCandidat.firstname);
			
			$('#editCandidatModal').modal();
		});
		
		actions.find(".remove-candidat").click(function(){
			CandidatManager.selectedCandidat = candidat;
			$("#removeCandidatModal").find("#nameCandidat").html(CandidatManager.selectedCandidat.lastname + " " + CandidatManager.selectedCandidat.firstname);
			$('#removeCandidatModal').modal();
		});
	
		var row = $("<tr></tr>");
		row.append("<td>" + candidat.lastname + "</td>");
		row.append("<td>" + candidat.firstname + "</td>");
		row.append(actions);
		
		$("#tableCandidats tbody").append(row);
	},
	
	GetCandidat: function(id_candidat) {
		for(var i = 0; i < CandidatManager.candidats.length; i ++)
		{
			if(CandidatManager.candidats[i].id_candidat == id_candidat)
				return CandidatManager.candidats[i];
		}
	},

	GetResults: function() {
		$.ajax({
			url: api_url + "ag_get_resultat",
			type: "POST",
			success: function(data) {
				var html = "<tr><td>Nombre de votes attendus</td><td>" + data.nbWaittingVote + "</td></tr>";
				html += "<tr><td>Nombre de votes enregistrés</td><td>" + data.nbVote + "</td></tr>";
				html += "<tr><td>Nombre de votes pour le rapport moral</td><td>" + data.nbVoteRapportMoral + "</td></tr>";
				html += "<tr><td>Nombre de votes pour le rapport financier</td><td>" + data.nbVoteRapportFinancier + "</td></tr>";
				html += "<tr><td>Nombre de votes pour le nouveau réglement</td><td>" + data.nbVoteCotisations + "</td></tr>";

				data.candidats.forEach(element => {
					html += "<tr><td>" + element.name + "</td><td>" + element.nbVotes + "</td></tr>";
				});

				$("#resultats tbody").html(html);
			}
		});
	}
}