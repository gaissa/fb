<div class="wrap">

	<?php $wceb_settings_tabs = apply_filters( 'easy_booking_settings_tabs', array(
		'general'    => __('General ', 'easy_booking'),
		'appearance' => __('Appearance', 'easy_booking')
	));

	$current_tab = empty( $_GET['tab'] ) ? 'general' : sanitize_title( $_GET['tab'] ); ?>

	<form method="post" action="options.php">

		<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
			<?php foreach ( $wceb_settings_tabs as $tab => $label ) { ?>
				<a href="<?php echo admin_url( 'admin.php?page=easy-booking&tab=' . $tab ); ?>" class="nav-tab <?php echo ( $current_tab == $tab ? 'nav-tab-active' : '' ) ?>"><?php echo $label; ?></a>
			<?php } ?>
		</h2>
			 
		<?php do_action( 'easy_booking_settings_' . $current_tab . '_tab' ); ?>
		<?php submit_button(); ?>

	</form>

</div>