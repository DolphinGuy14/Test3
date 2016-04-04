<?php
/**
 * Tour archive template.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

get_header( );
?>

<?php
ob_start();
do_action( 'woocommerce_sidebar' );
$sidebarContent = ob_get_clean();

$cat_term = is_tax() ? get_queried_object() : null;
$is_cat_page = $cat_term ? true : false;
$display_mode = AtTourHelper::get_tour_archive_page_display_mode( $cat_term ? $cat_term->term_id : 0 );
?>

<?php ob_start(); ?>
	<?php if ( get_query_var( 'paged' ) < 2 ) {
		if ( $is_cat_page ) {
			echo wc_format_content( $cat_term->description );
		} elseif ( is_archive() ) {
			$tourPageId = adventure_tours_get_option( 'tours_page' );
			$toursPage = $tourPageId ? get_post( $tourPageId ) : null;
			if ( $toursPage ) {
				echo wc_format_content( $toursPage->post_content );
			}
		}
	} ?>
	<?php if ( have_posts() ) : ?>
		<?php if ( get_query_var( 'paged' ) < 2 && ( 'both' == $display_mode || 'subcategories' == $display_mode ) ) {
			adventure_tours_di( 'register' )->setVar( 'tour_cat_columns', $sidebarContent ? 2 : 3 );
			adventure_tours_render_tour_categories(array(
				'before' => '<div class="row">',
				'after' => '</div>',
			));
		} ?>
		<?php if ( 'both' == $display_mode || 'products' == $display_mode ) {
			while ( have_posts() ) {
				the_post();
				adventure_tours_render_template_part( 'templates/tour/content' );
			}
			adventure_tours_render_pagination();
		} ?>
	<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>
		<?php wc_get_template( 'loop/no-products-found.php' ); ?>
	<?php endif; ?>
<?php $primaryContent = ob_get_clean();  ?>

<?php adventure_tours_render_template_part('templates/layout', '', array(
	'content' => $primaryContent,
	'sidebar' => $sidebarContent,
)); ?>

<?php get_footer( ); ?>
