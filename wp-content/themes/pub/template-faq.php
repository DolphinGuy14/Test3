<?php
/**
 * Template Name: FAQ
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

get_header();

TdJsClientScript::addScript( 'faqAccorsionChagesIconInit', 'Theme.faqAccordionCahgesIcon();' );

$is_sidebar = is_active_sidebar( 'faq-sidebar' );
$show_question_form = adventure_tours_get_option( 'faq_show_question_form' );
$is_show_col = ( $is_sidebar || $show_question_form );
$accordion_id = 1;
$accordion_item_id = 1;
?>

<?php if ( adventure_tours_check( 'faq_taxonomies' ) && have_posts() ) : ?>
	<?php while ( have_posts() ) { the_post(); ?>
		<div class="row faq">
			<main class="<?php echo ($is_show_col) ? 'col-md-9' : 'col-md-12'; ?>" role="main">
			<?php
				$faq_category_taxonomy = 'faq_category';
				$faq_category = get_terms( $faq_category_taxonomy );
				$categoryId = array();
				if ( $faq_category ) {
					foreach ( $faq_category as $category ) {
						$categoryId[] = $category->term_id;
					}
				}
				$uncategorized = new stdClass();
				$uncategorized->slug = 'uncategorized';
				array_unshift( $faq_category, $uncategorized );

				foreach ( $faq_category as $category ) :
					$tax_query = array();
					if ( 'uncategorized' == $category->slug ) {
						$tax_query = array(
							'taxonomy' => $faq_category_taxonomy,
							'field' => 'id',
							'terms' => $categoryId,
							'operator' => 'NOT IN',
						);
					} else {
						$tax_query = array(
							'taxonomy' => $faq_category_taxonomy,
							'field' => 'slug',
							'terms' => $category->slug,
						);
					}

					$query = new WP_Query(array(
						'post_type' => 'faq',
						'posts_per_page' => -1,
						'tax_query' => array( $tax_query ),
					));
					$posts = $query->get_posts();

					if ( empty( $posts ) ) {
						continue;
					}
				?>
				<div class="faq__item">
					<?php if ( isset( $category->name ) ) { ?>
						<div class="section-title title title--small title--center title--decoration-bottom-center title--underline">
							<h2 class="title__primary"><?php echo esc_html( $category->name ); ?></h2>
						</div>
						<?php } ?>
					<div class="padding-left padding-right">
						<div class="panel-group faq__accordion" id="faq-accordion<?php echo esc_attr( $accordion_id ); ?>">
							<?php foreach ( $posts as $post ) { ?>
								<div class="panel faq__accordion__item">
									<div class="faq__accordion__heading">
										<i class="fa"></i>
										<a class="faq__accordion__title" data-toggle="collapse" data-parent="#faq-accordion<?php echo esc_attr( $accordion_id ); ?>" href="#faq-accrodiotn-item<?php echo esc_attr( $accordion_item_id ); ?>"><?php echo esc_html( $post->post_title ); ?></a>
									</div>
									<div id="faq-accrodiotn-item<?php echo esc_attr( $accordion_item_id ); ?>" class="collapse faq__accordion__content-wrap">
										<div class="faq__accordion__content"><?php echo apply_filters( 'the_content', $post->post_content ); ?></div>
									</div>
								</div>
							<?php $accordion_item_id++; } ?>
						</div>
					</div>
				</div>
				<?php $accordion_id++;
				endforeach; ?>
			</main>
		<?php if ( $is_show_col ) : ?>
			<aside class="col-md-3 sidebar" role="complementary">
				<?php if ( $show_question_form ) { get_template_part( 'templates/parts/faq-question-form' ); } ?>
				<?php if ( $is_sidebar ) { dynamic_sidebar( 'faq-sidebar' ); } ?>
			</aside>
		<?php endif; ?>
		</div>
	<?php } ?>
<?php else : ?>
	<?php get_template_part( 'content', 'none' ); ?>
<?php endif; ?>

<?php get_footer(); ?>
