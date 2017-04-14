(function($) {

	$(document).ready(function() {

		$.extend( $.fn.pickadate.defaults, {
			hiddenName  : true,
			selectYears : true,
  			selectMonths: true
		});

		var format = $.fn.pickadate.defaults.format;

		var item_picker = $('#woocommerce-order-items').on( 'click', 'a.edit-order-item', function() {

			var line            = $(this).parents( 'tr' );
			var datepickerInput = line.find( '.datepicker' );
			var id              = line.find( '.variation_id' );

			var $input = datepickerInput.pickadate();

			if ( datepickerInput.length && id.length ) {

				setStartOnLoad = false;

				var item_id     = id.data( 'item_id' );
				var $inputStart = $( '.datepicker_start--' + item_id ).pickadate();
				var pickerStart = $inputStart.pickadate( 'picker' );
				var setStart    = $( '.datepicker_start--' + item_id ).data( 'value' );
				var startHidden = line.find( '.start_display' );

				if ( $( '.datepicker_end--' + item_id ).length > 0 ) {
					dateFormat = 'two';
				} else {
					dateFormat = 'one';
				}

				if ( dateFormat === 'two' ) {
					setEndOnLoad  = false;
					var $inputEnd = $( '.datepicker_end--' + item_id ).pickadate();
					var pickerEnd = $inputEnd.pickadate( 'picker' );
					var setEnd    = $( '.datepicker_end--' + item_id ).data( 'value' );
					var endHidden = line.find( '.end_display' );
				}

				pickerStart.on({
					set: function(startTime) {

						if ( typeof startTime.clear != 'undefined' && startTime.clear == null ) {

							if ( dateFormat === 'two' ) {
								pickerEnd.set( 'min', false, { muted: true } );
							}

							startHidden.val('');

						} else if ( startTime.select && typeof startTime.select != 'undefined' ) {

							startFormat = pickerStart.get( 'select', format );
							startHidden.val( startFormat );

							if ( dateFormat === 'two' ) {

								startPickerData = pickerStart.get( 'select' );

								if ( order_ajax_info.calc_mode === 'days' ) {

									pickerEnd.set(
										'min',
										[startPickerData.year, startPickerData.month, startPickerData.date],
										{ muted: true }
									);

								} else {

									pickerEnd.set(
										'min',
										[startPickerData.year, startPickerData.month, startPickerData.date + 1],
										{ muted: true }
									);

								}

							}

							if ( setStart == '' ) {
								setStartOnLoad = true;
							}

						}
						
					}

				});

				if ( dateFormat === 'two' ) {

					pickerEnd.on({
						set: function( endTime ) {

							if ( typeof endTime.clear != 'undefined' && endTime.clear == null ) {

								pickerStart.set( 'max', false, { muted: true } );
								endHidden.val('');

							} else if ( endTime.select && typeof endTime.select != 'undefined' ) {

								endFormat = pickerEnd.get( 'select', format );
								endHidden.val( endFormat );

								endPickerData = pickerEnd.get( 'select' );

								if ( order_ajax_info.calc_mode === 'days' ) {

									pickerStart.set(
										'max',
										[endPickerData.year, endPickerData.month, endPickerData.date],
										{ muted: true }
									);

								} else {

									pickerStart.set(
										'max',
										[endPickerData.year, endPickerData.month, endPickerData.date - 1],
										{ muted: true }
									);

								}

								if ( setEnd == '' ) {
									setEndOnLoad = true;
								}

							}
							
						}
					});

				}

				if ( setStart != '' ) {
					pickerStart.set( 'select', setStart, { format: 'yyyy-mm-dd' } );
				}

				if ( dateFormat === 'two' && setEnd != '' ) {
					pickerEnd.set( 'select', setEnd, { format: 'yyyy-mm-dd' } );
				}

			}

		});

	});

})(jQuery);