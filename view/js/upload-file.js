/* =======================================
 * This plugin is made by Sparky
 * and it needs Bootstrap 4
 * =======================================
 */

(function($) {
	$.fn.UploadFile = function(options = {}) {
		var defaults = {
			url: '/',               // URL where to send files.
			buttonText:"Add files", // Name of the send button.
			autoUpload: true,       // Download automaticaly when files are selected.
			acceptFileTypes: null,  // Type of accepted files.
			maxFileSize: 10000,     // Max size for each file. 10000 = 1ko
			maxSimultaneousFile: 3, // Max number of simultaneous file.
			maxFiles: 100,          // Max number of files.
			disableDom: ''          // Allow to disable a dom element.
		};
		var Options = $.extend(defaults, options);
		var Element = $(this);

		var UFile = function(options) {
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
				mignature();
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

			var mignature = function() {
				// Mignature picture of the preview
				var overlay = $("<div class='uploadFile-preview-overlay'></div>");
				overlay.append("<div class='uploadFile-preview-progress progress'><div class='progress-bar' role='progressbar' aria-valuemin='0' aria-valuemax='100'></div></div>");
				overlay.append("<div class='uploadFile-preview-message'>En attente</div>");

				var info = $("<div class='uploadFile-preview-info'></div>");
				info.append("<div class='uploadFile-preview-img'></div>");
				info.append("<div class='uploadFile-preview-name'>" + vars.file.name + "</div>");

				var box = $("<div class='uploadFile-preview-box'></div>");
				box.append(info);
				box.append(overlay);

				element = $("<div class='uploadFile-preview'></div>");
				element.append(box);

				element.find(".uploadFile-preview-img").css("background-image", "url(" + window.URL.createObjectURL(vars.file) + ")");
				element.find(".uploadFile-preview-progress").hide();

				vars.dom.find(".uploadFile-files").prepend(element);
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

		// Main object
		var Upload = {
			files: [],    // List of files to send.
			nb_files: 0,  // Number of files.
			pos: 0,       // Position in the file list.
			nb_runing: 0, // Number of file in uploading.
			
			Init: function () {
				Element.find(".uploadFile-input").hide();
				Element.find(".uploadFile-button").click(function(){
					Element.find(".uploadFile-input").click();
				});
				this.InitDragDrop();

				// Send
				Element.find(".uploadFile-input").change(function(){
					Upload.Handle(this.files);
				});
				
				console.log("Init ok");
			},

			InitDragDrop: function() {
				window.URL = window.URL || window.webkitURL;
				
				Element.bind("dragenter dragover", function(){
					Element.find(".uploadFile-drop").addClass("droped");
					return false;
				});
				Element.bind("dragleave", function(){
					Element.find(".uploadFile-drop").removeClass("droped");
					return false;
				});
				Element.bind("drop", function(event){
					Element.find(".uploadFile-drop").removeClass("droped");
					
					if(event.originalEvent.dataTransfer){
						if(event.originalEvent.dataTransfer.files.length) {
							event.preventDefault();
							event.stopPropagation();
							Upload.Handle(event.originalEvent.dataTransfer.files);
						}  
					}
				});
			},

			Handle: function (inputFiles) {
				var nb = inputFiles.length;
				
				if(nb != 0) {
					console.log("File Added");
					
					if(Options.disableDom != '')
						$(Options.disableDom).prop('disabled', true);
					
					for(var i = 0; i < nb; i++) {
						var file = inputFiles[i];

						if(this.nb_files < Options.maxFiles) {
							if(file.type.match(Options.acceptFileTypes)) { // If the file has the good type.
								this.files.push(new UFile({id: this.nb_files, tuteur: this, dom: Element, url: Options.url, file: file}));
								this.nb_files ++;
							} else {
								alert("Le format du fichier n'est pas bon.");
							}
						} else {
							alert('Vous ne pouvez pas mettre plus de ' + Options.maxFiles + ' photos.');
							break;
						}
					}

					console.log("Run transfert");
					this.Transfer();
				}
			},

			Transfer: function () {
				var nb_files = this.files.length;
				if(this.pos < nb_files) {
					var nb_to_send = 0;
					var remain = nb_files - this.pos;

					 // Nommbre de fichier restant à envoyer.
					if(remain >= Options.maxSimultaneousFile)
						nb_to_send = Options.maxSimultaneousFile;
					else
						nb_to_send = remain;

					while(this.nb_runing < nb_to_send) {
						this.files[this.pos].send();
						this.nb_runing ++;
						this.pos ++;
					}
				} else {
					if(Options.disableDom != '')
						$(Options.disableDom).prop('disabled', false);
				}
			},

			UploadDone: function () {
				this.nb_runing --;
				this.Transfer();
			}
		};

		Upload.Init();
	};
})(jQuery);