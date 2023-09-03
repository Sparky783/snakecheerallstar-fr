$(document).ready(function(){
	Options.Init();
});

function serializeForm(form) {
	let inputs = form.find(':input');
	let values = {};

	inputs.each(function(){
		switch ($(this).attr('type')) {
			case 'checkbox':
				values[this.name] = $(this).prop('checked');
				break;

			default:
				values[this.name] = $(this).val();
				break;
		}
	});

	return values;
}

let Options = {
	isCurrentSaison: false,
	sections: null,
	selectedSection: null,
	addSectionModal: null,
	editSectionModal: null,
	removeSectionModal: null,

	Init: function () {
		this.addSectionModal = new bootstrap.Modal('#addSectionModal');
		this.editSectionModal = new bootstrap.Modal('#editSectionModal');
		this.removeSectionModal = new bootstrap.Modal('#removeSectionModal');

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
			Options.addSectionModal.show();
		});
		
		$('#addSectionModal').find("form").submit(function(){
			$.ajax({
				url: api_url + "section_add",
				type: "POST",
				data: serializeForm($("#addSectionModal form")),
				success: function() {
					Options.RefreshSection();
					Options.addSectionModal.hide();
				}
			});
	
			return false;
		});
	
		$('#editSectionModal').find("form").submit(function(){
			let dataForm = serializeForm($("#editSectionModal form"));
			dataForm.idSection = Options.selectedSection.idSection;

			$.ajax({
				url: api_url + "section_edit",
				type: "POST",
				data: dataForm,
				success: function() {
					Options.RefreshSection();
					Options.editSectionModal.hide();
				}
			});
	
			return false;
		});

		$('#removeSectionModal').find("form").submit(function(){
			$.ajax({
				url: api_url + "section_remove",
				type: "POST",
				data: {
					idSection: Options.selectedSection.idSection
				},
				success: function() {
					Options.RefreshSection();
					Options.removeSectionModal.hide();
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
				
				$("#sectionList tbody").html("");

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
		actions.find("div").append("<button class='modify-section btn btn-secondary' data-id='" + section.idSection + "'><i class='fas fa-pen'></i></button> ");
		actions.find("div").append("<button class='remove-section btn btn-danger' data-id='" + section.idSection + "'><i class='fas fa-trash-alt'></i></button>");
		
		actions.find(".modify-section").click(function(){
			Options.selectedSection = section;
			
			$('#editSectionModal').find("#nameInput").val(Options.selectedSection.name);
			$('#editSectionModal').find("#maxYearInput").val(Options.selectedSection.maxYear);
			$('#editSectionModal').find("#cotisationPriceInput").val(Options.selectedSection.cotisationPrice);
			$('#editSectionModal').find("#rentUniformPriceInput").val(Options.selectedSection.rentUniformPrice);
			$('#editSectionModal').find("#cleanUniformPriceInput").val(Options.selectedSection.cleanUniformPrice);
			$('#editSectionModal').find("#buyUniformPriceInput").val(Options.selectedSection.buyUniformPrice);
			$('#editSectionModal').find("#depositUniformPriceInput").val(Options.selectedSection.depositUniformPrice);
			$('#editSectionModal').find("#maxMembersInput").val(Options.selectedSection.maxMembers);
			
			Options.editSectionModal.show();
		});
		
		actions.find(".remove-section").click(function(){
			Options.selectedSection = section;
			$("#removeSectionModal").find("#sectionName").html(Options.selectedSection.name);
			Options.removeSectionModal.show();
		});
	
		var row = $("<tr></tr>");
		row.append("<td>" + section.name + "</td>");
		row.append("<td>" + section.maxYear + "</td>");
		row.append("<td>" + section.cotisationPrice + " €</td>");
		row.append("<td>" + section.rentUniformPrice + " €</td>");
		row.append("<td>" + section.cleanUniformPrice + " €</td>");
		row.append("<td>" + section.buyUniformPrice + " €</td>");
		row.append("<td>" + section.depositUniformPrice + " €</td>");
		row.append("<td>" + section.maxMembers + "</td>");

		if (Options.isCurrentSaison) {
			row.append(actions);
		} else {
			row.append("<td></td>");
		}
		
		$("#sectionList tbody").append(row);
	},
};