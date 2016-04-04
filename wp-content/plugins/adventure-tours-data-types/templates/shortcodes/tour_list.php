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
$item_wrapper_class = 'col-md-'.( 12 / $colNumber ).' col-xs-6 at-atgrid__item-wrap';

if ( $image_size_mobile  && wp_is_mobile() ) {
	$image_size = $image_size_mobile;
}

if ( $colNumber > 3 ) {
	if ( $css_class ) {
		$css_class .= ' ';
	}
	$css_class = $css_class . ' at-atgrid--small';
}
?>

<div class="at-atgrid<?php if ( $css_class ) echo ' ' . esc_attr( $css_class ); ?>">
	<?php echo do_shortcode( '[title text="' . $title . '" subtitle="' . $sub_title . '" size="big" position="center" decoration="on" underline="' . $title_underline . '" style="dark"]' ); ?>
	<div class="row at-atgrid__row">
	<?php foreach ( $items as $item_index => $item ) : ?>
		<?php
		$post_id = $item->id;
		$item_url = get_permalink( $post_id );
		$image_html = get_the_post_thumbnail( $post_id, $image_size );
		$price_html = $item->get_price_html();

		if ( $item_index > 0 && $item_index % $colNumber == 0 ) {
			// echo '</div><div class="row at-atgrid__row">';
			echo '<div class="clearfix hidden-sm hidden-xs"></div>';
		}
		if ( $item_index > 0 && $item_index % 2 == 0 ) {
			echo '<div class="clearfix visible-sm visible-xs"></div>';
		}
		?>
		<div class="<?php echo esc_attr( $item_wrapper_class ); ?>">
			<div class="at-atgrid__item">
				<div class="at-atgrid__item__top">
					<?php printf('<a href="%s" class="at-atgrid__item__top__image">%s</a>',
						esc_url( $item_url ),
						$image_html
					); ?>
					<?php if ( 'highlighted' == $price_style ) { ?>
						<?php
						printf('<a href="%s" class="price-round"><span class="price-round__content">%s</span></a>',
							esc_url( $item_url ),
							$price_html
						);
						?>
					<?php } else { ?>
						<?php if ( $price_html ) {
							printf('<div class="at-atgrid__item__price"><a href="%s" class="at-atgrid__item__price__button">%s</a></div>',
								esc_url( $item_url ),
								$price_html
							);
						} ?>
					<?php } ?>
				</div>
				<div class="at-atgrid__item__content">
					<h3 class="at-atgrid__item__title"><a href="<?php echo esc_url( $item_url ); ?>"><?php echo esc_html( $item->post->post_title ); ?></a></h3>
					<div class="at-atgrid__item__description"><?php echo esc_html( $item->post->post_content ); ?></div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
	</div>
</div>
