<?php
/**
 * Shortcode [mailchimp_form] view.
 * For more detailed list see list of shortcode attributes.
 *
 * @var string $mailchimp_list_id
 * @var string $title
 * @var string $button_text
 * @var string $css_class
 * @var string $width_mode
 * @var string $bg_url
 * @var string $bg_repeat
 * @var string $view
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

if ( empty( $mailchimp_list_id ) ) {
	printf( '<div class="form-subscribe"><div class="form-subscribe__shadow"></div>%s</div>',
		esc_html__( 'Please enter the MailChimp List ID settings in the MailChimp Form [mailchimp_form] shortcode.', 'adventure-tours' )
	);
	return;
}

$form_id = adventure_tours_di( 'shortcodes_helper' )->generate_id();
$form_id_full = 'adventure-tours-mainchimp-form-' . $form_id;
TdJsClientScript::addScript( 'initMailChimpCustomValidtion' . $form_id, 'Theme.FormValidationHelper.initMailChimpCustomValidtion("' .  $form_id_full . '")' );

if ( $bg_url ) {
	wp_enqueue_script( 'parallax' );
	TdJsClientScript::addScript( 'initParallax', 'Theme.initParallax();' );
}

$form_mode_class = ( 'full-width' == $width_mode ) ? ' form-subscribe--full-width' : '';

?>
<div class="form-subscribe parallax-section <?php echo esc_attr( $css_class . $form_mode_class ); ?>">
<?php if ( $bg_url ) { ?>
	<div class="parallax-image" style="background-image:url(<?php echo esc_url( $bg_url ); ?>); background-repeat:<?php echo esc_attr( $bg_repeat ); ?>;"></div>
<?php } ?>
	<div class="form-subscribe__shadow"></div>
<?php
	if ( $title ) {
		printf( '<div class="form-subscribe__title">%s</div>', esc_html( $title ) );
	}
	if ( $content ) { 
		printf( '<div class="form-subscribe__description">%s</div>', adventure_tours_esc_text( $content ) );
	} 
	printf( '<div id="%s">%s</div>',
		esc_attr( $form_id_full ),
		do_shortcode( '[yks-mailchimp-list id="' . $mailchimp_list_id . '" submit_text="' . $button_text . '"] ' )
	);
?>
</div>
