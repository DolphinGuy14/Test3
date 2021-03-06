<?php
/**
 * View for rendering tour booking periods management tab.
 *
 * @var assoc $periods set of configurated periods.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

if ( ! isset( $periods ) ) {
	$periods = array();
}

$row_template = 'templates/admin/tour-booking-row';
?>
<div id="tour_booking_tab" class="panel woocommerce_options_panel">
	<div class="table_grid">
		<table class="widefat">
			<thead style="2px solid #eee;">
				<tr>
					<th class="sort" width="1%">&nbsp;</th>
					<th><?php esc_html_e( 'Dates', 'adventure-tours' ); ?></th>
					<th><?php esc_html_e( 'Details', 'adventure-tours' ); ?>&nbsp;<a class="tips" data-tip="<?php esc_attr_e( 'Number of tickets avaialable for a tour each day.', 'adventure-tours' ); ?>">[?]</a></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th colspan="6">
						<a href="#" class="button preview_btn" data-togletext="<?php esc_attr_e( 'Hide Calendar', 'adventure-tours' ); ?>"><?php esc_html_e( 'Preview Calendar' ,'adventure-tours' ); ?></a>
						<a href="#" class="button button-primary add_row_btn" data-row="<?php
							echo esc_attr( adventure_tours_render_template_part($row_template,'', array(
								'row' => array(),
								'rowIndex' => 0,
							), true) );
						?>"><?php esc_html_e( 'Add Period', 'adventure-tours' ); ?></a>
						<a href="#" class="button save_ranges_btn"><?php esc_html_e( 'Save' ,'adventure-tours' ); ?></a>
						<?php if ( isset( $nonce_field['name'] ) && isset( $nonce_field['value'] ) ) {
							wp_nonce_field( $nonce_field['value'], $nonce_field['name'] );
						} ?>
						<input type="hidden" name="booking_tour_id" value="<?php the_ID(); ?>" />
						<span class="description"><?php esc_html_e( 'Please add ranges with information about tour dates.', 'adventure-tours' ); ?></span>
					</th>
				</tr>
			</tfoot>
			<tbody id="tour_booking_rows_cont">
			<?php if ( $periods && is_array( $periods ) ) {
				foreach ( $periods as $curIndex => $row ) {
					adventure_tours_render_template_part( $row_template,'', array(
						'row' => $row,
						'rowIndex' => $curIndex,
					) );
				}
			} ?>
			</tbody>
		</table>
	</div>
</div>
