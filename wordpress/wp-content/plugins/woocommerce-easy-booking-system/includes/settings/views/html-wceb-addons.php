<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
} ?>

<div class="wrap">
	<h2><?php _e('WooCommerce Easy Booking Add-ons'); ?></h2>
	<div class="addons-container row">
		<?php $addons = array(
			array(
				'name' => 'Easy Booking: Availability Check',
				'slug' => 'availability-check',
				'desc' => '<p>'
					. __( 'Manage availabilities of your bookable products.', 'easy_booking' ) .
				'</p>'
			),
			array(
				'name' => 'Easy Booking: Duration Discounts',
				'slug' => 'duration-discounts',
				'desc' => '<p>
					' .  __( 'Set discounts or surcharges to your products depending on the duration booked by your clients.', 'easy_booking' ) . '
				</p>'
			)
			,
			array(
				'name' => 'Easy Booking: Disable Dates',
				'slug' => 'disable-dates',
				'desc' => '<p>
					' .  __( 'Disable days or dates on your products booking schedules.', 'easy_booking' ) . '
				</p>'
			)
			,
			array(
				'name' => 'Easy Booking: Pricing',
				'slug' => 'pricing',
				'desc' => '<p>
					' .  __( 'Set different prices depending on a day, date or daterange.', 'easy_booking' ) . '
				</p>'
			)
		);

		$plugin_dir = plugins_url( '/', WCEB_PLUGIN_FILE );

		$active_plugins = (array) get_option( 'active_plugins', array() );

        if ( is_multisite() ) {
            $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
        }

		foreach ( $addons as $addon ) : ?>
			<div class="addon-single">
				<div class="addon-single__img">
					<img src="<?php echo $plugin_dir . 'assets/img/addons/' . $addon['slug'] . '.png'; ?>" alt="<?php echo $addon['slug']; ?>">
				</div>
				<div class="addon-single__desc">
					<h2><?php echo $addon['name']; ?></h2>
					<?php echo $addon['desc']; ?>
					<p>
						<?php if ( ! ( array_key_exists( 'easy-booking-' . $addon['slug'] .'/' . 'easy-booking-' . $addon['slug'] . '.php', $active_plugins ) || in_array( 'easy-booking-' . $addon['slug'] .'/' . 'easy-booking-' . $addon['slug'] . '.php', $active_plugins ) ) ) { ?>
						<a href="http://herownsweetcode.com/easy-booking/plugin/<?php echo $addon['slug']; ?>" target="_blank" class="button">
							<?php _e('Learn more', 'easy_booking'); ?>
						</a>
						<?php } else { ?>
						<a href="#" class="button easy-booking-button easy-booking-button--installed">
							<?php _e('Installed', 'easy_booking'); ?>
						</a>
						<a href="http://herownsweetcode.com/easy-booking/documentation/<?php echo $addon['slug']; ?>" target="_blank" class="button">
							<?php _e('Documentation', 'easy_booking'); ?>
						</a>
						<a href="http://herownsweetcode.com/easy-booking/support/<?php echo $addon['slug']; ?>" target="_blank" class="button">
							<?php _e('Support', 'easy_booking'); ?>
						</a>
						<?php } ?>
					</p>
				</div>
			</div>

		<?php endforeach; ?>
	</div>
</div>