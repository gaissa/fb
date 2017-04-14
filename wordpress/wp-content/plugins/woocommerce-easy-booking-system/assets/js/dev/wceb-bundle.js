(function($) {

	$(document).ready(function() {

		wceb.dateFormat            = wceb_object.booking_dates;
		wceb.firstDate             = parseInt( wceb_object.first_date );
		wceb.bookingMin            = parseInt( wceb_object.min );
		wceb.bookingMax            = ( wceb_object.max === '' ) ? '' : parseInt( wceb_object.max );
		wceb.bookingDuration       = wceb_object.booking_duration;
		wceb.bookingCustomDuration = parseInt( wceb_object.booking_custom_duration );
		wceb.priceHtml             = wceb_object.prices_html;

		$bookingPrice = $('.booking_price');
		$body         = $('body');
		$bundle_wrap  = $('.bundle_form .bundle_data');
		$bundle_price = $bundle_wrap.find('.bundle_price');

		function updateBundle() {

			// Get selected bundled item IDs with quantity
			var children = wceb.get.childrenIds();

			// Variable bundled items
			$('.cart[data-type="variable"]').each( function() {

				var $this = $(this);
				var variation_data = $this.data('product_variations');

				// Get selected variation data
				$.each( variation_data, function( index, data ) {

					var variationID = data.variation_id;

					// If variation is selected
					if ( typeof children[variationID] !== 'undefined' ) {

						// Variation data
						current_variation = variation_data[index];

						// Trigger custom "found_variation" event
						$this.trigger( 'wceb_variation_found', [current_variation, $this] );

						// Remove booking price text (/ day, / night, etc.) for the variation
						$this.find( '.wceb-price-format' ).remove();

						// Display booking price text (/ day, / night, etc.) for the variation if it is bookable
						if ( true === current_variation.is_bookable ) {
							$this.find( '.price .amount' ).append( '<span class="wceb-price-format"></span>' );
							$this.find( '.wceb-price-format' ).html( wceb.priceHtml );
						}

					}

				});

			});

			var ids = {};

			// Calculated new price
			$.each( children, function( id, quantity ) {

				if ( quantity > 0 ) {
					ids[id] = quantity;
				}

			});

			wceb.pickers.init();
			wceb.pickers.render( ids );

		}

		function updateBundlePrice( totals ) {

			totalBundlePrice = totals.price;
			totalBundleRegularPrice = totals.regular_price;

			$bookingPrice.attr('data-booking_price', totalBundlePrice );
			$bookingPrice.attr('data-booking_regular_price', totalBundleRegularPrice );

			var additional_costs = wceb.get.additionalCosts();

			// Hide default bundle price
			$bundle_price.remove();

			if ( $('.cart').find('input[name="quantity"]').size() ) {
				var qty = parseFloat( $('.cart').find('input[name="quantity"]').val() );
			} else {
				var qty = 1;
			}

			var price           = parseFloat( ( totalBundlePrice + additional_costs ) * qty  );
			var regularPrice    = parseFloat( ( totalBundleRegularPrice + additional_costs ) * qty  );

			var formatted_price = '<span class="woocommerce-Price-amount amount">' + wceb.formatPrice( price ) + '</span>';

			if ( price !== regularPrice ) {
				var formatted_price = '<del><span class="woocommerce-Price-amount amount">' + wceb.formatPrice( regularPrice ) + '</span></del> <ins><span class="woocommerce-Price-amount amount">' + wceb.formatPrice( price ) + '</span></ins>';
			}

			// Update booking price with (maybe) additional costs
			$bookingPrice.find('.price').html( formatted_price );

		}

		$bundle_wrap.on( 'woocommerce-product-bundle-show', function() {

			$('.wceb_picker_wrap').slideDown( 200 );

			// Update calendars
			updateBundle();

			// Disable add to cart button only if dates are not set
			if ( wceb.dateFormat === 'two' && ! wceb.checkIf.datesAreSet() ) {
				$('.single_add_to_cart_button').prop( 'disabled', true );
			} else if ( wceb.dateFormat === 'one' && ! wceb.checkIf.dateIsSet( 'start' ) ) {
				$('.single_add_to_cart_button').prop( 'disabled', true );
			}

		});

		$bundle_wrap.on( 'woocommerce-product-bundle-hide', function() {
			$('.wceb_picker_wrap').hide();
		});

		// Update totals
		$bundle_wrap.on( 'woocommerce-product-bundle-updated-totals', function( event, bundle ) {
			
			var totals = bundle.api.get_bundle_totals();

			// Update totals
			updateBundlePrice( totals );

		});

	});

})(jQuery);