(function($) {
	$(document).ready(function() {

		$('.easy-booking-notice-close').on('click', function(e) {
			e.preventDefault();
			
			var $this = $(this),
				notice = $this.data('notice');

			var data = {
				action: 'wceb_hide_notice',
				security: ajax_object.hide_notice_nonce,
				notice: notice
			};

			$.ajax({
				url :  ajax_object.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					$this.parents('.easy-booking-notice').hide();
				}
			});
			
		});
		
	});
})(jQuery);