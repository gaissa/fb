<div class="wrap">

	<div id="wceb-settings-container">

		<?php $wceb_tabs = apply_filters( 'easy_booking_reports_tabs', array(
			'bookings' => __('Bookings', 'easy_booking')
		));

		$current_tab = empty( $_GET['tab'] ) ? 'bookings' : sanitize_title( $_GET['tab'] ); ?>

		<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
			<?php foreach ( $wceb_tabs as $tab => $label ) { ?>
				<a href="<?php echo admin_url( 'admin.php?page=easy-booking-reports&tab=' . $tab ); ?>" class="nav-tab <?php echo ( $current_tab == $tab ? 'nav-tab-active' : '' ) ?>"><?php echo $label; ?></a>
			<?php } ?>
		</h2>

		<?php do_action( 'easy_booking_reports_' . $current_tab ); ?>

	</div>
</div>
