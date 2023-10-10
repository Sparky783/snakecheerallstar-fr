$(document).ready(function(){
	Presences.init();
});

let Presences = {
	selectedSection: null,
	messageModal: null,
	isStatView: false,

	init: function () {
		this.messageModal = new bootstrap.Modal('#messageModal');

		$('#selectedSection').change(function(){
			Presences.selectedSection  = $('#selectedSection').val();
			Presences.refresh();
		});

		$("#validatePresences").click(function(){
			let status = [];

			$("#adherentsList .adherent").each(function(){
				status.push({
					id: $(this).data("id"),
					state: $(this).data("value")
				});
			});

			$.ajax({
				url: api_url + "validate_presences",
				type: "POST",
				data: {
					id_section: Presences.selectedSection,
					status: status
				},
				success: function(result) {
					$('#messageModal .modal-body').html(result.message);
					Presences.messageModal.show();
				}
			});
		});

		$("#statButton").click(function(){
			if (Presences.isStatView) {
				Presences.refresh();
			} else {
				$.ajax({
					url: api_url + "presences_stats",
					type: "POST",
					data: {
						id_section: Presences.selectedSection,
					},
					success: function(data) {
						$("#adherentsList").html('');

						data.adherents.forEach(adherentInfo => {
							Presences.addAdherentStat(adherentInfo);
						});

						$("#statButton").html('Saisir les présences');
						$("#presences .card-footer").hide();
						Presences.isStatView = true;
					}
				});
			}
		});
	
		$('#message').hide();

		this.selectedSection = $('#selectedSection').val();
		this.refresh();
	},
	
	refresh: function () {
		$.ajax({
			url: api_url + 'presences_list/' + this.selectedSection,
			type: 'GET',
			success: function(data) {
				$("#adherentsList").html('');

				data.adherents.forEach(adherentInfo => {
					Presences.addAdherent(adherentInfo);
				});

				$('#presences-content .card-header').html('Il y a ' + data.adherents.length + ' élèves dans cette section');

				$("#statButton").html('Voir les statistiques');
				$("#presences .card-footer").show();
				Presences.isStatView = false;
			}
		});
	},

	addAdherent: function (adherentInfo) {
		let status = "";

		switch (adherentInfo.status) {
			case "present": status = "- Présent"; break;
			case "justify": status = "- Absence justifiée"; break;
			case "late": status = "- En retard"; break;
			case "absent": status = "- Absent"; break;
		}

		let element = $(`
			<div class="adherent" data-id="${adherentInfo.id_adherent}" data-value="${adherentInfo.status}">
				<div class="adherent-text ${adherentInfo.status}">
					<span class="name">${adherentInfo.firstname} ${adherentInfo.lastname}</span>
					<span class="status">${status}</span>
				</div>
				
				<div class='presence-radio text-end'>
					<div class='btn-group'>
						<button class='btn btn-present${adherentInfo.status === "present" ? " active" : ""}' data-type='present' title='Présent'><i class='far fa-thumbs-up'></i></button>
						<button class='btn btn-justify${adherentInfo.status === "justify" ? " active" : ""}' data-type='justify' title='Absence justifiée'><i class='far fa-file-alt'></i></button>
						<button class='btn btn-late${adherentInfo.status === "late" ? " active" : ""}' data-type='late' title='En retard'><i class='far fa-clock'></i></button>
						<button class='btn btn-absent${adherentInfo.status === "absent" ? " active" : ""}' data-type='absent' title='Absent'><i class='fas fa-ban'></i></button>
					</div>
				</div>
			</div>`);

		element.Adherent();
		$("#adherentsList").append(element);
	},

	addAdherentStat: function (adherentInfo) {
		let element = $(`
			<div class="adherent">
				<div class="adherent-text">
					<span class="name">${adherentInfo.firstname} ${adherentInfo.lastname}</span>
				</div>
				
				<div class='stats text-center'>
					<span class='stat-present'><i class='far fa-thumbs-up'></i> ${adherentInfo.status.present}</span>
					<span class='stat-justify'><i class='far fa-file-alt'></i> ${adherentInfo.status.justify}</span>
					<span class='stat-late'><i class='far fa-clock'></i> ${adherentInfo.status.late}</span>
					<span class='stat-absent'><i class='fas fa-ban'></i> ${adherentInfo.status.absent}</span>
				</div>
			</div>`);

		$("#adherentsList").append(element);
	}
};

(function($) {
	$.fn.Adherent = function() {
		let Element = $(this);

		let Radio = {
			init: function () {
				Element.find(".presence-radio button").click(function(){
					Element.find(".presence-radio button").each(function(){
						$(this).removeClass("active");
					});

					$(this).addClass("active");

					Element.data("value", $(this).data("type"));

					Element.find('.adherent-text .status').html('- ' + $(this).attr('title'));
					Element.find('.adherent-text').removeClass("present justify late absent");
					Element.find('.adherent-text').addClass($(this).data("type"));
				});
			},
		};

		Radio.init();
	};
})(jQuery);