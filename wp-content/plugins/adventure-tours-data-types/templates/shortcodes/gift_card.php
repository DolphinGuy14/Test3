<?php
/**
 * Shortcode [gift_card] view.
 * For more detailed list see list of shortcode attributes.
 *
 * @var string $title
 * @var string $content
 * @var string $button_title
 * @var srting $button_link
 * @var string $view
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

$is_button = $button_title && $button_link ? true : false;
?>
<div class="at-gift-cart<?php if ( $is_button ) { echo ' at-gift-cart--button'; }; ?>">
	<div class="at-gift-cart__box">
		<div class="at-gift-cart__bow"></div>
<?php
	if ( $title ) {
		printf( '<h3 class="at-gift-cart__title">%s</h3>', esc_html( $title ) );
	}
	if ( $content ) {
		printf( '<div class="at-gift-cart__description">%s</div>', esc_html( $content ) );
	}
	if ( $is_button ) {
		printf('<a href="%s" class="at-gift-cart__button">%s</a>',
			esc_url( $button_link ),
			esc_html( $button_title )
		);
	}
?>
	</div>
</div>
