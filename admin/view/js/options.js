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
	editHorairesModal: null,

	Init: function () {
		this.addSectionModal = new bootstrap.Modal('#addSectionModal');
		this.editSectionModal = new bootstrap.Modal('#editSectionModal');
		this.removeSectionModal = new bootstrap.Modal('#removeSectionModal');
		this.editHorairesModal = new bootstrap.Modal('#editHorairesModal');

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
			$('#editSectionModal').find("#maxYearInput").val(Options.selectedSection.max_year);
			$('#editSectionModal').find("#cotisationPriceInput").val(Options.selectedSection.cotisation_price);
			$('#editSectionModal').find("#rentUniformPriceInput").val(Options.selectedSection.rent_uniform_price);
			$('#editSectionModal').find("#cleanUniformPriceInput").val(Options.selectedSection.clean_uniform_price);
			$('#editSectionModal').find("#buyUniformPriceInput").val(Options.selectedSection.buy_uniform_price);
			$('#editSectionModal').find("#depositUniformPriceInput").val(Options.selectedSection.deposit_uniform_price);
			$('#editSectionModal').find("#maxMembersInput").val(Options.selectedSection.nb_max_members);
			
			Options.editSectionModal.show();
		});
		
		actions.find(".remove-section").click(function(){
			Options.selectedSection = section;
			$("#removeSectionModal").find("#sectionName").html(Options.selectedSection.name);
			Options.removeSectionModal.show();
		});
	
		var row = $("<tr></tr>");
		row.append("<td>" + section.name + "</td>");
		row.append("<td>" + section.max_year + "</td>");
		row.append("<td>" + section.cotisation_price + " €</td>");
		row.append("<td>" + section.rent_uniform_price + " €</td>");
		row.append("<td>" + section.clean_uniform_price + " €</td>");
		row.append("<td>" + section.buy_uniform_price + " €</td>");
		row.append("<td>" + section.deposit_uniform_price + " €</td>");
		row.append("<td>" + section.nb_max_members + "</td>");

		if (Options.isCurrentSaison) {
			row.append(actions);
		} else {
			row.append("<td></td>");
		}
		
		$("#sectionList tbody").append(row);
	},
};