<?php
/**
 * Shortcode [icons_set] view.
 * For more detailed list see list of shortcode attributes.
 *
 * @var array   $items
 * @var integer $row_size
 * @var string  $view
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

if ( ! $items ) {
	return;
}

if ( $row_size < 2 ) {
	$row_size = 2;
} elseif ( $row_size > 4 ) {
	$row_size = 4;
}
$cell_size = 12 / $row_size;
$row_item_counter = 0;
?>
<div class="at-icons-set">
	<div class="row at-icons-set__row">
	<?php foreach ( $items as $item ) { ?>
		<?php
		if ( 0 != $row_item_counter ) {
			if ( $row_item_counter % $row_size == 0 ) {
				echo '</div><div class="row at-icons-set__row">';
				$row_item_counter = 0;
			}
		}
		$row_item_counter++;
		?>
		<div class="col-sm-<?php echo esc_attr( $cell_size ); ?>">
			<div class="at-icons-set__item">
				<?php if( $item['icon'] ) { ?>
					<div class="at-icons-set__item__icon-wrap"><i class="at-icons-set__item__icon<?php echo ' ' . esc_attr( $item['icon'] ); ?>"></i></div>
				<?php } ?>
				<div class="at-icons-set__item__content">
					<?php if ( $item['title'] ) { ?>
						<h3 class="at-icons-set__item__title"><?php echo esc_html( $item['title'] ); ?></h3>
					<?php } ?>
					<div class="at-icons-set__item__description"><?php echo do_shortcode( $item['content'] ); ?></div>
				</div>
			</div>
		</div>
	<?php } ?>
	</div>
</div>
