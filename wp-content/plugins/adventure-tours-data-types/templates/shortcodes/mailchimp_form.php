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
	printf( '<div class="at-form-subscribe">%s</div>',
		esc_html__( 'Please enter the MailChimp List ID settings in the MailChimp Form [mailchimp_form] shortcode.', 'adventure-tours-data-types' )
	);
	return;
}

?>
<div class="at-form-subscribe parallax-section <?php echo esc_attr( $css_class ); ?>">
<?php
	if ( $title ) {
		printf( '<div class="at-form-subscribe__title">%s</div>', esc_html( $title ) );
	}
	if ( $content ) { 
		printf( '<div class="at-form-subscribe__description">%s</div>', esc_html( $content ) );
	} 
	printf( '<div>%s</div>',
		do_shortcode( '[yks-mailchimp-list id="' . $mailchimp_list_id . '" submit_text="' . $button_text . '"] ' )
	);
?>
</div>