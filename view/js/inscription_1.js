(function($) {
	$.fn.InscriptionManager = function(options = {}) {
		var defaults = {};
		var Options = $.extend(defaults, options);
		var Element = $(this);

		// =====================================================================================
		// Objet de gestion de l'envoi des fichiers
		// =====================================================================================
		var ClassFile = function(options) {
			var vars = null;
			var id = null;
			var element = null;
			var file = null;
			var size = 0;
			var loaded = [];
			var nb_parts = 0;
			var error = false;

			// Constructor
			this.construct = function(options) {
				vars = options;
				id = options.id;
				file = options.file;
				size = file.size;
			};

			this.send = function() {
				element.find(".uploadFile-preview-progress").show();
				element.find(".uploadFile-preview-message").hide();

				// Split the file in blocks
				const BYTES_PER_CHUNK = 2 * 1024 * 1024; // 2MB chunk sizes.
				var blobs = [];
				var start = 0;
				var end = BYTES_PER_CHUNK;

				while(start < size) {
					blobs.push(file.slice(start, end));
					start = end;
					end = start + BYTES_PER_CHUNK;
				}

				// Upload each block
				var blobSize = blobs.length;
				nb_parts = blobs.length;
				var blobPos = 0;

				while(blobPos < blobSize) {
					var data = new FormData();
					data.append("id", id);
					data.append("name", file.name);
					data.append("size", file.size);
					data.append("part", blobPos);
					data.append("tot_parts", blobSize);
					data.append("data", blobs[blobPos]);
					
					$.ajax({
						url: vars.url,
						type: 'POST',
						data: data,
						processData: false,
						contentType: false,

						success: function(response, status) {
							console.log(response);
							
							if(response.status == "success" && !error) {
								nb_parts --;
								
								if(nb_parts <= 0) {
									element.find('.uploadFile-preview-overlay').hide();
									
									element.find('.uploadFile-preview-name').html("#" + response.id_photo);
									
									element.append("<div class='uploadFile-files-remove'><button class='btn btn-danger' type='button' data-id='" + response.id_photo + "'><i class='fa fa-trash'></i></button></div>");
									Album.RefreshActions();
									Album.nbPhotos ++;
									$("#nbPhotos").html(Album.nbPhotos + "");
									
									vars.tuteur.UploadDone();
								}
							} else {
								element.find('.uploadFile-preview-progress').hide();
								element.find('.uploadFile-preview-message').html("Error");
								element.find(".uploadFile-preview-message").show();

								nb_parts = 0;
								error = true;
								vars.tuteur.UploadDone();
							}
						},

						error: function(result, status, error){
							console.log(result);
							console.log(status);
							console.log(error);

							element.find('.uploadFile-preview-progress').hide();
							element.find('.uploadFile-preview-message').html("Error");
							element.find(".uploadFile-preview-message").show();

							nb_parts = 0;
							error = true;
							vars.tuteur.UploadDone();
						},

						xhr: function() // Progress
						{
							var xhr = new window.XMLHttpRequest();
							var part = blobPos;

							xhr.upload.addEventListener("progress", function(evt){
								if (evt.lengthComputable)
									loadedPart(part, evt.loaded);
							}, false);

							return xhr;
						}
					});
					
					blobPos ++;
				}
			};

			var loadedPart = function(part, loaded_data) {
				loaded[part] = loaded_data;

				var loaded_tot = 0;
				for (var i = loaded.length - 1; i >= 0; i--)
					loaded_tot += loaded[i];

				if(loaded_tot < size) {
					var percentComplete = loaded_tot / size * 100;
					element.find('.uploadFile-preview-progress div').css('width', percentComplete + "%");
				}
			};

			// Pass options when class instantiated
			this.construct(options);
		};

		// =====================================================================================
		// Objet de gestion des adhérents
		// =====================================================================================
		var ClassAdeherent = function(p_tuteur, p_id, p_template, p_dom) {
			var a_tuteur = null;
			var a_id= null;
			var a_dom = null;
			var a_element = null;
			var a_file_id_card = null;
			var a_file_photo = null;

			// Constructor
			this.Construct = function(p_tuteur, p_id, p_template, p_dom) {
				this.a_tuteur = p_tuteur;
				this.a_id = p_id;
				this.a_dom = p_dom;
				this.a_element = p_template.clone();
				
				this.InitElement();
				this.SetActions();
			};

			this.InitElement = function () {
				var tuteur = this.a_tuteur;
				var id = this.a_id;
				var element = this.a_element;

				element.attr("id", "adherent_" + id);
				element.find(".remove-button").click(function(){
					tuteur.RemoveAdherent(id);
				});

				element.find("input.inputRadioMedicine1").attr("id", "inputRadioMedicine1_" + id);
				element.find("label.inputRadioMedicine1").attr("for", "inputRadioMedicine1_" + id);
				element.find("input.inputRadioMedicine2").attr("id", "inputRadioMedicine2_" + id);
				element.find("label.inputRadioMedicine2").attr("for", "inputRadioMedicine2_" + id);

				element.find("input.inputRadioTenue1").attr("id", "inputRadioTenue1_" + id);
				element.find("label.inputRadioTenue1").attr("for", "inputRadioTenue1_" + id);
				element.find("input.inputRadioTenue2").attr("id", "inputRadioTenue2_" + id);
				element.find("label.inputRadioTenue2").attr("for", "inputRadioTenue2_" + id);

				this.a_dom.append(element);
			};

			this.GetId = function () {
				return this.a_id;
			};

			this.GetFields = function () {
				return {
					firstname: this.a_element.find("input[name='prenomInput']").val(),
					lastname: this.a_element.find("input[name='nomInput']").val(),
					birthday: this.a_element.find("input[name='birthdayInput']").val(),
					medicine: this.a_element.find("input[name='medicineInput']:checked").val(),
					infoMedicine: this.a_element.find("input[name='traitementInfoInput']").val(),
					tenue: this.a_element.find("input[name='tenueInput']:checked").val(),
					sportmut: this.a_element.find("input[name='sportmutInput']").val()
				};
			};

			this.SetNumber = function (p_number) {
				this.a_element.find(".adherent-title").html("Adhérent " + p_number);
			};

			this.SetActions = function () {
				this.a_element.find(".inputRadioMedicine1").change(function(){
					if($(this).prop("checked") == true)
						$(this).parent().parent().find(".traitementInfo").show();
				});

				this.a_element.find(".inputRadioMedicine2").change(function(){
					if($(this).prop("checked") == true)
						$(this).parent().parent().find(".traitementInfo").hide();
				});
			};

			// Pass options when class instantiated
			this.Construct(p_tuteur, p_id, p_template, p_dom);
		};

		// =====================================================================================
		// Objet principal de l'étape 1 de l'inscription
		// =====================================================================================
		var StepAdherents = {
			templateAdherent: null,
			adherentsList: [],
			lastIdAdherent: 0,
	
			Init: function () {
				this.templateAdherent = $("#templateAdherent");
				this.templateAdherent.find(".traitementInfo").hide();
				$("#templateAdherent").remove();
				this.templateAdherent.removeAttr("id");
				
				$("#adherents").append();
				
				$("#addAdherentButton").click(function(){
					StepAdherents.AddAdherent();
				});
				this.AddAdherent();
				
				$("#validButton").click(function(){
					StepAdherents.ValidForm();
				});
			},
			
			AddAdherent: function () {
				this.adherentsList.push(new ClassAdeherent(this, this.lastIdAdherent, this.templateAdherent, $('#adherents')))
				this.lastIdAdherent ++;

				this.RenameAdherents();
			},

			RemoveAdherent: function (p_id) {
				this.adherentsList = this.adherentsList.filter(function(value, index, arr){
					return value.GetId() != p_id;
				});
				$("#adherent_" + p_id).remove();

				this.RenameAdherents();
			},
			
			RenameAdherents: function () {
				var num = 1;
				this.adherentsList.forEach(function(item, index){
					item.SetNumber(num);
					num ++;
				});
			},
			
			ValidForm: function () {
				var data = {
					adherents: []
				};

				this.adherentsList.forEach(function(item, index){
					data.adherents.push(item.GetFields());
				});

				$.ajax({
					url: Options.UrlApi + "inscription_validate_adherents",
					type: "POST",
					data: data,
					success: function(response) {
						if(response.result)
							location.reload();
						else
							alert(response.message);
					}
				});
			}
		};
		
		StepAdherents.Init();
	};
})(jQuery);