<?php
/**
 * Shortcode [title] view.
 * For more detailed list see list of shortcode attributes.
 *
 * @var string  $text
 * @var string  $subtitle
 * @var string  $size
 * @var string  $position
 * @var boolean $decoration
 * @var boolean $underline
 * @var string  $style
 * @var string  $view
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */
$size_class = ( 'big' == $size ) ? ' at-title--big' : '';
$position_class = ( 'center' == $position ) ? ' at-title--center' : '';
$underline_class = $underline ? ' at-title--underline' : '';
$style_class = ( 'light' == $style ) ? ' at-title--light at-title--underline-light' : '';

$decoration_class = '';
if ( $decoration ) {
	switch ( $position ) {
		case 'center':
			$decoration_class = ' at-title--decoration-bottom-center';
			break;
		case 'left':
			$decoration_class = ' at-title--decoration-bottom-left';
			break;
	}
}

$title_class = $size_class . $position_class . $underline_class . $style_class . $decoration_class;
?>
<div class="at-title<?php echo esc_attr( $title_class ); ?>">
	<?php if ( $subtitle ) { ?>
		<div class="at-title__subtitle"><?php echo esc_html( $subtitle ); ?></div>
	<?php } ?>
	<h3 class="at-title__primary"><?php echo esc_html( $text ); ?></h3>
</div>
