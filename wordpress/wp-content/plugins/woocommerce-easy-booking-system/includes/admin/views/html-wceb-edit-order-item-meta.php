<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

?>

<div class="edit" style="display: none;">

    <table class="meta" cellspacing="0">

        <tbody class="meta_items">

            <tr data-meta_id="<?php echo esc_attr( $start_meta_id ); ?>">

                <td>

                    <label for="start_date" style="font-weight: bold;"><?php echo esc_html( $start_date_text ); ?>: </label>
                    <input type="hidden" name="meta_key[<?php echo esc_attr( $item_id ); ?>][<?php echo esc_attr( $start_meta_id ); ?>]" value="_ebs_start_format">
                    <input type="text"  name="meta_value[<?php echo esc_attr( $item_id ); ?>][<?php echo esc_attr( $start_meta_id ); ?>]" id="start_date" class="datepicker datepicker_start--<?php echo absint( $item_id ); ?>" value="<?php echo esc_attr( $start_date_set ); ?>" data-value="<?php echo esc_attr( $start_date_set ); ?>">

                </td>

            </tr>

            <tr data-meta_id="<?php echo esc_attr( $start_display_meta_id ); ?>">

                <input type="hidden" name="meta_key[<?php echo esc_attr( $item_id ); ?>][<?php echo esc_attr( $start_display_meta_id ); ?>]" value="_ebs_start_display">
                <input type="hidden" class="start_display" name="meta_value[<?php echo esc_attr( $item_id ); ?>][<?php echo esc_attr( $start_display_meta_id ); ?>]" value="<?php echo esc_attr( $start_date ); ?>">

            </tr>

            <?php if ( ! empty( $end_date_set ) ) : ?>

                <tr data-meta_id="<?php echo esc_attr( $end_meta_id ); ?>">

                    <td>

                        <label for="end_date" style="font-weight: bold;"><?php echo esc_html( $end_date_text ); ?>: </label>
                        <input type="hidden" name="meta_key[<?php echo esc_attr( $item_id ); ?>][<?php echo esc_attr( $end_meta_id ); ?>]" value="_ebs_end_format">
                        <input type="text" name="meta_value[<?php echo esc_attr( $item_id ); ?>][<?php echo esc_attr( $end_meta_id ); ?>]" id="end_date" class="datepicker datepicker_end--<?php echo absint( $item_id ); ?>" value="<?php echo esc_attr( $end_date_set ); ?>" data-value="<?php echo esc_attr( $end_date_set ); ?>">
                        
                    </td>

                </tr>

                <tr data-meta_id="<?php echo esc_attr( $end_display_meta_id ); ?>">

                    <input type="hidden" name="meta_key[<?php echo esc_attr( $item_id ); ?>][<?php echo esc_attr( $end_display_meta_id ); ?>]" value="_ebs_end_display">
                    <input type="hidden" class="end_display" name="meta_value[<?php echo esc_attr( $item_id ); ?>][<?php echo esc_attr( $end_display_meta_id ); ?>]" value="<?php echo esc_attr( $end_date ); ?>">

                </tr>

            <?php endif; ?>

            <input type="hidden" class="variation_id" name="variation_id" data-item_id="<?php echo absint( $item_id ); ?>" data-product_id="<?php echo absint( $product_id ); ?>" value="">

        </tbody>

    </table>

</div>