var wceb = {

	// General settings
	productType  : wceb_object.product_type,
	calcMode     : wceb_object.calc_mode, // Days or Nights
	maxYear      : parseInt( wceb_object.max_year ), // Max year
	maxOption    : new Date( parseInt( wceb_object.max_year ), 11, 31 ), // December 31st of max year
	allowDisabled: wceb_object.allow_disabled, // Allow disabled dates inside booking period
	session      : false,

	// Checking functions
	checkIf: {
		isDate     : null,
		isDay      : null,
		isArray    : null,
		isObject   : null,
		isDisabled : null,
		dateIsSet  : null,
		datesAreSet: null
	},

	get: {
		firstAvailableDate: null,
		basePrice         : null,
		additionalCosts   : null,
		childrenIds       : null,
		minAndMax         : null,
		closestDisabled   : null,
		closest           : null
	},

	createDateObject    : null,
	clearBookingSession : null,
	applyBookingDuration: null,
	formatPrice         : null,
	setPrice            : null,

	// Picker functions
	picker: {
		close: null,
		set  : null
	},

	// Pickers functions
	pickers: {
		init       : null,
		render     : null,
		clearSecond: null,
		set        : null
	}

};

(function($) {

	$(document).ready( function() {

		// Start picker
		$inputStart = $('.datepicker_start').pickadate();
		pickerStart = $inputStart.pickadate('picker');
		pickerStartItem = pickerStart.component.item;

		// End picker
		$inputEnd   = $('.datepicker_end').pickadate();
		pickerEnd   = $inputEnd.pickadate('picker');
		pickerEndItem   = pickerEnd.component.item;

		var selectedDates = {
			startFormat: null,
			endFormat  : null,
			start      : null,
			end        : null
		};

		/**
		* Check if is date (date object)
		*/
		wceb.checkIf.isDate = function( date ) {
			return ( date instanceof Date );
		}

		/**
		* Check if is weekday (1,2,3,4,5,6,7)
		*/
		wceb.checkIf.isDay = function( date ) {
			return ( ! isNaN( date ) && ( date >= 1 && date <= 7 ) );
		}

		/**
		* Check if is array ([1,0,2016])
		*/
		wceb.checkIf.isArray = function( date ) {
			return ( date instanceof Array );
		}

		/**
		* Check if is an object and not a date (from: [1,0,2016]; to: [1,0,2016])
		*/
		wceb.checkIf.isObject = function( date ) {
			return ( ( typeof date === 'object' ) && ! ( date instanceof Date ) );
		}

		/**
		* Check if the date is disabled
		*/
		wceb.checkIf.isDisabled = function( disabled, dateToEnable ) {

			if ( typeof disabled === 'undefined' ) {
				return false;
			}

		 	var d = false;

		 	var timeToEnable = dateToEnable.pick;

			$.each( disabled, function( index, dateObject ) {

				// [year, month, day]
				if ( wceb.checkIf.isArray( dateObject ) ) { 

					dateObject = new Date( dateObject[0], dateObject[1], dateObject[2] );

					if ( timeToEnable === dateObject.getTime() ) {
						d = true;
						return;
					}

				// { from: [year, month, day], to: [year, month, day] }
				} else if ( wceb.checkIf.isObject( dateObject ) ) {

					start = new Date( dateObject['from'][0], dateObject['from'][1], dateObject['from'][2] );
					end   = new Date( dateObject['to'][0], dateObject['to'][1], dateObject['to'][2] );

					if ( timeToEnable >= start.getTime() && timeToEnable <= end.getTime() ) {
						d = true;
						return;
					}

				// 1, 2, 3, 4, 5, 6, 7
				} else if ( wceb.checkIf.isDay( dateObject ) ) {

					var day = dateToEnable.day;

					if ( day === 0 ) {
						day = 7;
					}

					if ( dateObject === day ) {
						d = true;
						return;
					}

				// Date object
				} else if ( wceb.checkIf.isDate( dateObject ) ) { 

					if ( timeToEnable === dateObject.getTime() ) {
						d = true;
						return;
					}

				}

			});

			return d;

		}

		/**
		* Check if a date is set
		*/
		wceb.checkIf.dateIsSet = function( date ) {

			// If the calendar is specified, get the selected date corresponding
			if ( date === 'start' ) {
				var date = pickerStart.get('select');
			} else if ( date === 'end' ) {
				var date = pickerStart.get('select');
			}

			return ( typeof date !== 'undefined' && date !== null );
		}

		/**
		* Check if both dates (start and end) are set
		*/
		wceb.checkIf.datesAreSet = function() {
			var startSelected = pickerStart.get('select'),
				endSelected   = pickerEnd.get('select');

			return ( ( startSelected !== null && typeof startSelected !== 'undefined' ) && ( endSelected !== null && typeof endSelected !== 'undefined' ) );
		}

		/**
		* Get the first available date
		*/
		wceb.get.firstAvailableDates = function() {

			var dates = {};
			var firstDay = + parseInt( wceb.firstDate );

			if ( firstDay <= 0 ) {
				var firstDay = false;
			}

			// Get first available date
			var first = wceb.createDateObject( false, firstDay );

			// Get start picker disabled dates
			var disabled = pickerStartItem.disable;

			// If first available date is disabled, check the next date until one is available
			while ( true === wceb.checkIf.isDisabled( disabled, first ) ) {
				var first = wceb.createDateObject( first.obj, 1 );
			}

			dates['start'] = first;

			if ( wceb.dateFormat === 'two' ) {

				var startFirst = new Date( first.pick );
				// Get end picker first available date
				var endFirst = wceb.createDateObject( startFirst, wceb.bookingMin );

				// Get end picker disabled dates
				var endDisabled = pickerEndItem.disable;

				// If end picker first available date is disabled, check the next date until one is available
				while ( true === wceb.checkIf.isDisabled( endDisabled, endFirst ) ) {
					var endFirst = wceb.createDateObject( endFirst.obj, 1 );
				}

				dates['end'] = endFirst;

			}
			
			return dates;

		}

		/**
		* Get produt booking price, multiplied by quantity selected
		*/
		wceb.get.basePrice = function() {

			if ( $('.cart').find('input[name="quantity"]').size() ) {
				var qty = parseFloat( $('.cart').find('input[name="quantity"]').val() );
			} else {
				var qty = 1;
			}

			var product_price = parseFloat( $('.booking_price').attr('data-booking_price') ),
				addon_costs   = ( wceb.dateFormat === 'two' && ! wceb.checkIf.datesAreSet() ) || ( wceb.dateFormat === 'one' && ! wceb.checkIf.dateIsSet( 'start' ) ) ? wceb.get.additionalCosts() : 0,
				total_price   = parseFloat( ( addon_costs + product_price ) * qty );

			return total_price;

		}

		/**
		* Get produt booking regular price, multiplied by quantity selected
		*/
		wceb.get.regularPrice = function() {

			if ( $('.cart').find('input[name="quantity"]').size() ) {
				var qty = parseFloat( $('.cart').find('input[name="quantity"]').val() );
			} else {
				var qty = 1;
			}

			var product_price = parseFloat( $('.booking_price').attr('data-booking_regular_price') ),
				addon_costs   = ( wceb.dateFormat === 'two' && ! wceb.checkIf.datesAreSet() ) || ( wceb.dateFormat === 'one' && wceb.checkIf.dateIsSet( 'start' ) ) ? wceb.get.additionalCosts() : 0,
				total_price   = parseFloat( ( addon_costs + product_price ) * qty );

			return total_price;

		}

		/**
		* WooCommerce Product Add-ons compatibility
		*/
		wceb.get.additionalCosts = function( format ) {

			var total = 0;
			var costs = {};

			if ( wceb.productType === 'bundle' && format === 'each' ) {
				$selector = $('.product').find('form.cart').find('.cart');
			} else if ( wceb.productType === 'bundle' && format !== 'each' ) {
				$selector = $('.product').find('.cart.bundle_data');
			} else {
				$selector = $('.product').find('form.cart');
			}

			$selector.each( function() {

				var product_addon = 0;

				$(this).find( '.addon' ).each( function() {

					var addon_cost = 0;
					var $this = $(this);
					var $parent = $this.parents('.cart');
					
					if ( typeof $parent.data( 'bundle_id' ) !== 'undefined' ) {

						// Item ID for bundled products
						var bundle = $parent.data( 'product_id' );
						var bundle_id = $parent.data( 'bundled_item_id' );
						var bundle_variation = $parent.find('input[name="bundle_variation_id_' + bundle_id + '"]').val();
						var id = ( typeof bundle_variation === 'undefined' ) ? bundle : bundle_variation;

					}

					if ( $this.is('.addon-custom-price') ) {
						addon_cost = $this.val();
					} else if ( $this.is('.addon-input_multiplier') ) {
						if( isNaN( $this.val() ) || $this.val() == "" ) { // Number inputs return blank when invalid
							$this.val('');
							$this.closest('p').find('.addon-alert').show();
						} else {
							if( $this.val() != "" ){
								$this.val( Math.ceil( $this.val() ) );
							}
							$this.closest('p').find('.addon-alert').hide();
						}
						addon_cost = $this.data('price') * $this.val();
					} else if ( $this.is('.addon-checkbox, .addon-radio') ) {
						if ( $this.is(':checked') )
							addon_cost = $this.data('price');
					} else if ( $this.is('.addon-select') ) {
						if ( $this.val() )
							addon_cost = $this.find('option:selected').data('price');
					} else {
						if ( $this.val() )
							addon_cost = $this.data('price');
					}

					if ( ! addon_cost ) {
						addon_cost = 0;
					}

					total += addon_cost;
					product_addon += addon_cost;

					if ( typeof id !== 'undefined' ) {
						costs[id] = product_addon;
					} else {
						var product_id   = $('input[name=add-to-cart]').val();
						var variation_id = $('.variations_form').find('input[name=variation_id]').val();
						var id = ( typeof variation_id !== 'undefined' ) ? variation_id : product_id;
						costs[id] = product_addon;
					}

				});

			});

			// If format is specified to "Array" return array, otherwise return total addon costs
			return ( format === 'each' ) ? costs : total;

		}

		wceb.get.childrenIds = function() {
			var children = {};

			// Get IDs
			if ( wceb.productType === 'grouped' ) {

				var productChildren = wceb_object.children;

				$.each( productChildren, function( index, child ) {

					quantity = $('input[name="quantity[' + child + ']"]').val();

					if ( quantity > 0 ) {
						children[child] = quantity;
					}

				});

			}  else if ( wceb.productType === 'bundle' ) {

				var $bundle_data = $('.cart.bundle_data');
				var item_id      = $bundle_data.data('bundle_id');

				if ( $bundle_data.find( 'input[name="quantity"]').size() ) {
					var bundle_qty = $bundle_data.find( 'input[name="quantity"]').val();
				} else {
					var bundle_qty = 1;
				}

				children[item_id] = bundle_qty;

				var $bundled_items = $('body').find('.bundled_product .cart');

				$bundled_items.each( function() {

					$this     = $(this);
					optional  = $this.data('optional');
					bundle    = $this.data('bundled_item_id');
					child     = $this.data('product_id');
					variation = $this.find('input[name="bundle_variation_id_' + bundle + '"]').val();
					quantity  = $this.find('.bundled_qty').val();

					var id = ( typeof variation === 'undefined' ) ? child : variation;

					if ( optional === 'yes' ) {

						var checked = $('input[name="bundle_selected_optional_' + bundle + '"]').is(':checked');

						if ( false === checked ) {
							quantity = 0;
						}

					}

					if ( id !== '' && quantity > 0 ) {
						children[id] = quantity;
					}

				});

			}

			return children;

		}

		/**
		* Get min and max from a given date, 'operator' depends on the picker set when the function is called (plus or minus)
		*/
		wceb.get.minAndMax = function( disabledDate, operator ) {

			var selectedMinDate = new Date( disabledDate.year, disabledDate.month, disabledDate.date ); // Selected date
			var selectedMaxDate = new Date( disabledDate.year, disabledDate.month, disabledDate.date ); // Selected date
			
			var firstAvailableDates = wceb.get.firstAvailableDates();
			var firstAvailableDate  = operator === 'minus' ? firstAvailableDates['start'].obj : firstAvailableDates['end'].obj; // First available date

			// After setting the end date, if there is no maximum booking duration
			if ( operator === 'minus' && wceb.bookingMax === '' ) {
				// Set min to the first available date
				var selectedMinDate = firstAvailableDate;
			}

			// After setting start date
			if ( operator === 'plus' ) {

				selectedMinDate.setDate( selectedMinDate.getDate() + wceb.bookingMin );

				if ( wceb.bookingMax !== '' ) {
					selectedMaxDate.setDate( selectedMaxDate.getDate() + wceb.bookingMax );
				}

			// After setting end date (reverse min and max)
			} else {

				selectedMaxDate.setDate( selectedMaxDate.getDate() - wceb.bookingMin );

				// If a maxium booking duration is set
				if ( wceb.bookingMax !== '' ) {
					selectedMinDate.setDate( selectedMinDate.getDate() - wceb.bookingMax );
				}
				
			}

			// Check if minimum date is not inferior to the first available date
			if ( firstAvailableDate > selectedMinDate ) {
				selectedMinDate = firstAvailableDate; // If it is, set minimum to first available day
			}

			// If no maximum booking duration is set, set it to false
			if ( operator === 'plus' && wceb.bookingMax === '' ) {
				selectedMaxDate = false;
			}

			// Set maximum to maximum option (max year) if false
			if ( ! selectedMaxDate || wceb.maxOption < selectedMaxDate ) {
				selectedMaxDate = wceb.maxOption;
			}

			var minAndMax = {};
	 		minAndMax['min'] = selectedMinDate;
	 		minAndMax['max'] = selectedMaxDate;

			return minAndMax;

		}

		/**
		* Get the closest disabled date from a given date, 'direction' depends on the picker set when the function is called ('inferior' or 'superior')
		*/
		wceb.get.closestDisabled = function( time, picker, direction ) {
			var selectedDate = new Date( time ), // Get Selected date
				selectedDay  = selectedDate.getDay(); // Get selected day (1, 2, 3, 4, 5, 6, 7)

			var disabled     = picker.get('disable'),
				disabledTime = [];

			$.each( disabled, function( index, date ) {

				// [2016, 01, 01]
				if ( wceb.checkIf.isArray( date ) ) {

					if ( date['type'] === 'booked' || wceb.allowDisabled === 'no' ) {
						var getDate = new Date( date[0], date[1], date[2] );
						disabledTime.push( getDate.getTime() );
					}

				// { from: [2016, 01, 01], to: [2016, 01, 01] }
				} else if ( wceb.checkIf.isObject( date ) ) {

					if ( direction === 'superior' ) {
						var getDate = new Date( date.from[0], date.from[1], date.from[2] );
					} else {
						var getDate = new Date( date.to[0], date.to[1], date.to[2] );
					}
					
					if ( date.type === 'booked' || wceb.allowDisabled === 'no' ) {
						disabledTime.push( getDate.getTime() );
					}

				// Date object
				} else if ( wceb.checkIf.isDate( date ) ) {

					disabledTime.push( date.getTime() );

				// 1, 2, 3, 4, 5, 6, 7
				} else if ( wceb.allowDisabled === 'no' && wceb.checkIf.isDay( date ) ) {

					if ( direction === 'superior' ) {

						var interval = Math.abs( selectedDay - date );

						if ( interval === 0 )
							interval = 7;

						if ( date < selectedDay && interval !== 7 )
							interval = 7 - interval;

						var nextDisabledDay = selectedDate.setDate( selectedDate.getDate() + interval );
						
						disabledTime.push( nextDisabledDay );
						selectedDate = new Date( time ); // Reset selected date

					} else if ( direction === 'inferior' ) {

						var interval = Math.abs( selectedDay - date );

						if ( interval === 0 )
							interval = 7;

						if ( selectedDay < date && interval !== 7 )
							interval = 7 - interval;
						
						previousDisabledDay = selectedDate.setDate( selectedDate.getDate() - interval );
						disabledTime.push( previousDisabledDay );

					}

				}

			});
			
			disabledTime.sort();
			var closestDisabled = wceb.get.closest( disabledTime, time, direction );

			return closestDisabled;

		}

		/**
		* Get the closest date from a given date
		*/
		wceb.get.closest = function( arr, closestTo, direction ) {

			minClosest = false;

		    for ( var i = 0; i < arr.length; i++ ) { // Loop the array

		    	if ( direction === 'superior' ) {

		    		if ( arr[i] > closestTo ) { // Check if it's higher than the date
			    		minClosest = arr[i];
			    		break;
			    	} else {
			    		minClosest = false;
			    	}

		    	} else if ( direction === 'inferior' ) {

		    		if ( arr[i] < closestTo ) { // Check if it's lower than the date
			    		minClosest = arr[i];
			    	}

		    	}

		    }

		    return minClosest;
			
		}

		/**
		* Create date object ({date, day, month, object, pick, year})
		*/
		wceb.createDateObject = function( date, add ) {
			var dateObject = {};

			// If not date, get current date
			if ( ! date ) {
				var date = new Date();
			}

			// Maybe add days
			if ( add ) {
				date.setDate( date.getDate() + add );
			}

			// Create infinity object
			if ( date === 'infinity' ) {
				var dateObject = {
					date : Infinity,
					day  : Infinity,
					month: Infinity,
					obj  : Infinity,
					pick : Infinity,
					year : Infinity
				}

				return dateObject;
			}

			// Check if is valid date
			if ( ! wceb.checkIf.isDate( date ) ) {
				return dateObject;
			}

			// Set date to 00:00
			date.setHours(0,0,0,0);

			// Create date object
			var dateObject = {
				date : date.getDate(),
				day  : date.getDay(),
				month: date.getMonth(),
				obj  : date,
				pick : date.getTime(),
				year : date.getFullYear()
			}

			return dateObject;
		}

		/**
		* Clear session
		*/
		wceb.clearBookingSession = function() {

			if ( wceb.session ) {

				var data = {
					action: 'clear_booking_session',
					security: $('input[name="_wceb_nonce"]').val()
				};
				
				$.post( wceb_object.ajax_url, data, function( response ) {
					wceb.session = response;
				});
			}

		}

		/**
		* Apply custom booking duration after settings one of the pickers
		*/
		wceb.applyBookingDuration = function( picker, pickerItem, selected ) {

			var alreadyDisabled = pickerItem.disable, // Get already disabled dates
				thingToCheck    = picker === 'end' ? pickerItem.max : pickerItem.min;

			// Get selected date on the other datepicker
			var selectedDate = new Date( selected.year, selected.month, selected.date );

			// Get last, current and next month duration (in days), relative to the current view
			var view                   = pickerItem.view, // Get current view on the current picker
				lastDayOfPreviousMonth = new Date( view.year, view.month, 0 ).getDate(), // Number of days of last month (relative to view)
				lastDayOfTheMonth      = new Date( view.year, view.month + 1, 0 ).getDate(), // Number of days of viewed month
				lastDayOfNextMonth     = new Date( view.year, view.month + 2, 0 ).getDate(); // Number of days of next month (relative to view)

			// Get the total of days to disable dates (3 months)
			var remainingDays = parseInt( lastDayOfPreviousMonth + lastDayOfTheMonth + lastDayOfNextMonth );

			// Difference between the selected day and the view (in days)
			var diff = Math.abs( Math.round( ( view.pick - selected.pick ) / 86400000 ) );

			// Number of days to start counting
			var diffMinus = picker === 'end' ? parseInt( diff - lastDayOfPreviousMonth ) : parseInt( diff - lastDayOfTheMonth - lastDayOfNextMonth );

			// Number of days to end counting
			var diffPlus = picker === 'end' ? parseInt( diff - lastDayOfPreviousMonth + remainingDays ) : parseInt( diff - lastDayOfNextMonth + remainingDays );
			
			if ( picker === 'end' && diff < lastDayOfPreviousMonth ) {
				var diffMinus = wceb.bookingCustomDuration;
				var diffPlus  = parseInt( diff + lastDayOfTheMonth + lastDayOfNextMonth );
			}

			if ( picker === 'start' && diffMinus < lastDayOfNextMonth ) {
				var diffMinus = wceb.bookingCustomDuration;
				var diffPlus  = parseInt( diff + lastDayOfTheMonth + lastDayOfPreviousMonth );
			}

			if ( diffMinus < 0 ) {
				diffMinus = 0;
			}

			if ( diffMinus < wceb.bookingCustomDuration ) {

				diffMinus = wceb.bookingCustomDuration;

			} else {

				var j;
				var multiples = [];

				// Get the closest multiple of the booking duration
				for ( j = 0; j <= diffMinus; j+= wceb.bookingCustomDuration ) {
					multiples.push( j );
				}

				var diffMinus = multiples.slice(-1)[0]; // Get last value

			}

			if ( wceb.calcMode === 'days' ) {
				diffMinus -= 1;
			}

			var i;
			var enabled = [1,2,3,4,5,6,7]; // Disable every day

			first = false;

			for ( i = diffMinus; i <= diffPlus; i+= wceb.bookingCustomDuration ) {

				var baseSelectedDate = new Date( selected.year, selected.month, selected.date ); // Selected date in the other picker

				if ( picker === 'start' ) {
					baseSelectedDate.setDate( selectedDate.getDate() - i ); // Remove booking duration
				} else if ( picker === 'end' ) {
					baseSelectedDate.setDate( selectedDate.getDate() + i ); // Add booking duration
				}

				dateToEnable = wceb.createDateObject( baseSelectedDate );

				// If the date is before the minimum set or after the maximum set, stop
				if ( ( picker === 'end' && dateToEnable.obj > thingToCheck.obj ) || ( picker === 'start' && dateToEnable.obj < thingToCheck.obj ) ) {
					break;
				}

				// Check if the date is disabled
				if ( typeof alreadyDisabled !== 'undefined' && alreadyDisabled.length > 0 ) {
					var d = wceb.checkIf.isDisabled( alreadyDisabled, dateToEnable );
				}

				// If it is disabled, don't enable it
				if ( true === d ) {
					continue;
				}
				
				enabled.push( [dateToEnable.year, dateToEnable.month, dateToEnable.date, 'inverted'] ); // add 'inverted' to enable date
			}

			pickerItem.disable = alreadyDisabled.concat( enabled ); // Merge arrays

			return false;
		}

		/**
		* Format price
		*/
		wceb.formatPrice = function( price ) {

			formatted_price = accounting.formatMoney( price, {
				symbol 		: wceb_object.currency_format_symbol,
				decimal 	: wceb_object.currency_format_decimal_sep,
				thousand	: wceb_object.currency_format_thousand_sep,
				precision 	: wceb_object.currency_format_num_decimals,
				format		: wceb_object.currency_format
			} );

			return formatted_price;

		}

		/**
		* Ajax request to calculate and return price, and store booking session
		*/
		wceb.setPrice = function() {

			product_id   = $('input[name=add-to-cart], button[name=add-to-cart]').val();
			variation_id = $('.variations_form').find('input[name=variation_id]').val();

			children = wceb.get.childrenIds();
			
			selectedDates = {};

			var format = $.fn.pickadate.defaults.format;

			// Start date
			selectedDates['startFormat'] = pickerStart.get('select', 'yyyy-mm-dd'), // yyyy-mm-dd
			selectedDates['start']       = pickerStart.get('select', format); // Nice format

			// End date
			selectedDates['endFormat'] = pickerEnd.get('select', 'yyyy-mm-dd'), // yyyy-mm-dd
			selectedDates['end']       = pickerEnd.get('select', format); // Nice format

			// WooCommerce Product Add-ons compatibility
			var additionalCost = wceb.get.additionalCosts( 'each' );

			var data = {
				action         : 'add_new_price',
				security       : $('input[name="_wceb_nonce"]').val(),
				product_id     : product_id,
				variation_id   : variation_id,
				children       : children,
				start          : selectedDates['start'],
				end            : selectedDates['end'],
				start_format   : selectedDates['startFormat'],
				end_format     : selectedDates['endFormat'],
				additional_cost: additionalCost
			};

			$('form.cart, form.bundle_form').fadeTo('400', '0.6').block({
				message: null,
				overlayCSS: {
					background: 'transparent',
					backgroundSize: '16px 16px',
					opacity: 0.6
				}
			});

			$.post( wceb_object.ajax_url, data, function( response ) {

				$('.woocommerce-error, .woocommerce-message').remove();
				fragments = response.fragments;
				errors    = response.errors;

				// If error, reset pickers
				if ( errors ) {

					$.each( errors, function( key, value ) {
						$( key ).replaceWith( value );
					});

					wceb.pickers.init();
					pickerStart.render();
					pickerEnd.render();

					// Unblock
					$('form.cart, form.bundle_form').stop(true).css('opacity', '1').unblock();

					return false;

				}

				if ( fragments ) {

					$.each( fragments, function( key, value ) {
						$( key ).replaceWith( value );
					});

					// Get quantity selected
					if ( $('.cart').find('input[name="quantity"]').size() ) {
						var qty_selected = parseFloat( $('.cart').find('input[name="quantity"]').val() );
					} else {
						var qty_selected = 1;
					}
					
					// Multiply booking price by quantity selected
					var new_price = wceb.formatPrice( fragments.booking_price * qty_selected );
					var price_html = '<span class="woocommerce-Price-amount amount">' + new_price + '</span>';

					// If the product is on sale
					if ( fragments.booking_regular_price !== '' ) {
						var new_regular_price = wceb.formatPrice( fragments.booking_regular_price * qty_selected );
						var price_html = '<del><span class="woocommerce-Price-amount amount">' + new_regular_price + '</span></del> <ins><span class="woocommerce-Price-amount amount">' + new_price + '</span></ins>';
						$('.booking_price').attr('data-booking_regular_price', fragments.booking_regular_price );
					}

					// Update price
					$('.booking_price').attr('data-booking_price', fragments.booking_price )
								       .find('.price').html( price_html );

					wceb.session = fragments.session;

				}

				$('body').trigger( 'update_price', [ data, response ] );

				// Unblock
				$('form.cart, form.bundle_form').stop(true).css('opacity', '1').unblock();
			
			});
		}

		/**
		* Set picker (one date only)
		*/
		wceb.picker.set = function() {
			
			product_id   = $('input[name=add-to-cart], button[name=add-to-cart]').val();
			variation_id = $('.variations_form').find('input[name=variation_id]').val();

			children = wceb.get.childrenIds();

			selectedDates['startFormat'] = pickerStart.get('select', 'yyyy-mm-dd'), // yyyy-mm-dd
			selectedDates['start']       = pickerStart.get('select', 'dd mmmm yyyy'); // Nice format

			var data = {
				action         : 'add_new_price',
				security       : $('input[name="_wceb_nonce"]').val(),
				product_id     : product_id,
				variation_id   : variation_id,
				children       : children,
				start          : selectedDates['start'],
				start_format   : selectedDates['startFormat'],
				additional_cost: wceb.get.additionalCosts( 'each' )
			};

			$('form.cart, form.bundle_form').fadeTo('400', '0.6').block({
				message: null,
				overlayCSS: {
					background: 'transparent',
					backgroundSize: '16px 16px',
					opacity: 0.6
				}
			});

			$.post( wceb_object.ajax_url, data, function( response ) {

				$('.woocommerce-error, .woocommerce-message').remove();
				fragments = response.fragments;
				errors    = response.errors;

				// If error, reset pickers
				if ( errors ) {

					$.each(errors, function(key, value) {
						$(key).replaceWith(value);
					});

					wceb.pickers.init();
					pickerStart.render();

					// Unblock
					$('form.cart, form.bundle_form').stop(true).css('opacity', '1').unblock();

					return false;

				}

				if ( fragments ) {

					$.each(fragments, function(key, value) {
						$(key).replaceWith(value);
					});

					if ( fragments.booking_price ) {

						// Get quantity selected
						if ( $('.cart').find('input[name="quantity"]').size() ) {
							var qty_selected = parseFloat( $('.cart').find('input[name="quantity"]').val() );
						} else {
							var qty_selected = 1;
						}

						// Multiply booking price by quantity selected
						var new_price = wceb.formatPrice( fragments.booking_price * qty_selected );
						var price_html = '<span class="woocommerce-Price-amount amount">' + new_price + '</span>';

						// If the product is on sale
						if ( fragments.booking_regular_price !== '' ) {
							var new_regular_price = wceb.formatPrice( fragments.booking_regular_price * qty_selected );
							var price_html = '<del><span class="woocommerce-Price-amount amount">' + new_regular_price + '</span></del> <ins><span class="woocommerce-Price-amount amount">' + new_price + '</span></ins>';
							$('.booking_price').attr('data-booking_regular_price', fragments.booking_regular_price );
						}

						// Update price
						$('.booking_price').attr('data-booking_price', fragments.booking_price )
									       .find('.price').html( price_html );

					}

					wceb.session = fragments.session;

				}

				$('body').trigger( 'update_price', [ data, response ] );

				// Unblock
				$('form.cart, form.bundle_form').stop(true).css('opacity', '1').unblock();
			
			});
		}

		/**
		* Open the second picker when selecting a date
		*/
		wceb.picker.close = function( picker, secondPicker ) {

			// Bug fix
			$( document.activeElement ).blur();

			if ( wceb.dateFormat === 'two' ) {

				var thisSet   = picker.get('select'),
					secondSet = secondPicker.get('select');

				// Open other picker if current picker is set and other not
				if ( wceb.checkIf.dateIsSet( thisSet ) && ! wceb.checkIf.dateIsSet( secondSet ) ) {
					setTimeout( function() { secondPicker.open(); }, 250 );
				}

			}

		}

		/**
		* Init or reset pickers
		*/
		wceb.pickers.init = function() {

			// Reset disabled dates
			pickerStartItem.disable = [];

			if ( wceb.dateFormat === 'two' ) {
				pickerEndItem.disable   = [];
			}

			var firstAvailableDates = wceb.get.firstAvailableDates();

			var firstDay = firstAvailableDates['start'];

			var minObject = firstDay,
				max       = wceb.createDateObject( new Date( wceb.maxYear, 11, 31 ) ),
				view      = wceb.createDateObject( new Date( minObject.year, minObject.month, 1 ) );

			pickerStartItem.clear     = null;
			pickerStartItem.select    = undefined;
			pickerStartItem.min       = minObject;
			pickerStartItem.max       = max;
			pickerStartItem.highlight = minObject;
			pickerStartItem.view      = view;

			pickerStart.$node.val('');

			if ( wceb.dateFormat === 'two' ) {

				var endFirstDay = firstAvailableDates['end'];

				var endMinObject = endFirstDay,
					endView      = wceb.createDateObject( new Date( endMinObject.year, endMinObject.month, 1 ) );
			
				pickerEndItem.clear     = null;
				pickerEndItem.select    = undefined;
				pickerEndItem.min       = endMinObject;
				pickerEndItem.max       = max;
				pickerEndItem.highlight = endMinObject;
				pickerEndItem.view      = endView;

				pickerEnd.$node.val('');

			}

			$('.single_add_to_cart_button').prop( 'disabled', true );

			return false;

		}

		/**
		* Renders pickers and triggers event
		*/
		wceb.pickers.render = function( ids ) {

			var $body = $('body');

			$body.trigger( 'pickers_init', ids );

			pickerStart.render();
			pickerEnd.render();
			
			$body.trigger( 'after_pickers_init', ids );

		}

		/**
		* Clear the other picker
		*/
		wceb.pickers.clearSecond = function( picker, secondPicker, secondPickerObject ) {

			var secondPickerItem = secondPickerObject.component.item,
				firstAvailableDates = wceb.get.firstAvailableDates(),
				min  = firstAvailableDates[secondPicker],
				max  = wceb.createDateObject( wceb.maxOption ),
				view = wceb.createDateObject( new Date( min.year,min.month,01 ) );

			secondPickerItem.disable = [];
			secondPickerItem.min     = min;
			secondPickerItem.max     = max;

			if ( secondPickerObject.get('select') === null ) {
				secondPickerItem.highlight = min;
				secondPickerItem.view      = view;
			}

			$('body').trigger( 'clear_' + picker + '_picker', secondPickerItem );

			secondPickerObject.render();

		}

		/**
		* Set the other picker and call Ajax function if both pickers are set
		*/
		wceb.pickers.set = function( picker, pickerObject, secondPickerObject, secondPickerItem ) {

			var selectedObject       = pickerObject.get('select'), // Array [year,month,date,day,obj,pick]
				secondSelectedObject = secondPickerObject.get('select'); // Array [year,month,date,day,obj,pick]

			if ( selectedObject === null ) {
				return;
			}

			var direction = picker === 'start' ? 'superior' : 'inferior',
				calc      = picker === 'start' ? 'plus' : 'minus';

			var selectedTimestamp = selectedObject.pick; // Unix timestamp

			var minAndMax = wceb.get.minAndMax( selectedObject, calc ),
				min       = minAndMax.min,
				max       = minAndMax.max;

			var thingToSet = picker === 'start' ? max : min;

			// If no maximum date is set, set max to maximum year
			if ( ! max ) {
				max = wceb.maxOption;
			}

			// Get the closest disabled date
			var closestDisabled = wceb.get.closestDisabled( selectedTimestamp, secondPickerObject, direction );

			// If a date is disabled
			if ( closestDisabled ) {

				var date = new Date( closestDisabled ); // Convert to date

				if ( ( picker === 'start' && closestDisabled < thingToSet ) || ( picker === 'end' && closestDisabled > thingToSet ) ) {
					var thingToSet = date;
				}
			}

			var min  = picker === 'start' ? wceb.createDateObject( min ) : wceb.createDateObject( thingToSet ),
				max  = picker === 'start' ? wceb.createDateObject( thingToSet ) : wceb.createDateObject( max ),
				view = wceb.createDateObject( new Date( min.year, min.month, 01 ) );

			secondPickerItem.min  = min;
			secondPickerItem.max  = max;
			secondPickerItem.view = view;

			// If other picker is not set
			if ( typeof secondSelectedObject === 'undefined' || secondSelectedObject === null ) {
				secondPickerItem.highlight = min;
			}

			$('body').trigger('set_' + picker + '_picker', [secondPickerItem, selectedTimestamp] );

			secondPickerObject.render();

			// If both pickers are set
			if ( wceb.checkIf.datesAreSet() ) {
				wceb.setPrice(); // Ajax request to calculate price and store session data
			}

		}

		pickerStart.on({
			render: function() {

				// Display picker title
				pickerStart.$root.find('.picker__header').prepend('<div class="picker__title">' + wceb_object.start_text + '</div>');

			},
			set: function( startTime ) {

				// If picker is cleared
				if ( typeof startTime.clear !== 'undefined' && startTime.clear === null ) {

					if ( wceb.dateFormat === 'two' ) {
						// Reset min, max and disabled dates on other picker
						wceb.pickers.clearSecond( 'start', 'end', pickerEnd );

					}

					selectedDates['start']       = null;
					selectedDates['startFormat'] = null;

					// Clear session
					wceb.clearBookingSession();

					$('.single_add_to_cart_button').prop( 'disabled', true );

				}

				// If picker is set
				if ( wceb.dateFormat === 'two' && wceb.checkIf.dateIsSet( startTime.select ) ) {
					wceb.pickers.set( 'start', pickerStart, pickerEnd, pickerEndItem );
				} else if ( wceb.dateFormat === 'one' && wceb.checkIf.dateIsSet( startTime.select ) ) {
					wceb.picker.set();
				}
				
			},
			close: function() {
				wceb.picker.close( pickerStart, pickerEnd );
			}
		});

		pickerEnd.on({
			render: function() {

				// Display picker title
				pickerEnd.$root.find('.picker__header').prepend('<div class="picker__title">' + wceb_object.end_text + '</div>');
				
			},
			set: function( endTime ) {

				// If picker is cleared
				if ( typeof endTime.clear !== 'undefined' && endTime.clear === null ) {

					// Reset min, max and disabled dates on other picker
					wceb.pickers.clearSecond( 'end', 'start', pickerStart );

					selectedDates['end']       = null;
					selectedDates['endFormat'] = null;

					// Clear session
					wceb.clearBookingSession();

					$('.single_add_to_cart_button').prop( 'disabled', true );

				}

				// If picker is set
				if ( wceb.dateFormat === 'two' && wceb.checkIf.dateIsSet( endTime.select ) ) {
					wceb.pickers.set( 'end', pickerEnd, pickerStart, pickerStartItem );
				}

				return false;
				
			},
			close: function() {
				wceb.picker.close( pickerEnd, pickerStart );
			}
		});

		$('body').on('pickers_init', function( e, variation ) {

			var firstAvailableDates = wceb.get.firstAvailableDates();

			var first = firstAvailableDates['start'];

			pickerStartItem.view = wceb.createDateObject( new Date( first.year,first.month,01 ) ); // First day of the first available date month
			pickerStartItem.highlight = first; // First available date

			if ( wceb.dateFormat === 'two' ) {

				var endFirst = firstAvailableDates['end'];

				pickerEndItem.view = wceb.createDateObject( new Date( endFirst.year,endFirst.month,01 ) ); // First day of the first available date month
				pickerEndItem.highlight = endFirst; // First available date
				pickerEndItem.min = endFirst; // First available date

			}

			return false;
		});

		/**
		* Before rendering the start picker
		*/
		pickerStart.on( 'before_rendering', function() {

			if ( wceb.dateFormat === 'two' ) {

				var selected = pickerEnd.get('select'); // Get selected date on the End picker

				startPickerDisabled = pickerStartItem.disable; // Store already disabled dates

				if ( wceb.checkIf.dateIsSet( selected ) && wceb.bookingDuration !== 'days' && wceb.bookingCustomDuration > 1  ) {
					wceb.applyBookingDuration( 'start', pickerStartItem, selected );
				}

			}

		});

		/**
		* After rendering the start picker
		*/
		pickerStart.on( 'after_rendering', function() {

			if ( wceb.dateFormat === 'two' ) {
				pickerStartItem.disable = startPickerDisabled; // Reset disabled dates
			}

		});

		/**
		* Before rendering the end picker
		*/
		pickerEnd.on( 'before_rendering', function() {

			var selected = pickerStart.get('select'); // Get selected date on the Start picker

			endPickerDisabled = pickerEndItem.disable; // Store already disabled dates

			if ( wceb.checkIf.dateIsSet( selected ) && wceb.bookingDuration !== 'days' && wceb.bookingCustomDuration > 1 ) {
				wceb.applyBookingDuration( 'end', pickerEndItem, selected );
			}

		});

		/**
		* After rendering the end picker
		*/
		pickerEnd.on( 'after_rendering', function() {
			pickerEndItem.disable = endPickerDisabled; // Reset disabled dates
		});

		/**
		* Update booking price when changing product quantity
		*/
		$('.cart').on('change', 'input[name="quantity"]', function() {
			formatted_price = wceb.formatPrice( wceb.get.basePrice() );
			$('.booking_price').find('.price .amount').html( formatted_price );

			formatted_regular_price = wceb.formatPrice( wceb.get.regularPrice() );
			$('.booking_price').find('.price del .amount').html( formatted_regular_price );
		});

		$('body').on( 'update_price', function() {
			$('.single_add_to_cart_button').prop( 'disabled', false );
		});

		/**
		* WooCommerce Product Add-ons compatibility
		*/
		$('.product-addon input, .product-addon textarea, .product-addon select, .product-addon input.qty').on( 'woocommerce-product-addons-update', function() {

			if ( wceb.dateFormat === 'two' && wceb.checkIf.datesAreSet() ) {
				wceb.setPrice();
			} else if ( wceb.dateFormat === 'one' && wceb.checkIf.dateIsSet( 'start' ) ) {
				wceb.picker.set();
			} else {
				var formatted_total = wceb.formatPrice( wceb.get.basePrice() );
				$('.booking_price').find('.price .amount').html( formatted_total );

				formatted_regular_price = wceb.formatPrice( wceb.get.regularPrice() );
				$('.booking_price').find('.price del .amount').html( formatted_regular_price );
			}
			
		});

	});

}(jQuery));