<?php
/**
 * Shortcode [tour_list] view.
 * For more detailed list see list of shortcode attributes.
 *
 * @var string  $title
 * @var boolean $title_underline
 * @var string  $sub_title
 * @var string  $image_size
 * @var string  $btn_more_text           text for more button
 * @var string  $btn_more_link           url address for more button
 * @var string  $price_style             allowed values are: 'default', 'highlighted',
 * @var string  $description_words_limit limit for words that should be outputed for each item
 * @var boolean $show_categories
 * @var int     $number
 * @var string  $css_class
 * @var string  $view
 * @var array   $items                   collection of tours that should be rendered.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

if ( ! $items ) {
	return;
}

// $is_mobile = wp_is_mobile();
// $max_col = $is_mobile ? 2 : 4;
$max_col = 4;

// $colChooseVal = $number > count( $items ) ? count( $items ) : $number;
$colChooseVal = $number > 0 ? $number : $max_col;

$colNumber = $colChooseVal > $max_col ? $max_col : $colChooseVal;
$item_wrapper_class = 'col-md-'.( 12 / $colNumber ).' col-xs-6 atgrid__item-wrap';

if ( $image_size_mobile  && wp_is_mobile() ) {
	$image_size = $image_size_mobile;
}

if ( $colNumber > 3 ) {
	if ( $css_class ) {
		$css_class .= ' ';
	}
	$css_class = $css_class . ' atgrid--small';
}

$placeholder_image = adventure_tours_placeholder_img( $image_size );
?>

<div class="atgrid<?php if ( $css_class ) echo ' ' . esc_attr( $css_class ); ?>">
<?php if ( $btn_more_link && ( $title || $sub_title ) ) { ?>
	<div class="title-block-link title-block-link--with-button">
		<div class="title-block-link__text-wrap">
		<?php if ( $title ) { ?>
			<h3 class="title-block-link__title"><?php echo esc_html( $title ); ?></h3>
		<?php } ?>
		<?php if ( $sub_title ) { ?>
			<div class="title-block-link__description"><?php echo esc_html( $sub_title ); ?></div>
		<?php } ?>
		</div>
		<div class="title-block-link__button-wrap">
			<a href="<?php echo esc_url( $btn_more_link ); ?>" class="title-block-link__button"><?php echo esc_html( $btn_more_text ); ?><i class="fa fa-long-arrow-right"></i></a>
		</div>
	</div>
<?php } elseif ( $title || $sub_title ) { ?>
	<?php echo do_shortcode( '[title text="' . $title . '" subtitle="' . $sub_title . '" size="big" position="center" decoration="on" underline="' . $title_underline . '" style="dark"]' ); ?>
<?php } ?>
	<div class="row atgrid__row">
	<?php foreach ( $items as $item_index => $item ) : ?>
		<?php
		$post_id = $item->id;
		$item_url = get_permalink( $post_id );
		$image_html = adventure_tours_get_the_post_thumbnail( $post_id, $image_size );
		$price_html = $item->get_price_html();

		if ( $item_index > 0 && $item_index % $colNumber == 0 ) {
			// echo '</div><div class="row atgrid__row">';
			echo '<div class="clearfix hidden-sm hidden-xs"></div>';
		}
		if ( $item_index > 0 && $item_index % 2 == 0 ) {
			echo '<div class="clearfix visible-sm visible-xs"></div>';
		}
		?>
		<div class="<?php echo esc_attr( $item_wrapper_class ); ?>">
			<div class="atgrid__item">
				<div class="atgrid__item__top">
					<?php printf('<a href="%s" class="atgrid__item__top__image">%s</a>',
						esc_url( $item_url ),
						$image_html ? $image_html : $placeholder_image
					); ?>
					<?php if ( 'highlighted' == $price_style ) { ?>
						<?php
						$badge = adventure_tours_di( 'tour_badge_service' )->get_tour_badge( $post_id );
						printf('<a href="%s" class="price-round"%s><span class="price-round__content">%s</span></a>',
							esc_url( $item_url ),
							isset( $badge['color'] ) ? ' style="background-color:' . esc_attr( $badge['color'] ) . '"' : '',
							$price_html
						);
						?>
					<?php } else { ?>
						<?php adventure_tours_renders_tour_badge( array(
							'tour_id' => $post_id,
							'wrap_css_class' => 'atgrid__item__angle-wrap',
							'css_class' => 'atgrid__item__angle',
						) ); ?>
						<?php if ( $price_html ) {
							printf('<div class="atgrid__item__price"><a href="%s" class="atgrid__item__price__button">%s</a></div>',
								esc_url( $item_url ),
								$price_html
							);
						} ?>
					<?php } ?>
					<?php adventure_tours_renders_stars_rating($item->get_average_rating(), array(
						'before' => '<div class="atgrid__item__rating">',
						'after' => '</div>',
					)); ?>
					<?php if ( $show_categories ) {
						adventure_tours_render_tour_icons(array(
							'before' => '<div class="atgrid__item__icons">',
							'after' => '</div>',
						), $post_id );
					} ?>
				</div>
				<div class="atgrid__item__content">
					<h3 class="atgrid__item__title"><a href="<?php echo esc_url( $item_url ); ?>"><?php echo esc_html( $item->post->post_title ); ?></a></h3>
				<?php if ( $description_words_limit > 0 ) { ?>
					<div class="atgrid__item__description"><?php echo adventure_tours_get_short_description( $item->post, $description_words_limit ); ?></div>
				<?php } ?>
				</div>
				<div class="item-attributes">
					<?php adventure_tours_render_product_attributes(array(
						'before_each' => '<div class="item-attributes__item">',
						'after_each' => '</div>',
						'limit' => 2,
					), $post_id ); ?>
					<div class="item-attributes__item"><a href="<?php echo esc_url( $item_url ); ?>" class="item-attributes__link"><i class="fa fa-long-arrow-right"></i></a></div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
	</div>
</div>
