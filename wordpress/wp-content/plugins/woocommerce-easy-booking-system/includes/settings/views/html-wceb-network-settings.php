<div class="wrap">

	<div id="wceb-settings">

		<h2><?php _e('Network settings for WooCommerce Easy Booking', 'easy_booking'); ?></h2>
		<form method="post" action="<?php echo admin_url(); ?>options.php">

			<?php settings_fields('easy_booking_global_settings'); ?>
			<?php do_settings_sections('easy_booking_global_settings'); ?>
			 
			<?php submit_button(); ?>

		</form>

	</div>

</div>