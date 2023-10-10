$(document).ready(function(){
	Adherents.Init();
});

let Adherents = {
	selectedId: null,
	validateModal: null,
	removeModal: null,
	changeSectionModal: null,
	exportModal: null,
	changeSectionAction: '',

	Init: function () {
		this.validateModal = new bootstrap.Modal('#validateModal'),
		this.removeModal = new bootstrap.Modal('#removeModal'),
		this.changeSectionModal = new bootstrap.Modal('#changeSectionModal');
		this.exportModal = new bootstrap.Modal('#exportModal'),

		this.InitAjax();

		$("#selectedSection").change(function(){
			Adherents.SelectSection();
		});
		
		this.SelectSection();
		this.InitExportList();
	},

	InitAjax: function () {
		$('#validateModal').find("form").submit(function(){
			let data = {}

			$(this).find(".form-check-input").each(function(){
				data[$(this).attr("name")] = $(this).is(":checked");
			});
			
			data.id_adherent = Adherents.selectedId;
			
			$.ajax({
				url: api_url + "adherent_validate_update",
				type: "POST",
				data: data,
				success: function() {
					Adherents.SelectSection();
					Adherents.validateModal.hide();
				}
			});
	
			return false;
		});

		$('#changeSectionModal').find("form").submit(function() {
			let sendEmail = $(this).find("#customSwitchChangeSectionEmail").is(":checked");
			console.log(sendEmail);

			if (Adherents.changeSectionAction == 'surclassement') {
				Adherents.SurclasserAdherentAction(Adherents.selectedId, sendEmail);
			} else if(Adherents.changeSectionAction == 'sousclassement') {
				Adherents.SousclasserAdherentAction(Adherents.selectedId, sendEmail);
			}

			return false;
		});
		
		$('#removeModal').find("form").submit(function(){
			let id_adherent = Adherents.selectedId;
			$.ajax({
				url: api_url + "remove_adherent",
				type: "POST",
				data: {
					id_adherent: id_adherent
				},
				success: function() {
					Adherents.SelectSection();
					Adherents.removeModal.hide();
				}
			});
	
			return false;
		});
	},
	
	SelectSection: function () {
		$.ajax({
			url: api_url + "adherent_list",
			type: "POST",
			data: {
				id_section: $("#selectedSection").val()
			},
			success: function(data) {
				$("#tableAdherents tbody").html("");
				$("#nbAdherents").html(data.list.length + "");

				data.list.forEach(adh => {
					Adherents.AddAdherentRow(adh);
				});
			}
		});
	},

	AddAdherentRow: function (data_adherent)
	{
		let dom = $("<tr class='" + data_adherent.status + "' data-id-adherent='" + data_adherent.id + "'></tr>");
		dom.append("<td>" + data_adherent.lastname + "</td>");
		dom.append("<td>" + data_adherent.firstname + "</td>");
		
		let actions = $("<td class='text-end'><div class='dropdown'><a class='btn btn-secondary dropdown-toggle' href='#' role='button' data-bs-toggle='dropdown' aria-expanded='false'>Action</a><div class='dropdown-menu'></div></div></td>");
		
		data_adherent.actions.forEach(action => {
			let button = null;

			switch(action) {
				case "view":
					button = $("<a class='view-adherent dropdown-item' href=" + data_adherent.link + " title='Voir le profil de " + data_adherent.firstname + "'><i class='fas fa-eye'></i> Voir la fiche</a>");
					break;

				case "validate":
					button = $("<button class='modify-adherent dropdown-item'><i class='fas fa-check'></i> Valider l'inscription</button>")
					button.click(function(){
						Adherents.ModifyAdherentAction(data_adherent.id);
					});
					break;

				case "surclasser":
					button = $("<button class='remove-adherent dropdown-item'><i class='fas fa-arrow-up'></i> Sur-classer</button>");
					button.click(function(){
						$('#changeSectionModal .modal-title').html('Surclassement');
						$('#changeSectionModal .message').html("Voulez-vous vraiment surclasser " + data_adherent.firstname + " " + data_adherent.lastname + "?");
						$('#customSwitchChangeSectionEmail').prop("checked", false);
						Adherents.changeSectionAction = 'surclassement';
						Adherents.selectedId = data_adherent.id;
						Adherents.changeSectionModal.show();
					});
					break;

				case "sousclasser":
					button = $("<button class='remove-adherent dropdown-item'><i class='fas fa-arrow-down'></i> Sous-classer</button>");
					button.click(function(){
						$('#changeSectionModal .modal-title').html('Sousclassement');
						$('#changeSectionModal .message').html("Voulez-vous vraiment sousclasser " + data_adherent.firstname + " " + data_adherent.lastname + "?");
						$('#customSwitchChangeSectionEmail').prop("checked", false);
						Adherents.changeSectionAction = 'sousclassement';
						Adherents.selectedId = data_adherent.id;
						Adherents.changeSectionModal.show();
					});
					break;

				case "remove":
					button = $("<button class='remove-adherent dropdown-item'><i class='fas fa-trash-alt'></i> Supprimer</button>");
					button.click(function(){
						Adherents.RemoveAdherentAction(data_adherent.id, data_adherent.firstname + " " + data_adherent.lastname);
					});
					break;
			}

			actions.find(".dropdown-menu").append(button);
		});
			
		dom.append(actions);

		$("#tableAdherents tbody").append(dom);
	},

	ModifyAdherentAction: function (id_adherent) {
		$.ajax({
			url: api_url + "adherent_validate_form/" + id_adherent,
			type: "GET",
			success: function(response) {
				$('#validateModal').find("#editModalTitle").html("Validation " + response.name);
				$('#validateModal').find(".modal-body").html(response.content);
				Adherents.validateModal.show();
			}
		});
	},

	SurclasserAdherentAction: function (id_adherent, sendEmail = false) {
		$.ajax({
			url: api_url + "adherent_surclassement",
			type: "POST",
			data: {
				id: id_adherent,
				sendEmail: sendEmail
			},
			success: function(response) {
				Adherents.changeSectionModal.hide();
				alert(response);
				Adherents.SelectSection();
			}
		});
	},

	SousclasserAdherentAction: function (id_adherent, sendEmail = false) {
		this.selectedId = id_adherent;

		$.ajax({
			url: api_url + "adherent_sousclassement",
			type: "POST",
			data: {
				id: id_adherent,
				sendEmail: sendEmail
			},
			success: function(response) {
				Adherents.changeSectionModal.hide();
				alert(response);
				Adherents.SelectSection();
			}
		});
	},

	RemoveAdherentAction: function (id_adherent, name) {
		this.selectedId = id_adherent;
		
		$("#removeModal").find("#nameAdherent").html(name);
		Adherents.removeModal.show();
	},

	InitExportList: function () {
		$("#exportForm").submit(function(){
			$.ajax({
				url: api_url + "adherent_export_list",
				type: "POST",
				data: {
					id_section: $("#exportSection").val(),
					delimiter: $("#exportDelimiter").val()
				},
				success: function(response) {
					let filename = "Export_List_Adherents.csv";
					let blob;
					
					if (typeof File === 'function') {
						try {
							blob = new File([response], filename, { type: "text/csv;charset=ansi", encoding: "ansi" });
						} catch (e) { /* Edge */ }
					}

					if (typeof blob === 'undefined') {
						blob = new Blob([response], { type: "text/csv;charset=ansi", encoding: "ansi" });
					}
					
					if (typeof window.navigator.msSaveBlob !== 'undefined') {
						// IE workaround for "HTML7007: One or more blob URLs were revoked by closing the blob for which they were created. These URLs will no longer resolve as the data backing the URL has been freed."
						window.navigator.msSaveBlob(blob, filename);
					} else {
						let URL = window.URL || window.webkitURL;
						let downloadUrl = URL.createObjectURL(blob);

						if (filename) {
							// use HTML5 a[download] attribute to specify filename
							let a = document.createElement("a");
							// safari doesn't support this yet
							if (typeof a.download === 'undefined') {
								window.location = downloadUrl;
							} else {
								a.href = downloadUrl;
								a.download = filename;
								document.body.appendChild(a);
								a.click();
							}
						} else {
							window.location = downloadUrl;
						}

						setTimeout(function () { URL.revokeObjectURL(downloadUrl); }, 100); // cleanup
					}

					Adherents.exportModal.show();
				}
			});

			return false;
		});
	}
}