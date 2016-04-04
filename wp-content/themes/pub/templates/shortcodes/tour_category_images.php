<?php
/**
 * Shortcode [tour_category_images] view.
 * For more detailed list see list of shortcode attributes.
 *
 * @var string  $title
 * @var boolean $title_underline
 * @var string  $sub_title
 * @var string  $css_class
 * @var string  $view
 * @var string  $items
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

if ( ! $items ) {
	return;
}

$slider_id = 'swiper' . adventure_tours_di( 'shortcodes_helper' )->generate_id();
wp_enqueue_style( 'swiper' );
wp_enqueue_script( 'swiper' );
TdJsClientScript::addScript(
	'toursTypeSliderInit' . $slider_id,
	'Theme.makeSwiper(' . wp_json_encode( array(
		'containerSelector' => '#' . $slider_id,
		'slidesNumber' => 4,
		'navPrevSelector' => '.tours-type__slider__prev',
		'navNextSelector' => '.tours-type__slider__next',
	) ). ');'
);
?>
<div id="<?php echo esc_attr( $slider_id ); ?>" class="tours-type<?php if ( $css_class ) { echo esc_attr( ' ' . $css_class ); } ?>">
	<?php if ( $title || $sub_title ) {
		echo do_shortcode('[title text="' . addslashes( $title ) . '" subtitle="' . addslashes( $sub_title ) . '" size="big" position="center" decoration="on" underline="' . addslashes( $title_underline ) . '" style="dark"]');
	} ?>
	<div class="tours-type__slider">
		<div class="tours-type__slider__controls">
			<a class="tours-type__slider__prev" href="#"><i class="fa fa-chevron-left"></i></a>
			<a class="tours-type__slider__next" href="#"><i class="fa fa-chevron-right"></i></a>
		</div>

		<div class="swiper-container swiper-slider">
			<div class="swiper-wrapper">
			<?php foreach ( $items as $item ) { ?>
				<?php $detail_url = get_term_link( $item->slug, 'tour_category' ); ?>
				<div class="swiper-slide tours-type__item">
					<a href="<?php echo esc_url( $detail_url ); ?>" class="tours-type__item__image"><?php adventure_tours_render_category_thumbnail( $item ); ?></a>
					<div class="tours-type__item__title"><?php echo esc_html( $item->name ); ?></div>
				</div>
			<?php } ?>
			</div>
		</div>
	</div>
</div>
