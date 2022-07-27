$(document).ready(function(){
	Adherents.Init();
});

var Adherents = {
	selectedId: null,

	Init: function () {
		this.InitAjax();

		$("#selectedSection").change(function(){
			Adherents.SelectSection($("#selectedSection").val());
		});
		
		this.SelectSection($("#selectedSection").val());
		this.InitExportList();
	},

	InitAjax: function () {
		$('#validateModal').find("form").submit(function(){
			var data = {}

			$(this).find(".custom-control-input").each(function(){
				data[$(this).attr("name")] = $(this).is(":checked");
			});
			
			data.id_adherent = Adherents.selectedId;
			
			$.ajax({
				url: api_url + "adherent_validate_update",
				type: "POST",
				data: data,
				success: function() {
					Adherents.SelectSection($("#selectedSection").val());
					$('#validateModal').modal('hide');
				}
			});
	
			return false;
		});
		
		$('#removeModal').find("form").submit(function(){
			var id_adherent = Adherents.selectedId;
			$.ajax({
				url: api_url + "remove_adherent",
				type: "POST",
				data: {
					id_adherent: id_adherent
				},
				success: function() {
					Adherents.SelectSection($("#selectedSection").val());
					$('#removeModal').modal('hide');
				}
			});
	
			return false;
		});
	},
	
	SelectSection: function (id_section) {
		$.ajax({
			url: api_url + "adherent_list",
			type: "POST",
			data: {
				id_section: id_section
			},
			success: function(data) {
				var nbr = 0;

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
		var dom = $("<tr class='" + data_adherent.status + "' data-id-adherent='" + data_adherent.id + "'></tr>");
		dom.append("<td>" + data_adherent.lastname + "</td>");
		dom.append("<td>" + data_adherent.firstname + "</td>");
		
		var actions = $("<td class='text-right'><div class='btn-group'></div></td>");
		
		data_adherent.actions.forEach(action => {
			var button = null;

			switch(action) {
				case "view":
					button = $("<a class='view-adherent btn btn-primary' href=" + data_adherent.link + " title='Voir le profil de " + data_adherent.firstname + "'><i class='fas fa-eye'></i></a>");
					break;

				case "validate":
					button = $("<button class='modify-adherent btn btn-secondary'><i class='fas fa-check'></i></button>")
					button.click(function(){
						Adherents.ModifyAdherentAction(data_adherent.id);
					});
					break;

				case "remove":
					button = $("<button class='remove-adherent btn btn-danger'><i class='fas fa-trash-alt'></i></button>");
					button.click(function(){
						Adherents.RemoveAdherentAction(data_adherent.id, data_adherent.firstname + " " + data_adherent.lastname);
					});
					break;
			}

			actions.find("div").append(button);
		});
			
		dom.append(actions);

		$("#tableAdherents tbody").append(dom);
	},

	ModifyAdherentAction: function (id_adherent) {
		this.selectedId = id_adherent;

		$.ajax({
			url: api_url + "adherent_validate_form/" + id_adherent,
			type: "GET",
			success: function(response) {
				$('#validateModal').find("#editModalTitle").html("Validation " + response.name);
				$('#validateModal').find(".modal-body").html(response.content);
				$('#validateModal').modal();
			}
		});
	},

	RemoveAdherentAction: function (id_adherent, name) {
		this.selectedId = id_adherent;
		
		$("#removeModal").find("#nameAdherent").html(name);
		$('#removeModal').modal();
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
					var filename = "Export_List_Adherents.csv";
					var blob;
					
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
						var URL = window.URL || window.webkitURL;
						var downloadUrl = URL.createObjectURL(blob);

						if (filename) {
							// use HTML5 a[download] attribute to specify filename
							var a = document.createElement("a");
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

					$('#exportModal').modal('toggle');
				}
			});

			return false;
		});
	}
}