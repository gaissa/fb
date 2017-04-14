<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="updated easy-booking-notice">
	<p>
		<?php _e( 'Want more features for WooCommerce Easy Booking?', 'easy_booking' ); ?>
		<a href="admin.php?page=easy-booking-addons"><?php _e( ' Check the add-ons!', 'easy_booking' ); ?></a>
	</p>
	<button type="button" class="notice-dismiss easy-booking-notice-close" data-notice="wceb-addons"></button>
</div>