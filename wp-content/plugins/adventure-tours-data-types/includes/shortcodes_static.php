<?php
/**
 * Definition of shortcodes that generate own content based on own params/theme settings values.
 *
 * @author    Themedelight
 * @package   Themedelight/ATDTP
 * @version   1.0.0
 */

$shortoces_nl_escaper = ATDTP()->shortcodes_helper()->nl_escaper();

if ( ! shortcode_exists( 'row') && ! shortcode_exists( 'column' ) ) {
	ATDTP()->require_file( '/classes/ATDTP_Shortcodes_Row.php' );
	ATDTP_Shortcodes_Row::register( 'row', 'column' );

	if ( $shortoces_nl_escaper ) {
		$shortoces_nl_escaper->registerNestedShortcodes( 'row','column' );
	}
}

if ( ! shortcode_exists( 'faq_question_form' ) ) {
	ATDTP()->require_file( '/classes/ATDTP_Shortcodes_FAQ_Question_Form.php' );
	ATDTP_Shortcodes_FAQ_Question_Form::register( 'faq_question_form' );
}

if ( ! shortcode_exists( 'title' ) ) {
	add_shortcode( 'title', 'atdtp_shortcode_title' );

	/**
	 * Title shortcode rendering function.
	 *
	 * @param  array  $atts     shortcode attributes.
	 * @param  string $content  shortcode content text.
	 * @return string
	 */
	function atdtp_shortcode_title( $atts, $content=null ) {
		$atts = shortcode_atts( array(
			'text' => '',
			'subtitle' => '',
			'size' => 'big',
			'position' => 'left',
			'decoration' => 'on',
			'underline' => 'on',
			'style' => 'dark',
			'view' => '',
		), $atts );

		$helper = ATDTP()->shortcodes_helper();

		$atts['decoration'] = $helper->attribute_is_true( $atts['decoration'] );
		$atts['underline'] = $helper->attribute_is_true( $atts['underline'] );

		return $helper->render_view( 'templates/shortcodes/title', $atts['view'], $atts );
	}
}

if ( ! shortcode_exists( 'social_icons' ) ) {
	add_shortcode( 'social_icons', 'atdtp_shortcode_social_icons' );

	/**
	 * Social icons shortcode rendering function.
	 *
	 * @param  array  $atts     shortcode attributes.
	 * @param  string $content  shortcode content text.
	 * @return string
	 */
	function atdtp_shortcode_social_icons( $atts, $content=null ) {
		$atts = shortcode_atts( array(
			'title' => '',
			'facebook_url' => '',
			'twitter_url' => '',
			'googleplus_url' => '',
			'pinterest_url' => '',
			'linkedin_url' => '',
			'instagram_url' => '',
			'dribbble_url' => '',
			'tumblr_url' => '',
			'vk_url' => '',
			'view' => '',
		), $atts );

		return ATDTP()->shortcodes_helper()->render_view( 'templates/shortcodes/social_icons', $atts['view'], $atts );
	}
}

if ( ! shortcode_exists( 'contact_info' ) ) {
	add_shortcode( 'contact_info', 'atdtp_shortcode_contact_info' );

	/**
	 * Contact info shortcode rendering function.
	 *
	 * @param  array  $atts     shortcode attributes.
	 * @param  string $content  shortcode content text.
	 * @return string
	 */
	function atdtp_shortcode_contact_info( $atts, $content=null ) {
		$atts = shortcode_atts( array(
			'address' => '',
			'phone' => '',
			'email' => '',
			'skype' => '',
			'view' => '',
		), $atts );

		return ATDTP()->shortcodes_helper()->render_view( 'templates/shortcodes/contact_info', $atts['view'], $atts );
	}
}

if ( ! shortcode_exists( 'mailchimp_form' ) ) {
	add_shortcode( 'mailchimp_form', 'atdtp_shortcode_mailchimp_form' );

	/**
	 * MainChimp form shortcode rendering function.
	 *
	 * @param  array  $atts     shortcode attributes.
	 * @param  string $content  shortcode content text.
	 * @return string
	 */
	function atdtp_shortcode_mailchimp_form( $atts, $content=null ) {
		$atts = shortcode_atts( array(
			'mailchimp_list_id' => '',
			'title' => '',
			'button_text' => '',
			'css_class' => '',
			'width_mode' => 'box-width',
			'bg_url' => '',
			'bg_repeat' => '',
			'view' => '',
		), $atts );

		$atts['content'] = $content;

		return ATDTP()->shortcodes_helper()->render_view( 'templates/shortcodes/mailchimp_form', $atts['view'], $atts );
	}
}

if ( ! shortcode_exists( 'google_map' ) ) {
	add_shortcode( 'google_map', 'atdtp_shortcode_google_map' );

	/**
	 * Google map shortcode rendering function.
	 *
	 * @param  array  $atts     shortcode attributes.
	 * @param  string $content  shortcode content text.
	 * @return string
	 */
	function atdtp_shortcode_google_map( $atts, $content=null ) {
		$atts = shortcode_atts( array(
			'address' => '',
			'coordinates' => '40.764324,-73.973057',
			'zoom' => '10',
			'height' => '400',
			'width_mode' => 'box-width',
			'css_class' => '',
			'view' => '',
		), $atts );

		return ATDTP()->shortcodes_helper()->render_view( 'templates/shortcodes/google_map', $atts['view'], $atts );
	}
}

if ( ! shortcode_exists( 'icon_tick' ) ) {
	add_shortcode( 'icon_tick', 'atdtp_shortcode_icon_tick' );

	/**
	 * Icon tick (+/- check icon) shortcode rendering function.
	 *
	 * @param  array  $atts     shortcode attributes.
	 * @param  string $content  shortcode content text.
	 * @return string
	 */
	function atdtp_shortcode_icon_tick( $atts, $content=null ) {
		$atts = shortcode_atts( array(
			'state' => 'on',
			'view' => '',
		), $atts );

		$helper = ATDTP()->shortcodes_helper();

		$atts['state'] = $helper->attribute_is_true( $atts['state'] );

		return $helper->render_view( 'templates/shortcodes/icon_tick', $atts['view'], $atts );
	}
}

if ( ! shortcode_exists( 'timeline' ) ) {
	add_shortcode( 'timeline', 'atdtp_shortcode_timeline' );

	if ( $shortoces_nl_escaper ) {
		$shortoces_nl_escaper->registerNestedShortcodes( 'timeline','timeline_item' );
	}

	/**
	 * Timeline shortcode rendering function.
	 *
	 * @param  array  $atts     shortcode attributes.
	 * @param  string $content  shortcode content text.
	 * @return string
	 */
	function atdtp_shortcode_timeline( $atts, $content = null ) {
		$atts = shortcode_atts( array(
			'css_class' => '',
			'view' => '',
		), $atts );

		$atts['content'] = $content;
		return ATDTP()->shortcodes_helper()->render_view( 'templates/shortcodes/timeline', $atts['view'], $atts );
	}
}

if ( ! shortcode_exists( 'timeline_item' ) ) {
	add_shortcode( 'timeline_item', 'atdtp_shortcode_timeline_item' );

	/**
	 * Timeline item shortcode rendering function.
	 * Used inside [timeline] shortcode to present information about tour agenda.
	 *
	 * @param  array  $atts     shortcode attributes.
	 * @param  string $content  shortcode content text.
	 * @return string
	 */
	function atdtp_shortcode_timeline_item( $atts, $content = null ) {
		$atts = shortcode_atts( array(
			'item_number' => '',
			'title' => '',
			'view' => '',
		), $atts );

		$atts['content'] = $content;

		return ATDTP()->shortcodes_helper()->render_view( 'templates/shortcodes/timeline_item', $atts['view'], $atts );
	}
}

if ( ! shortcode_exists( 'icons_set' ) ) {
	add_shortcode( 'icons_set', 'atdtp_shortcode_icons_set' );

	/**
	 * Icons set shortcode rendering function.
	 * Container for set [icon_item] shortcodes.
	 *
	 * @param  array  $atts     shortcode attributes.
	 * @param  string $content  shortcode content text.
	 * @return string
	 */
	function atdtp_shortcode_icons_set( $atts, $content = null ) {
		$atts = shortcode_atts( array(
			'row_size' => 3,
			'view' => '',
		), $atts );

		$GLOBALS['__tmp_icons_set'] = array();
		do_shortcode( $content );
		$atts['items'] = $GLOBALS['__tmp_icons_set'];
		unset( $GLOBALS['__tmp_icons_set'] );

		// need improve regexp - [^"]*, as this one does not allow to use " character in text attribute
		/*$items = array();
		if ( preg_match_all('`\[icon_item(?: icon="([^"]*)")?(?: text="([^"]*)")?\]`s', $content, $matches) ) {
			foreach ($matches[1] as $_item_index => $icon_class) {
				$item_text = $matches[2][$_item_index];
				if ( ! $item_text && ! $icon_class ) {
					continue;
				}
				$items[] = array(
					'icon_class' => $icon_class,
					'text' => $item_text,
				);
			}
		}*/

		return ATDTP()->shortcodes_helper()->render_view( 'templates/shortcodes/icons_set', $atts['view'], $atts );
	}
}

if ( ! shortcode_exists( 'icon_item' ) ) {
	add_shortcode( 'icon_item', 'atdtp_shortcode_icon_item' );

	/**
	 * Icon item shortcode rendering function.
	 * Used inside [icons_set] shortcode to preset set of icons.
	 *
	 * @param  array  $atts     shortcode attributes.
	 * @param  string $content  shortcode content text.
	 * @return string
	 */
	function atdtp_shortcode_icon_item( $atts, $content = null ) {
		shortcode_atts( array(
			'icon' => '',
			'title' => '',
		), $atts );
		$atts['content'] = $content;
		$GLOBALS['__tmp_icons_set'][] = $atts;
		return '';
	}
}

if ( ! shortcode_exists( 'gift_card' ) ) {
	add_shortcode( 'gift_card', 'atdtp_shortcode_gift_card' );

	/**
	 * Gift card shortcode rendering function.
	 *
	 * @param  array  $atts     shortcode attributes.
	 * @param  string $content  shortcode content text.
	 * @return string
	 */
	function atdtp_shortcode_gift_card( $atts, $content = null) {
		$atts = shortcode_atts( array(
			'title' => '',
			'button_title' => '',
			'button_link' => '#',
			'view' => '',
		), $atts );

		$atts['content'] = $content;

		return ATDTP()->shortcodes_helper()->render_view( 'templates/shortcodes/gift_card', $atts['view'], $atts );
	}
}
