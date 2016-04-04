<?php
/**
 * Tour booking form view.
 *
 * @var WC_Product_Tour $current_product optinal parameter, if missed will try to use global product.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

if ( ! isset( $current_product ) || ! $current_product ) {
	$current_product = ! empty( $GLOBALS['product'] ) ? $GLOBALS['product'] : '';
	if ( is_string( $current_product ) && $current_product ) {
		$current_product = wc_get_product( false, $current_product );
	}
}

if ( ! $current_product || ! AtTourHelper::isTourProduct( $current_product ) ) {
	return;
}

// Refactoring is required: to improve validation and childs field processing.
$booking_range = adventure_tours_get_tour_booking_range( $current_product->id );
$booking_dates = adventure_tours_di( 'tour_booking_service' )->get_expanded( $current_product->id, $booking_range['start'], $booking_range['end'] );
// As there is no bookable dates - we are hiding booking form at all.
if ( ! $booking_dates ) {
	return;
}
$booking_form = adventure_tours_di( 'booking_form' );

wp_enqueue_script( 'jquery-ui-datepicker' );
wp_enqueue_style( 'jquery-ui-datepicker-custom' );
TdJsClientScript::addScript( 'initTourBookingForm', 'Theme.tourBookingForm.init(' . wp_json_encode( array(
	'availableDates' => $booking_dates,
	'dateFormat' => $booking_form->get_date_format( 'datepicker' ),
) ) . ');' );

$current_tour_id = $current_product->id;

$form_data = apply_filters('adventure_tours_load_booking_form_data', array(
	'fields' => array(
		'name' => '',
		'email' => '',
		'phone' => '',
		'children' => 0,
		'quantity' => isset( $_REQUEST['quantity'] ) ? $_REQUEST['quantity'] : 1,
		'date' => date(
			$booking_form->get_date_format(),
			strtotime( key( $booking_dates ) ) // Getting 1-st bookable date.
		),
	),
	'errors' => array(),
), $current_tour_id);

$rendererer = new AtFormRendererHelper( array(
	'field_vals' => $form_data['fields'],
	'field_errors' => $form_data['errors'],
	'field_placeholders' => array(
		'name' => __( 'Name', 'adventure-tours' ),
		'email' => __( 'Email address', 'adventure-tours' ),
		'phone' => __( 'Phone number', 'adventure-tours' ),
		'date' => __( 'Date', 'adventure-tours' ),
	),
));

if ( $rendererer->field_errors ) {
	$rendererer->init_js_errors( '#tourBookingForm input[title]' );
}
?>
<div class="form-block form-block--style3 form-block--tour-booking block-after-indent">
	<?php printf('<h3 class="form-block__title">%s</h3>',
		esc_html( apply_filters( 'adventure_tours_booking_form_title', __( 'Book the tour', 'adventure-tours'), $current_product ) )
	); ?>
	<form id="tourBookingForm">
		<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $current_tour_id ); ?>">
		<div class="form-block__item form-block__field-width-icon">
			<input type="text" name="order_data[name]" <?php $rendererer->render_field_attributes( 'name' ); ?>>
			<i class="td-user"></i>
		</div>
		<div class="form-block__item form-block__field-width-icon">
			<input type="text" name="order_data[email]" <?php $rendererer->render_field_attributes( 'email' ); ?>>
			<i class="td-email-2"></i>
		</div>
		<div class="form-block__item form-block__field-width-icon">
			<input type="text" name="order_data[phone]" <?php $rendererer->render_field_attributes( 'phone' ); ?>>
			<i class="td-phone-1"></i>
		</div>
		<div class="form-block__fields-short">
			<div class="form-block__item form-block__field-short form-block__field-short--wide form-block__field-width-icon">
				<input type="text" name="tour_data[date]" <?php $rendererer->render_field_attributes( 'date' ); ?>>
				<i class="td-calendar"></i>
			</div>
			<div class="form-block__item form-block__field-short">
				<input type="number" name="quantity" <?php $rendererer->render_field_attributes( 'quantity' ); ?>>
			</div>
		</div>
		<?php printf('<input class="form-block__button" type="submit" value="%s">',
			esc_attr( apply_filters( 'adventure_tours_booking_form_btn_text', __( 'Book now', 'adventure-tours'), $current_product ) )
		); ?>
	</form>
</div>
