(function($) {
	$.fn.SpForm = function(fnSend = function(){}, fnResponse = function(data){}, options = {}) {
		var defaults = {};
		var Options = $.extend(defaults, options);
		var Element = $(this);

		var Form = {
			fields: {},
			
			Init: function () {
				Element.find("input[type='text'], input[type='password'], input[type='email'], input[type='date'], input[type='datetime-local'], input[type='month'], input[type='number']").each(function () {
					var id = $(this).attr("name");
					Form.fields[id] = $(this).val();

					$(this).keyup(function () {
						if($(this).val() != Form.fields[id]){
							Form.fields[id] = $(this).val();
							var regex = Form.Regex($(this));
							if(regex.test($(this).val()))
								$(this).removeClass("incorrect");
							else
								$(this).addClass("incorrect");
						}
					});
				});

				Element.find("input[type='date'], input[type='datetime-local']").each(function () {
					var id = $(this).attr("name");
					Form.fields[id] = $(this).val();

					$(this).change(function () {
						if($(this).val() != Form.fields[id]){
							Form.fields[id] = $(this).val();
							var regex = Form.Regex($(this));
							if(regex.test($(this).val()))
								$(this).removeClass("incorrect");
							else
								$(this).addClass("incorrect");
						}
					});
				});

				Element.find("input[type='checkbox']").each(function () {
					var id = $(this).attr("name");
					Form.fields[id] = $(this).is(':checked');

					$(this).click(function () {
						Form.fields[id] = $(this).is(':checked');
					});
				});

				Element.find("input[type='radio']").each(function () {
					var id = $(this).attr("name");
					Form.fields[id] = $(this).val();

					$(this).change(function () {
						Form.fields[id] = $(this).val();
					});
				});

				Element.find("textarea").each(function () {
					var id = $(this).attr("name");
					Form.fields[id] = $(this).val();

					$(this).keyup(function () {
						if($(this).val() != Form.fields[id]){
							Form.fields[id] = $(this).val();
							var regex = /.*\S.*/;
							if(regex.test($(this).val()))
								$(this).removeClass("incorrect");
							else
								$(this).addClass("incorrect");
						}
					});
				});

				Element.find("select").each(function () {
					var id = $(this).attr("name");
					Form.fields[id] = $(this).val();

					$(this).click(function () {
						if($(this).val() != Form.fields[id]){
							Form.fields[id] = $(this).val();
							var regex = Form.Regex($(this));
							if(regex.test($(this).val()))
								$(this).removeClass("incorrect");
							else
								$(this).addClass("incorrect");
						}
					});
				});


				// Trigger when you submit the form.
				Element.submit(function(){
					Element.find("input, textarea, select").each(function () {
						var id = $(this).attr("name");
						Form.fields[id] = $(this).val();
					});
					Element.find("input[type='checkbox']").each(function () {
						var id = $(this).attr("name");
						Form.fields[id] = $(this).is(':checked');
					});
					Element.find("input[type='radio']:checked").each(function () {
						var id = $(this).attr("name");
						Form.fields[id] = $(this).val();
					});
					console.log(Form.fields);
					
					fnSend();

					$.ajax({
						method: "POST",
						url: Element.attr("action"),
						data: Form.fields
					}).done(function(data) {
						fnResponse(data);
					});
					return false;
				});
			},

			Regex: function (field) {
				switch(field.attr("type")){
					case "text":
						return /.*\S.*/;
						break;

					case "email":
						return /^[a-z0-9._-]+@[a-z0-9._-]+\.[a-z]{2,6}$/;
						break;

					case "date":
						return /^[0-9 -\/\.]{10}$/;
						break;

					default:
						return /.*\S.*/;
						break;
				}
			}
		};

		Form.Init();
	};
})(jQuery);