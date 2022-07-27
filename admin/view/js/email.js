$(document).ready(function(){
	Email.Init();
});

function sendFormCallback() {
	$("#emailForm form").submit();
}

var Email = {
	Files: [],

	Init: function () {
		$("#emailSend").hide();
		$("#emailResponse").hide();

		this.AttachmentManager();
		this.SubmitManager();
	},

	AttachmentManager: function () {
		$('#attachementSelectFile').hide();

		$('#attachementSelectFile').change(function(){
			var nb = this.files.length;
			
			if(nb != 0) {
				for(var i = 0; i < nb; i++)
					Email.Files.push(this.files[i]);

				Email.UpdateAttachments();
			}

			$(this).val("");
		});

		$('#addAttachement').click(function(){
			$('#attachementSelectFile').click();
		});
	},

	UpdateAttachments: function () {
		var html = "";
		var nb = Email.Files.length;

		for(var i = 0; i < nb; i++) {
			var file = Email.Files[i];
			html += "<button class='btn-attachement' data-index=" + i + ">" + file.name + " <i class='fas fa-times-circle'></i></button>";
		}

		$('#attachementsList').html("");
		$('#attachementsList').html(html);

		$('.btn-attachement').click(function(){
			Email.Files.splice($(this).data("index"), 1);
			Email.UpdateAttachments();
		});
	},

	SubmitManager: function () {
		$("#formButton").click(function() {
		//$("#emailForm form").submit(function() {
			$("#emailForm").hide();
			$("#emailSend").show();

			var formData = new FormData();
			formData.append('id_section', $("#inputSection").val());
			formData.append('subject', $("#inputSubject").val());
			formData.append('message', $("#inputMessage").val());

			var nb = Email.Files.length;
			for(var i = 0; i < nb; i++)
				formData.append('files[]', Email.Files[i], Email.Files[i].name);

			$.ajax({
				//url: $(this).attr("action"),
				url: api_url,
				type: "POST",
				data: formData,
				contentType: false,
				processData: false,
				success: function(response) {
					console.log(response);
	
					if(response.error)
						console.log(response.errorMessage);
	
					$("#emailResponse").html('<p class="text-center"><i class="fa fa-grin-alt"></i> ' + response.message + '</p>');
					$("#emailSend").hide();
					$("#emailResponse").show();
				}
			});

			return false;
		});
	}
}