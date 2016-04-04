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
?>

<div class="at-last-posts">
<?php if ( $title ) { ?>
	<h3 class="at-last-posts__title"><?php echo $title; ?></h3>
<?php } ?>

<?php foreach ( $items as $post ) : ?>
	<?php
	$image = get_the_post_thumbnail( $post->ID, 'thumbnail' );
	$classItem = ($image) ? ' at-last-posts__item--with-images' : '';
	$post_link = get_the_permalink( $post->ID );
	?>
	<div class="at-last-posts__item<?php echo esc_attr( $classItem ); ?>">
		<?php
		printf( '<a href="%s" class="at-last-posts__item__image-wrap">%s</a>',
			esc_url( $post_link ),
			$image
		);
		?>
		<div class="at-last-posts__item__content">
			<h3 class="at-last-posts__item__title"><a href="<?php echo esc_url( $post_link ); ?>"><?php echo esc_html( $post->post_title ); ?></a></h3>
			<div class="at-last-posts__item__description"><?php echo esc_html( $post->post_content ); ?></div>
			<div class="at-last-posts__item__read-more"><a href="<?php echo esc_url( $post_link ); ?>"><?php echo esc_html( $read_more_text ); ?></a></div>
		</div>
	</div>
<?php endforeach; ?>
</div>
