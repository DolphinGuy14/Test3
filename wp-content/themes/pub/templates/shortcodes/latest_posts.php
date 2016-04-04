<?php
/**
 * Shortcode [latest_posts] view.
 * For more detailed list see list of shortcode attributes.
 *
 * @var string  $title
 * @var boolean $title_underline
 * @var string  $number
 * @var string  $translate
 * @var srting  $read_more_text
 * @var string  $words_limit
 * @var string  $view
 * @var array   $items
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

if ( ! $items ) {
	return '';
}

$title_underline_class = ( $title_underline ) ? ' title--underline' : '';
?>
<div class="last-posts">
<?php
if ( $title ) {
	echo do_shortcode( '[title text="' . $title . '" size="small" position="center" decoration="on" underline="' . $title_underline . '" style="dark"]' );
}
?>
<?php foreach ( $items as $post ) : ?>
	<?php
	$image = adventure_tours_get_the_post_thumbnail( $post->ID, 'thumb_last_posts_shortcode' );
	$classItem = ($image) ? ' last-posts__item--with-images' : '';
	$post_link = get_the_permalink( $post->ID );
	?>
	<div class="last-posts__item<?php echo esc_attr( $classItem ); ?>">
		<div class="last-posts__item__container">
		<?php
		printf( '<a href="%s" class="last-posts__item__image-wrap">%s</a>',
			esc_url( $post_link ),
			$image
		);
		?>
			<div class="last-posts__item__content">
				<h3 class="last-posts__item__title"><a href="<?php echo esc_url( $post_link ); ?>"><?php echo esc_html( $post->post_title ); ?></a></h3>
				<div class="last-posts__item__description"><?php echo adventure_tours_do_excerpt( $post->post_content, $words_limit ); ?></div>
				<div class="read-more"><a href="<?php echo esc_url( $post_link ); ?>"><?php echo esc_html( $read_more_text ); ?></a></div>
			</div>
		</div>
	</div>
<?php endforeach; ?>
</div>
