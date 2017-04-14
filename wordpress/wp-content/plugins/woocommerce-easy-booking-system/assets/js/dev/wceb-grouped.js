(function($) {

	$(document).ready(function() {

		wceb.dateFormat            = wceb_object.booking_dates,
		wceb.firstDate             = parseInt( wceb_object.first_date ),
		wceb.bookingMin            = parseInt( wceb_object.min ),
		wceb.bookingMax            = ( wceb_object.max === '' ) ? '' : parseInt( wceb_object.max ),
		wceb.bookingDuration       = wceb_object.booking_duration,
		wceb.bookingCustomDuration = parseInt( wceb_object.booking_custom_duration ),
		wceb.priceHtml             = wceb_object.prices_html;

		$pickerWrap   = $('.wceb_picker_wrap');
		$bookingPrice = $('.booking_price');

		$pickerWrap.hide();

		wceb.pickers.init();
		
		$('.cart').on( 'change', '.quantity input.qty', function() {

			var $this      = $(this),
				ids        = {},
				quantities = [];

			totalGroupedPrice = 0;
			totalGroupedRegularPrice = 0;

			var children = wceb.get.childrenIds();

			$.each( children, function( id, qty ) {

				var price = wceb_object.product_price[id];
				var regular_price = wceb_object.product_regular_price[id];

				if ( qty > 0 ) {
					totalGroupedPrice += parseFloat( price * qty );

					if ( regular_price !== '' ) {
						totalGroupedRegularPrice += parseFloat( regular_price * qty );
					} else {
						totalGroupedRegularPrice += parseFloat( price * qty );
					}

					ids[id] = qty;
				}

				quantities.push( qty );

			});

			// Get highest quantity selected
			max_qty = Math.max.apply( Math, quantities );

			// Hide date inputs if no quantity is selected
			( max_qty > 0 ) ? $pickerWrap.slideDown( 200 ) : $pickerWrap.hide();

			var formatted_price = '<span class="woocommerce-Price-amount amount">' + wceb.formatPrice( totalGroupedPrice ) + '</span>';

			if ( totalGroupedPrice !== totalGroupedRegularPrice ) {
				var formatted_price = '<del><span class="woocommerce-Price-amount amount">' + wceb.formatPrice( totalGroupedRegularPrice ) + '</span></del> <ins><span class="woocommerce-Price-amount amount">' + wceb.formatPrice( totalGroupedPrice ) + '</span></ins>';
			}

			$bookingPrice.html( '<span class="price">' + formatted_price + '</span>' );
			
			wceb.pickers.init();
			wceb.pickers.render( ids );

		});

	});

})(jQuery);