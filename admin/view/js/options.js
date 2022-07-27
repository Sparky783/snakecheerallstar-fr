$(document).ready(function(){
	Options.Init();
});

var Options = {
	isCurrentSaison: false,
	sections: null,
	selectedSection : null,

	Init: function () {
		// Partie inscription
		$("#applyBlock button").click(function(){
			$.ajax({
				url: api_url + "apply_options",
				type: "POST",
				data: {
					"open_inscription" : $("#cbOpenInscription").is(":checked"),
					"min_date_inscription" : $("#tbMinDateInscription").val(),
					"max_date_inscription" : $("#tbMaxDateInscription").val()
				},
				success: function() {
					alert("Les modifications ont bien été enregistrées.")
				}
			});
		});

		$("#addSectionButton").click(function(){
			Options.selectedSection = null;
			$('#addSectionModal').find("input, textarea, select").val("");
			$('#addSectionModal').modal();
		});
		
		$('#addSectionModal').find("form").submit(function(){
			$.ajax({
				url: api_url + "section_add",
				type: "POST",
				data: {
					name: $('#addSectionModal').find("#nameInput").val(),
					min_age: $('#addSectionModal').find("#minAgeInput").val(),
					price_cotisation: $('#addSectionModal').find("#priceCotisationInput").val(),
					price_uniform: $('#addSectionModal').find("#priceUniformInput").val(),
					max_members: $('#addSectionModal').find("#maxMembersInput").val()
				},
				success: function() {
					Options.RefreshSection();
					$('#addSectionModal').modal('hide');
				}
			});
	
			return false;
		});
	
		$('#editSectionModal').find("form").submit(function(){
			$.ajax({
				url: api_url + "section_edit",
				type: "POST",
				data: {
					id_section: Options.selectedSection.id_section,
					name: $('#editSectionModal').find("#nameInput").val(),
					min_age: $('#editSectionModal').find("#minAgeInput").val(),
					price_cotisation: $('#editSectionModal').find("#priceCotisationInput").val(),
					price_uniform: $('#editSectionModal').find("#priceUniformInput").val(),
					max_members: $('#editSectionModal').find("#maxMembersInput").val()
				},
				success: function() {
					Options.RefreshSection();
					$('#editSectionModal').modal('hide');
				}
			});
	
			return false;
		});

		$('#removeSectionModal').find("form").submit(function(){
			$.ajax({
				url: api_url + "section_remove",
				type: "POST",
				data: {
					id_section: Options.selectedSection.id_section
				},
				success: function() {
					Options.RefreshSection();
					$('#removeSectionModal').modal('hide');
				}
			});
	
			return false;
		});
	
		this.RefreshSection();
	},

	RefreshSection: function () {
		$.ajax({
			url: api_url + "section_list",
			type: "POST",
			success: function(data) {
				Options.isCurrentSaison = data.isCurrentSaison;
				Options.sections = data.sections;
				
				$("#nbSections").html(Options.sections.length + "");
				
				$("#tableSections tbody").html("");

				if(Options.isCurrentSaison)
					$("#addSectionButton").show();
				else
					$("#addSectionButton").hide();
				
				
				for(var i = 0; i < Options.sections.length; i ++)
					Options.AddSection(Options.sections[i]);
			}
		});
	},
	
	AddSection: function(section) {
		var actions = $("<td class='text-right'><div class='btn-group'></div></td>");
		actions.find("div").append("<button class='modify-section btn btn-secondary' data-id='" + section.id_section + "'><i class='fas fa-pen'></i></button> ");
		actions.find("div").append("<button class='remove-section btn btn-danger' data-id='" + section.id_section + "'><i class='fas fa-trash-alt'></i></button>");
		
		actions.find(".modify-section").click(function(){
			Options.selectedSection = section;
			
			$('#editSectionModal').find("#nameInput").val(Options.selectedSection.name);
			$('#editSectionModal').find("#minAgeInput").val(Options.selectedSection.min_age);
			$('#editSectionModal').find("#priceCotisationInput").val(Options.selectedSection.price_cotisation);
			$('#editSectionModal').find("#priceUniformInput").val(Options.selectedSection.price_uniform);
			$('#editSectionModal').find("#maxMembersInput").val(Options.selectedSection.max_members);
			
			$('#editSectionModal').modal();
		});
		
		actions.find(".remove-section").click(function(){
			Options.selectedSection = section;
			$("#removeSectionModal").find("#nameSection").html(Options.selectedSection.name);
			$('#removeSectionModal').modal();
		});
	
		var row = $("<tr></tr>");
		row.append("<td>" + section.name + "</td>");
		row.append("<td>" + section.min_age + "</td>");
		row.append("<td>" + section.price_cotisation + "</td>");
		row.append("<td>" + section.price_uniform + "</td>");
		row.append("<td>" + section.max_members + "</td>");

		if(Options.isCurrentSaison)
			row.append(actions);
		else
			row.append("<td></td>");
		
		$("#tableSections tbody").append(row);
	},
};