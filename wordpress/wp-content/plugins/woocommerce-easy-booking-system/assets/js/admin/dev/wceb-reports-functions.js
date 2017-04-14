(function($) {
	$(document).ready(function() {

		var $input = $('.datepicker').pickadate({
			formatSubmit: 'yyyy-mm-dd'
		});

		var picker = $input.pickadate('picker');
		
	});
})(jQuery);