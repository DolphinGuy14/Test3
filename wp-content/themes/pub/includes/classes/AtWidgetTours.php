<?php
/**
 * Widget component allows present some set of tours.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

class AtWidgetTours extends WP_Widget
{
	public function __construct() {
		parent::__construct(
			'tours_adventure_tours',
			'AdventureTours: ' . esc_html__( 'Tours', 'adventure-tours' ),
			array(
				'description' => esc_html__( 'Tours Widget', 'adventure-tours' ),
			)
		);
	}

	protected function get_field_values_list( $field_code ) {
		static $all_lists;
		if ( null == $all_lists ) {
			$all_lists = array(
				'orderby' => array(
					'date' => __( 'Added Date', 'adventure-tours'),
					'most_popular' => __( 'Most popular', 'adventure-tours'),
					'sales' => __( 'Sales', 'adventure-tours'),
					'price' => __( 'Price', 'adventure-tours'),
					'rand' => __( 'Random', 'adventure-tours'),
				),
				'order' => array(
					'DESC' => __( 'DESC', 'adventure-tours'),
					'ASC' =>__( 'ASC', 'adventure-tours'),
				),
				'display_mode' => array(
					'price' => __( 'Price', 'adventure-tours'),
					'price_rating' => __( 'Price & Rating', 'adventure-tours'),
					'price_badge' => __( 'Price & Badge', 'adventure-tours'),
					'price_rating_badge' => __( 'Price & Rating & Badge', 'adventure-tours'),
					'alt-price' => __( 'Highlighted Price', 'adventure-tours'),
					'alt-price_rating' => __( 'Highlighted Price & Rating', 'adventure-tours'),
					'badge' => __( 'Badge', 'adventure-tours'),
					'rating' => __( 'Rating', 'adventure-tours'),
					'rating_badge' => __( 'Rating & Badge', 'adventure-tours'),
				),
				'show' => array(
					'' => __( 'All', 'adventure-tours'),
					'featured' => __( 'Featured', 'adventure-tours'),
					'onsale' => __( 'On Sale', 'adventure-tours'),
				)
			);
		}

		return isset($all_lists[$field_code]) ? $all_lists[$field_code] : array();
	}

	public function widget( $args, $instance ) {
		adventure_tours_render_template_part( 'templates/widgets/tours', '', array(
			'widget_args' => $args,
			'settings' => $instance,
			'items' => adventure_tours_di( 'shortcodes_helper' )->get_tours_collection( $instance )
		) );
	}

	public function form( $instance ) {
		$default = array(
			'title' => '',
			'display_mode' => 'rating_badge',

			'show' => '',
			'tour_category' => '',
			'tour_ids' => '',
			'orderby' => 'date',
			'order' => 'DESC',
			'number' => '2',
		);

		$instance = wp_parse_args( (array) $instance, $default );

		$parts = array();

		$parts[] = $this->render_input_row( 
			'title',
			__( 'Title', 'adventure-tours' ),
			$this->render_text_input(
				'title', $instance['title']
			)
		);

		// Tour category selector rendering - start
		$current_term_id = 0;
		if ( $instance['tour_category'] ) {
			$cur_cat_term = get_term_by('slug', $instance['tour_category'], 'tour_category' );
			if ( $cur_cat_term ) {
				$current_term_id = $cur_cat_term->term_id;
			}
		}

		$parts[] = $this->render_input_row( 'tour_category', __( 'Tour category', 'adventure-tours' ), wp_dropdown_categories( array(
			'show_option_all' => __( 'Any' , 'adventure-tours' ),
			'name' => $this->get_field_name( 'tour_category' ),
			'class' => 'widefat',
			'hide_if_empty' => false,
			'taxonomy' => 'tour_category', 
			'hierarchical' => true,
			'echo' => false,
			'value_field' => 'slug',
			'hide_if_empty' => true,
			'show_count' => true,
			'selected' => $current_term_id,
		) ) );
		// Tour category selector rendering - end

		$parts[] = $this->render_input_row( 'show', __( 'Show', 'adventure-tours' ), $this->render_select_input(
			'show', $instance['show'], $this->get_field_values_list('show'), '')
		);

		$parts[] = $this->render_input_row( 'orderby', __( 'Order', 'adventure-tours' ), $this->render_select_input(
			'orderby', $instance['orderby'], $this->get_field_values_list('orderby'), '') . $this->render_select_input('order', $instance['order'], $this->get_field_values_list('order'), '')
		);

		$parts[] = $this->render_input_row( 
			'tour_ids',
			__( 'Tour ids', 'adventure-tours' ),
			$this->render_text_input(
				'tour_ids', $instance['tour_ids'], ''
			)
		);

		$parts[] = $this->render_input_row( 'number', __( 'Number of posts to show', 'adventure-tours' ), $this->render_text_input(
			'number', $instance['number'], ''
		) );

		$parts[] = $this->render_input_row( 'display_mode', __( 'Display Mode', 'adventure-tours' ), $this->render_select_input(
			'display_mode', $instance['display_mode'], $this->get_field_values_list('display_mode'), '')
		);

		print join('', $parts);
	}

	protected function render_select_input( $field_code, $value, $options_list, $css_class = 'widefat' ) {
		return sprintf( '<select id="%s" class="%s" name="%s">%s</select>',
			$css_class ? esc_attr( $css_class ) : '',
			esc_attr( $this->get_field_id( $field_code ) ),
			esc_attr( $this->get_field_name( $field_code ) ),
			$this->render_options_html( $options_list, $value )
		);
	}

	protected function render_checkbox_input( $field_code, $value, $css_class = '' ) {
		return sprintf('<input class="%s" id="%s" name="%s" type="checkbox" value="on"%s>',
			$css_class ? esc_attr( $css_class ) : '',
			esc_attr( $this->get_field_id( $field_code ) ),
			esc_attr( $this->get_field_name( $field_code ) ),
			$value ? ' checked="checked"' : ''
		);
	}

	protected function render_text_input( $field_code, $value, $css_class = 'widefat' ) {
		return sprintf('<input class="%s" id="%s" name="%s" type="text" value="%s">',
			$css_class ? esc_attr( $css_class ) : '',
			esc_attr( $this->get_field_id( $field_code ) ),
			esc_attr( $this->get_field_name( $field_code ) ),
			esc_attr( $value )
		);
	}

	protected function render_input_row( $field_code, $label, $input_html, $css_class = '' ) {
		return sprintf('<p class="%s"><label for="%s">%s</label>&nbsp;%s</p>',
			$css_class ? esc_attr( $css_class ) : '',
			esc_attr( $this->get_field_id( $field_code ) ),
			esc_html( $label ),
			$input_html
		);
	}

	protected function render_options_html( array $options, $selectedValue = '' ) {
		$result = '';
		foreach ( $options as $val => $title ) {
			$checked = ( $val == $selectedValue ) ? ' selected="selected"' : '';
			$result .= '<option value="' . esc_attr( $val ) . '" ' . $checked . '>' . esc_html( $title ) . '</option>';
		}
		return $result;
	}
}
