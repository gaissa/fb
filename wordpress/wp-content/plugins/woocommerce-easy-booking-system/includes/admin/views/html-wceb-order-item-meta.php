<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

?>

<div class="view">

	<table cellspacing="0" class="display_meta">
		
		<tbody>

			<tr>
			    <th><?php echo esc_html( $start_date_text ); ?>:</th>
			    <td><p><?php echo esc_html( $start_date ); ?></p></td>
			</tr>
			
			<?php if ( ! empty( $end_date_set ) ) : ?>

				<tr>
				    <th><?php echo esc_html( $end_date_text ); ?>: </th>
				    <td><p><?php echo esc_html( $end_date ); ?></p></td>
				</tr>

			<?php endif; ?>

		</tbody>

	</table>

</div>