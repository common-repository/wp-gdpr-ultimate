var $ = jQuery.noConflict();
var form = $('body').find('form');
$(form).each(function () {
	$(this).validate({
		ignore: 'input[type="text"]',
		rules: {
			gdrp: {
				required: true,
			},
		},
		highlight: function (element, errorClass, validClass) {
			if ($('.gdprcontainer input').is(':checked')) {
				var csslight = '.gdprcontainer li label::before { content: "\\2714"; }';
				$('head').append('<style>' + csslight + '</style>');
			}
			else {
				var cssunlight = '.gdprcontainer li label::before { content: " "; border-color: #3a677c;}';
				$('head').append('<style>' + cssunlight + '</style>');
			}
			$(element).addClass(errorClass).removeClass(validClass);
		},
		unhighlight: function (element, errorClass, validClass) {
			if ($('.gdprcontainer input').is(':checked')) {
				var csslight = '.gdprcontainer li label::before { content: "\\2714";}';
				$('head').append('<style>' + csslight + '</style>');
			}
			else {
				var cssunlight = '.gdprcontainer li label::before { content: " "; border-color:#3a677c;}';
				$('head').append('<style>' + cssunlight + '</style>');
			}
			$(element).removeClass(errorClass).addClass(validClass);
			$(element).closest('ul').removeClass(errorClass);
		},
		errorLabelContainer: ".js-errors",
		errorElement: "li",
		errorPlacement: function (error, element) {
			if (element.attr("name") == "gdrp") {
				error.insertBefore(element);
			}
		},
	});
});
