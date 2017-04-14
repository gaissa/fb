<div class="wceb-reports tablenav <?php echo esc_attr( $which ); ?>">

	<div class="alignleft actions bulkactions">

		<form id="bookings-filter" method="get">

			<input type="hidden" name="page" value="easy-booking-reports">

			<div class="reports-filter filter-id">

				<?php if ( version_compare( WC_VERSION, '2.7', '<' ) ) { ?>

					<input type="hidden" id="reports_search" name="wceb_report_product_ids" value='<?php echo absint( $filter_id ) ? absint( $filter_id ) : ''; ?>' data-selected='<?php echo esc_attr( $product ); ?>' class="wc-product-search" style="width: 100%;" data-action="wceb_reports_product_id" data-placeholder="<?php _e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-allow_clear="true" data-multiple="false" />
					
				<?php } else { ?>

					<select class="wc-product-search"  style="width:203px;" id="reports_search" name="wceb_report_product_ids" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="wceb_reports_product_id" data-allow_clear="true">
						
					<?php

					$product = wc_get_product( $filter_id );
					if ( is_object( $product ) ) {
						echo '<option value="' . esc_attr( $filter_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
					}

					?>

					</select>

				<?php } ?>

			</div>

			<div class="reports-filter filter-date">
				<input type="text" name="wceb_report_start_date" data-value="<?php echo esc_attr( $filter_start_date ); ?>" class="datepicker" placeholder="<?php echo isset( $start_date_text ) ? esc_attr( $start_date_text ) : __( 'Start', 'easy_booking' ); ?>">
			</div>

			<div class="reports-filter filter-date">
				<input type="text" name="wceb_report_end_date" data-value="<?php echo esc_attr( $filter_end_date ); ?>" class="datepicker" placeholder="<?php echo isset( $end_date_text ) ? esc_attr( $end_date_text ) : __( 'End', 'easy_booking' ); ?>">
			</div>
			
			<div class="reports-filter-submit">
				<input type="submit" id="post-query-submit" class="button" value="Filtrer">
			</div>

			<br class="clear">

		</form>

		<?php $this->bulk_actions( $which ); ?>

	</div>

	<?php
		$this->extra_tablenav( $which );
		$this->pagination( $which );
	?>

	<br class="clear">
</div>