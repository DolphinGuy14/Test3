<?php
/**
 * Definition of shortcodes that generate own content based on data stored in DB.
 *
 * @author    Themedelight
 * @package   Themedelight/ATDTP
 * @version   1.0.0
 */

if ( ! function_exists( 'atdpt_shortcode_latest_posts' ) ) {
	add_shortcode( 'latest_posts', 'atdpt_shortcode_latest_posts' );

	/**
	 * Latest posts shorcode rendering function.
	 *
	 * @param  array  $atts     shortcode attributes.
	 * @param  string $content  shortcode content text.
	 * @return string
	 */
	function atdpt_shortcode_latest_posts( $atts, $content = null ) {
		$atts = shortcode_atts( array(
			'title' => '',
			'title_underline' => 'on',
			'number' => 1,
			'translate' => '1',
			'read_more_text' => __( 'Read more', 'adventure-tours-data-types' ),
			'words_limit' => 25,
			'view' => '',
		), $atts );

		$helper = ATDTP()->shortcodes_helper();

		$query_arguments = array(
			'post_type' => 'post',
			'posts_per_page' => $atts['number'] > 0 ? $atts['number'] : -1,
		);

		/*
		if ( $helper->attribute_is_true( $atts['translate'] ) ) {
			$queryArguments = apply_filters( 'widget_posts_args', $query_arguments );
		}
		*/

		$query = new Wp_Query( $query_arguments );
		$atts['title_underline'] = $helper->attribute_is_true( $atts['title_underline'] );
		$atts['items'] = $query->get_posts();

		return $helper->render_view( 'templates/shortcodes/latest_posts', $atts['view'], $atts );
	}
}

if ( ! function_exists( 'atdpt_shortcode_tour_search_form' ) ) {
	add_shortcode( 'tour_search_form', 'atdpt_shortcode_tour_search_form' );

	/**
	 * Tour search form shorcode rendering function.
	 * @param  array  $atts     shortcode attributes.
	 * @param  string $content  shortcode content text.
	 * @return string
	 */
	function atdpt_shortcode_tour_search_form( $atts, $content = null) {
		$atts = shortcode_atts( array(
			'title' => '',
			'note' => '',
			'css_class' => '',
			'view' => '',
		), $atts );

		$helper = ATDTP()->shortcodes_helper();

		return $helper->render_view( 'templates/shortcodes/tour_search_form', $atts['view'], $atts );
	}
}

if ( ! function_exists( 'atdpt_shortcode_tour_category_images' ) ) {
	add_shortcode( 'tour_category_images', 'atdpt_shortcode_tour_category_images' );

	/**
	 * Tour category shortcode shorcode rendering function.
	 *
	 * @param  array  $atts     shortcode attributes.
	 * @param  string $content  shortcode content text.
	 * @return string
	 */
	function atdpt_shortcode_tour_category_images( $atts, $content = null ) {
		$atts = shortcode_atts( array(
			'title' => '',
			'title_underline' => 'on',
			'sub_title' => '',
			'parent_id' => '',
			'ignore_empty' => 1,
			'tour_category' => '',
			'tour_ids' => '',
			'number' => '',
			'css_class' => '',
			'view' => '',
		), $atts );

		$helper = ATDTP()->shortcodes_helper();

		$atts['title_underline'] = $helper->attribute_is_true( $atts['title_underline'] );
		$atts['ignore_empty'] = $helper->attribute_is_true( $atts['ignore_empty'] );
		$atts['items'] = $helper->get_tour_categories_collection( $atts );

		return $helper->render_view( 'templates/shortcodes/tour_category_images', $atts['view'], $atts );
	}
}

if ( ! function_exists( 'atdpt_shortcode_tour_category_icons' ) ) {
	add_shortcode( 'tour_category_icons', 'atdpt_shortcode_tour_category_icons' );

	/**
	 * Tour category shortcode that presents each tour category with related icon.
	 *
	 * @param  array  $atts     shortcode attributes.
	 * @param  string $content  shortcode content text.
	 * @return string
	 */
	function atdpt_shortcode_tour_category_icons( $atts, $content = null ) {
		$atts = shortcode_atts( array(
			'title' => '',
			'title_underline' => 'on',
			'sub_title' => '',
			'parent_id' => '',
			'bg_url' => '',
			'ignore_empty' => 1,
			'tour_category' => '',
			'tour_ids' => '',
			'number' => '',
			'css_class' => '',
			'view' => '',
		), $atts );

		$helper = ATDTP()->shortcodes_helper();

		$atts['title_underline'] = $helper->attribute_is_true( $atts['title_underline'] );
		$atts['ignore_empty'] = $helper->attribute_is_true( $atts['ignore_empty'] );
		$atts['items'] = $helper->get_tour_categories_collection( $atts );

		return $helper->render_view( 'templates/shortcodes/tour_category_icons', $atts['view'], $atts );
	}
}

if ( ! function_exists( 'atdpt_shortcode_tour_carousel' ) ) {
	add_shortcode( 'tour_carousel', 'atdpt_shortcode_tour_carousel' );

	/**
	 * Tour carousel shortcode rendering function.
	 *
	 * @param  array  $atts     shortcode attributes.
	 * @param  string $content  shortcode content text.
	 * @return string
	 */
	function atdpt_shortcode_tour_carousel( $atts, $content = null ) {
		$atts = shortcode_atts( array(
			'title' => '',
			'title_underline' => 'on',
			'sub_title' => '',
			'image_size' => 'thumb_tour_listing_small',
			'image_size_mobile' => 'thumb_tour_medium',
			'bg_url' => '',
			'arrow_style' => 'light',
			'description_words_limit' => 20,
			'tour_category' => '',
			'tour_ids' => '',
			'number' => '',
			'css_class' => '',
			'view' => '',
		), $atts );

		$helper = ATDTP()->shortcodes_helper();

		$atts['title_underline'] = $helper->attribute_is_true( $atts['title_underline'] );
		$atts['items'] = $helper->get_tours_collection( $atts );

		return $helper->render_view( 'templates/shortcodes/tour_carousel', $atts['view'], $atts );
	}
}

if ( ! function_exists( 'atdpt_shortcode_tour_list' ) ) {
	add_shortcode( 'tour_list', 'atdpt_shortcode_tour_list' );

	/**
	 * Tour list shortcode rendering function.
	 *
	 * @param  array  $atts     shortcode attributes.
	 * @param  string $content  shortcode content text.
	 * @return string
	 */
	function atdpt_shortcode_tour_list( $atts, $content = null ) {
		$atts = shortcode_atts( array(
			'title' => '',
			'title_underline' => 'on',
			'sub_title' => '',
			'image_size' => 'thumb_tour_listing_small',
			'image_size_mobile' => '',
			'btn_more_text' => __( 'View more', 'adventure-tours-data-types' ),
			'btn_more_link' => '',
			'price_style' => '',
			'description_words_limit' => 20,
			'tour_category' => '',
			'show_categories' => 'on',
			'tour_ids' => '',
			'number' => '4',
			'css_class' => '',
			'view' => '',
		), $atts );

		$helper = ATDTP()->shortcodes_helper();

		$atts['title_underline'] = $helper->attribute_is_true( $atts['title_underline'] );
		$atts['show_categories'] = $helper->attribute_is_true( $atts['show_categories'] );
		$atts['items'] = $helper->get_tours_collection( $atts );

		return $helper->render_view( 'templates/shortcodes/tour_list', $atts['view'], $atts );
	}
}

if ( ! function_exists( 'atdpt_shortcode_tour_reviews' ) ) {
	add_shortcode( 'tour_reviews', 'atdpt_shortcode_tour_reviews' );

	/**
	 * Tour reviews shortcode (renders latest reviews related to tours) rendering function.
	 *
	 * @param  array  $atts     shortcode attributes.
	 * @param  string $content  shortcode content text.
	 * @return string
	 */
	function atdpt_shortcode_tour_reviews( $atts, $content = null ) {
		$atts = shortcode_atts( array(
			'title' => '',
			'title_underline' => 'on',
			'number' => '2',
			'css_class' => '',
			'view' => '',
		), $atts );

		$helper = ATDTP()->shortcodes_helper();

		$atts['title_underline'] = $helper->attribute_is_true( $atts['title_underline'] );
		$atts['reviews'] = get_comments( array(
			'number' => (int) $atts['number'],
			'status' => 'approve',
			'post_status' => 'publish',
			'post_type' => 'product',
			// Filtering only ratings related to tours.
			'meta_key' => 'is_tour_rating',
			'meta_value' => '1',
		) );

		return $helper->render_view( 'templates/shortcodes/tour_reviews', $atts['view'], $atts );
	}
}
