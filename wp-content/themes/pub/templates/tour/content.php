<?php
/**
 * Tour content template part.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

global $product;
$average = $product->get_average_rating();

$permalink = get_permalink();
$title = get_the_title();

$description_words_limit = 13;

$thumb_size = 'thumb_tour_box';
$thumb_html = adventure_tours_get_the_post_thumbnail( null, $thumb_size );

$price_html = $product->get_price_html();

ob_start();
adventure_tours_render_product_attributes(array(
	'before' => '<div class="item-attributes item-attributes--style2">',
	'after' => '</div>',
	'before_each' => '<div class="item-attributes__item">',
	'after_each' => '</div>',
	'limit' => 3,
));
$attributes = ob_get_clean();
?>
<div class="atlist__item margin-bottom">
	<div class="atlist__item__image">
	<?php printf('<a class="atlist__item__image-wrap" href="%s">%s</a>',
		esc_url( $permalink ),
		$thumb_html ? $thumb_html : adventure_tours_placeholder_img( $thumb_size )
	); ?>
	<?php adventure_tours_render_tour_icons(array(
		'before' => '<div class="atlist__item__icons">',
		'after' => '</div>',
	)); ?>
	<?php if ( $product->is_type( 'tour' ) ) {
		adventure_tours_renders_tour_badge( array(
			'tour_id' => get_the_ID(),
			'wrap_css_class' => 'atlist__item__angle-wrap',
			'css_class' => 'atlist__item__angle',
		) );
	} ?>
	</div>
	<div class="atlist__item__content<?php if ( ! $attributes ) { echo ' atlist__item__content--full-height'; }; ?>">
		<div class="atlist__item__content__items">
			<div class="atlist__item__content__item">
				<h2 class="atlist__item__title"><a href="<?php echo esc_url( $permalink ); ?>"><?php the_title(); ?></a></h2>
				<div class="atlist__item__description"><?php echo adventure_tours_get_short_description( null, $description_words_limit ); ?></div>
			</div>
			<div class="atlist__item__content__item atlist__item__content__item--alternative">
				<?php
					if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' ) {
						$rating_count = $product->get_rating_count();
						$review_count = $product->get_review_count();
						$average = $product->get_average_rating();

						if ( $rating_count > 0 ) {
							adventure_tours_renders_stars_rating($product->get_average_rating(), array(
								'before' => '<div class="atlist__item__rating">',
								'after' => '</div>',
							));
							echo '<div class="atlist__item__rating-value">' . $average . ' / ' . sprintf( _n( '1 review', '%s reviews', $review_count, 'adventure-tours' ), $review_count ) . '</div>';
						}
					}
				?>
				<?php if ( $price_html ) {
					printf( '<div class="atlist__item__price"><a href="%s">%s</a></div>',
						esc_url( $permalink ),
						$price_html
					);
				} ?>
				<?php 
					$label_text = apply_filters( 'adventure_tours_list_price_decoration_label', __( 'per person', 'adventure-tours' ), $product );
					if ( $label_text ) {
						printf('<div class="atlist__item__price-label">%s</div>', esc_html( $label_text ) );
					}
				?>
				<div class="atlist__item__read-more"><a href="<?php echo esc_url( $permalink ); ?>"><?php _e( 'view tour', 'adventure-tours' ); ?></a></div>
			</div>
		</div>
		<?php if ( $attributes ) {
			printf( '<div class="atlist__item__attributes">%s</div>', $attributes ); 
		} ?>
	</div>
</div>
