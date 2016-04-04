<?php
global $product;
$product_permalink = get_permalink( $product->id );
?>
<li class="product_list_widget__item">
	<?php printf( '<div class="product_list_widget__item__image">%s</div>', $product->get_image() ); ?>
	<div class="product_list_widget__item__content">
		<div class="product_list_widget__item__title">
			<a href="<?php echo esc_url( $product_permalink ); ?>"><?php echo esc_html( $product->get_title() ); ?></a>
		</div>
		<?php printf( '<div class="product_list_widget__item__price">%s</div>', $product->get_price_html() ); ?>
		<?php if ( ! empty( $show_rating ) ) { ?>
			<?php
				$average = $product->get_average_rating();
				adventure_tours_renders_stars_rating( ceil( $average ), array(
					'before' => '<div class="product_list_widget__item__rating">',
					'after' => '</div>',
				) );
			?>
		<?php } else { ?>
			<a href="<?php echo esc_url( $product_permalink ); ?>" class="product_list_widget__item__button"><?php esc_html_e( 'View', 'adventure-tours' ); ?></a>
		<?php } ?>
	</div>
</li>