(function($) {

	$(document).ready(function() {
		
		$pickerWrap   = $('.wceb_picker_wrap');
		$priceText    = $('.price').find('.wceb-price-format');
		$bookingPrice = $('.booking_price');
		$body         = $('body');

		$pickerWrap.hide();
		$priceText.hide();

		$body.on( 'found_variation', '.variations_form', function( e, variation ) {

			variationId = variation.variation_id;
			priceHtml   = wceb_object.prices_html[variationId];

			// Clear session
			wceb.clearBookingSession();

			if ( ! variation.is_purchasable || ! variation.is_in_stock || ! variation.variation_is_visible || ! variation.is_bookable ) {

				$pickerWrap.hide();
				$bookingPrice.html('');
				$('.booking_details').html('');

				$('.single_add_to_cart_button').prop( 'disabled', false );

			} else {

				$pickerWrap.slideDown( 200 );
				
				var variationPrice = parseFloat( variation.display_price );
				var variationRegularPrice = parseFloat( variation.display_regular_price );

				$bookingPrice.attr('data-booking_price', variationPrice );
				$bookingPrice.attr('data-booking_regular_price', variationRegularPrice );

				var additional_costs = wceb.get.additionalCosts();

				if ( $('.cart').find('input[name="quantity"]').size() ) {
					var qty = parseFloat( $('.cart').find('input[name="quantity"]').val() );
				} else {
					var qty = 1;
				}

				var variationPrice           = parseFloat( ( variationPrice + additional_costs ) * qty  );
				var variationRegularPrice    = parseFloat( ( variationRegularPrice + additional_costs ) * qty  );

				var price = '<span class="amount">' + wceb.formatPrice( variationPrice ) + '</span>';

				if ( variationPrice !== variationRegularPrice ) {
					var price = '<del><span class="woocommerce-Price-amount amount">' + wceb.formatPrice( variationRegularPrice ) + '</span></del> <ins><span class="woocommerce-Price-amount amount">' + wceb.formatPrice( variationPrice ) + '</span></ins>';
				}

				$bookingPrice.html('<span class="price">' + price + '</span>');
				
				$('.booking_details').html('');

				$('.wceb_info_text').html( variation.info_text );

				// Get selected variation booking settings
				wceb.dateFormat            = wceb_object.booking_dates[variationId];
				wceb.firstDate             = parseInt( wceb_object.first_date[variationId] );
				wceb.bookingMin            = parseInt( wceb_object.min[variationId] );
				wceb.bookingMax            = ( wceb_object.max[variationId] === '' ) ? '' : parseInt( wceb_object.max[variationId] );
				wceb.bookingDuration       = wceb_object.booking_duration[variationId];
				wceb.bookingCustomDuration = parseInt( wceb_object.booking_custom_duration[variationId] );

				( wceb.dateFormat === 'one' ) ? $pickerWrap.find('.show_if_two_dates').hide() : $pickerWrap.find('.show_if_two_dates').show();

				wceb.pickers.init();
				wceb.pickers.render( variation );

			}

			// Hide "/ day" or "/ night" if variation is not bookable
			( ! variation.is_bookable ) ? $priceText.hide() : $priceText.html( priceHtml ).show();

		});

		// Reset variations
		$body.on('reset_image', '.variations_form', function( e, variation ) {

			$pickerWrap.hide();
			$priceText.hide();
			$bookingPrice.html('');
			$('.booking_details').html('');

			// Clear session
			wceb.clearBookingSession();

		});

	});

})(jQuery);