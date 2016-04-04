<?php
/**
 * Shortcode [tour_search_form] view.
 * For more detailed list see list of shortcode attributes.
 *
 * @var string $title
 * @var string $note
 * @var string $css_class
 * @var string $view
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

$attributesRequest = isset( $_REQUEST['tourtax'] ) ? $_REQUEST['tourtax'] : array();
?>
<div class="form-block block-after-indent<?php if ( $css_class ) { echo esc_attr( ' ' . $css_class ); } ?>">
<?php if ( $title ) { ?>
	<h3 class="form-block__title"><?php echo esc_html( $title ); ?></h3>
<?php } ?>

<?php if ( $note ) { ?>
	<div class="form-block__description"><?php echo esc_html( $note ); ?></div>
<?php } ?>

	<form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<input type="hidden" name="toursearch" value="1">

		<div class="form-block__item form-block__field-width-icon">
			<input type="text" placeholder="<?php echo esc_attr_x( 'Search Tour', 'placeholder', 'adventure-tours' ); ?>" value="<?php echo get_search_query(); ?>" name="s">
			<i class="td-search-1"></i>
		</div>

		<?php $formTaxonomies = AtTourHelper::get_search_form_fields();
		if ( $formTaxonomies ) {
			foreach ( $formTaxonomies as $name => $list ) {
				if ( '__tour_categories_filter' === $name ) {

					if ( ! adventure_tours_check( 'tour_category_taxonomy_exists' ) ) {
						continue;
					}

					$current_term_id = ! empty( $_REQUEST['tour_category'] ) ? $_REQUEST['tour_category'] : '';
					/*
					// For WP < 4.3
					$current_term_id = 0;
					$tour_cat_slug = ! empty( $_REQUEST['tour_category'] ) ? $_REQUEST['tour_category'] : '';
					if ( $tour_cat_slug ) {
						$cur_cat_term = get_term_by( 'slug', $tour_cat_slug, 'tour_category' );
						if ( $cur_cat_term ) {
							$current_term_id = $cur_cat_term->term_id;
						}
					}*/

					$use_parent_cat_name_as_title = true;
					$parent_term_id = adventure_tours_get_option( 'tours_search_form_start_category' );
					$show_all_title = __( 'Category', 'adventure-tours' );
					if ( $use_parent_cat_name_as_title && $parent_term_id ) {
						$parent_term_obj = get_term( $parent_term_id, 'tour_category' );
						if ( $parent_term_obj ) {
							$show_all_title = $parent_term_obj->name;
						}
					}

					$drop_down_html = wp_dropdown_categories( array(
						'show_option_all' => $show_all_title,
						'hide_if_empty' => true,
						'taxonomy' => 'tour_category',
						'hierarchical' => true,
						'echo' => false,
						'name' => 'tour_category',
						'value_field' => 'slug',
						'hide_if_empty' => true,
						'class' => 'selectpicker',
						'show_count' => true,
						'selected' => $current_term_id,
						'child_of' => $parent_term_id,
					) );

					if ( $drop_down_html ) {
						echo '<div class="form-block__item form-block__field-width-icon">' .
							$drop_down_html;

						$field_icon_class = $parent_term_id ? AtTourHelper::get_tour_category_icon_class( $parent_term_id ) : '';
						printf( '<i class="%s"></i>', $field_icon_class ? esc_attr( $field_icon_class ) : 'td-network');

						echo '</div>';
					}
				} else {
					$selectedValue = isset( $attributesRequest[ $name ] ) ? $attributesRequest[ $name ] : '';
					$listOptions = array();
					foreach ( $list as $value => $title ) {
						$listOptions[] = sprintf(
							'<option value="%s"%s>%s</option>',
							esc_attr( $value ),
							$selectedValue == $value ? ' selected="selected"' : '',
							esc_html( $title )
						);
					}

					$iconClass = AtTourHelper::get_product_attribute_icon_class( $name );
					printf(
						'<div class="form-block__item%s"><select name="tourtax[%s]" class="selectpicker">%s</select>%s</div>',
						$iconClass ? ' form-block__field-width-icon' : '',
						esc_attr( $name ),
						join( '', $listOptions ),
						$iconClass ? sprintf( '<i class="%s"></i>', esc_attr( $iconClass ) ) : ''
					);
				}
			}
		} ?>

		<input class="form-block__button" type="submit" value="<?php esc_attr_e( 'Find Tours', 'adventure-tours' ); ?>">
	</form>
</div>
