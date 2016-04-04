<?php
/**
 * Shortcodes menu definition.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

$toursMenu = __( 'Tours', 'adventure-tours' ) . '.';

$typographyMenu = __( 'Typography', 'adventure-tours' ) . '.';

$tablesMenu = __( 'Tables', 'adventure-tours' ) . '.';

$otherMenu = __( 'Other', 'adventure-tours' ) . '.';

$contactMenu = __( 'Contact', 'adventure-tours' ) . '.';

$externalApiMenu = __( 'External Services', 'adventure-tours' ) . '.';

adventure_tours_di( 'shortcodes_register' )
	->add( '_edit_', __( 'Edit', 'adventure-tours' ) )
	->add( 'row', __( 'Columns', 'adventure-tours' ), array(
		'columns' => '2',
		'css_class' => '',
	))

	->add( 'title', $typographyMenu . __( 'Title', 'adventure-tours' ), array(
		'text' => '',
		'subtitle' => '',
		'size' => array(
			'type' => 'select',
			'values' => array(
				'big',
				'small',
			),
		),
		'position' => array(
			'type' => 'select',
			'values' => array(
				'left',
				'center',
			),
		),
		'decoration' => array(
			'type' => 'boolean',
			'default' => 'on',
		),
		'underline' => array(
			'type' => 'boolean',
			'default' => 'on',
		),
		'style' => array(
			'type' => 'select',
			'values' => array(
				'dark',
				'light',
			),
		),
	))

	->add( 'icon_tick', $typographyMenu . __( 'Icon Tick', 'adventure-tours' ), array(
		'state' => array(
			'type' => 'boolean',
			'default' => 'on',
		),
	))

	->add( 'table', $tablesMenu . __( 'Table', 'adventure-tours' ), array(
		'rows' => '',
		'cols' => '',
		'css_class' => '',
	))
	->add( 'tour_table', $tablesMenu . __( 'Tour Table', 'adventure-tours' ), array(
		'rows' => '',
		'cols' => '',
		'css_class' => '',
	))

	->add( 'tour_search_form', $toursMenu . __( 'Tour Search Form', 'adventure-tours' ), array(
		'css_class' => '',
	))
	->add( 'tour_category_images', $toursMenu . __( 'Tour Category Images', 'adventure-tours' ), array(
		'title' => '',
		'title_underline' => array(
			'type' => 'boolean',
			'default' => 'on',
		),
		'sub_title' => '',
		'parent_id' => '',
		'ignore_empty' => array(
			'type' => 'boolean',
			'default' => 'on',
		),
		'css_class' => '',
	))
	->add( 'tour_category_icons', $toursMenu . __( 'Tour Category Icons', 'adventure-tours' ), array(
		'title' => '',
		'title_underline' => array(
			'type' => 'boolean',
			'default' => 'on',
		),
		'sub_title' => '',
		'parent_id' => '',
		'bg_url' => array(
			'type' => 'image_url',
			'help' => __( 'Select image that should be used as background.', 'adventure-tours' ),
			'default' => '',
		),
		'ignore_empty' => array(
			'type' => 'boolean',
			'default' => 'on',
		),
		'css_class' => '',
	))
	->add( 'tour_carousel', $toursMenu . __( 'Tours Carousel', 'adventure-tours' ), array(
		'title' => '',
		'title_underline' => array(
			'type' => 'boolean',
			'default' => 'on',
		),
		'sub_title' => '',
		'description_words_limit' => '20',
		'tour_category' => array(
			'help' => __( 'Filter items from specific tour category (enter category slug).', 'adventure-tours' ),
			'default' => '',
		),
		'tour_ids' => array(
			'help' => __( 'Specify exact ids of items that should be displayed separated by comma.', 'adventure-tours' ),
			'default' => '',
		),
		'number' => '',
		'css_class' => '',
		'bg_url' => array(
			'type' => 'image_url',
			'help' => __( 'Select image that should be used as background.', 'adventure-tours' ),
			'default' => '',
		),
		'arrow_style' => array(
			'type' => 'select',
			'values' => array(
				'light',
				'dark',
			),
		),
	))
	->add( 'tour_list', $toursMenu . __( 'Tours List', 'adventure-tours' ), array(
		'title' => '',
		'title_underline' => array(
			'type' => 'boolean',
			'default' => 'on',
		),
		'sub_title' => '',
		'description_words_limit' => '20',
		'tour_category' => array(
			'help' => __( 'Filter items from specific tour category (enter category slug).', 'adventure-tours' ),
			'default' => '',
		),
		'tour_ids' => array(
			'help' => __( 'Specify exact ids of items that should be displayed separated by comma.', 'adventure-tours' ),
			'default' => '',
		),
		'number' => '4',
		'css_class' => '',
		'price_style' => array(
			'type' => 'select',
			'values' => array(
				'default',
				'highlighted',
			),
		),
		'show_categories' => array(
			'type' => 'boolean',
			'default' => 'on',
		),
		'btn_more_text' => __( 'View more', 'adventure-tours' ),
		'btn_more_link' => '',
	))
	->add( 'tour_reviews', $toursMenu . __( 'Tour Reviews', 'adventure-tours' ), array(
		'title' => '',
		'title_underline' => array(
			'type' => 'boolean',
			'default' => 'on',
		),
		'number' => '2',
		'css_class' => '',
	))

	->add( 'contact_info', $contactMenu . __( 'Contact Info', 'adventure-tours' ), array(
		'address' => '',
		'phone' => '',
		'email' => '',
		'skype' => '',
	))
	->add( 'social_icons', $contactMenu . __( 'Social Icons', 'adventure-tours' ), array(
		'title' => __( 'We are social', 'adventure-tours' ),
		'facebook_url' => '',
		'twitter_url' => '',
		'googleplus_url' => '',
		'pinterest_url' => '',
		'linkedin_url' => '',
		'instagram_url' => '',
		'dribbble_url' => '',
		'tumblr_url' => '',
		'vk_url' => '',
	))

	->add( 'mailchimp_form', $externalApiMenu . __( 'MailChimp Form', 'adventure-tours' ), array(
		'mailchimp_list_id' => array(
			'required' => true,
		),
		'title' => '',
		'content' => '',
		'button_text' => __( 'Submit', 'adventure-tours' ),
		'css_class' => '',
		'width_mode' => array(
			'type' => 'select',
			'values' => array(
				'box-width',
				'full-width',
			),
		),
		'bg_url' => array(
			'type' => 'image_url',
			'help' => __( 'Select image that should be used as background.', 'adventure-tours' ),
			'default' => '',
		),
		'bg_repeat' => array(
			'type' => 'select',
			'values' => array(
				'repeat',
				'no-repeat',
				'repeat-x',
				'repeat-y',
			),
		),
	))
	->add( 'google_map', $externalApiMenu . __( 'Google Map', 'adventure-tours' ), array(
		'address' => array(
			'help' => __( 'The address will show up when clicking on the map marker.', 'adventure-tours' ),
		),
		'coordinates' => array(
			'help' => __( 'Coordinates separated by comma.', 'adventure-tours' ),
			'default' => '40.764324,-73.973057',
			'required' => true,
		),
		'zoom' => array(
			'help' => __( 'Number in range from 1 up to 21.', 'adventure-tours' ),
			'default' => '10',
			'required' => true,
		),
		'height' => array(
			'default' => '400',
		),
		'width_mode' => array(
			'type' => 'select',
			'values' => array(
				'box-width',
				'full-width',
			),
		),
		'css_class' => '',
	))

	->add( 'latest_posts', $otherMenu . __( 'Latest Posts', 'adventure-tours' ), array(
		'title' => __( 'Latest Posts', 'adventure-tours' ),
		'title_underline' => array(
			'type' => 'boolean',
			'default' => 'on',
		),
		'number' => '1',
		'read_more_text' => __( 'Read more', 'adventure-tours' ),
		'words_limit' => '25',
		'translate' => array(
			'type' => 'boolean',
			'default' => 'on',
		),
	))
	->add( 'timeline', $otherMenu . __( 'Timeline', 'adventure-tours' ), array(
		'content' => '[timeline_item item_number="1" title="Day 1"]Lorem ipsum 1[/timeline_item][timeline_item item_number="2" title="Day 2"]Lorem ipsum 2[/timeline_item]',
		'css_class' => '',
	))
	->add( 'icons_set', $otherMenu . __( 'Icons Set', 'adventure-tours' ), array(
		'row_size' => array(
			'type' => 'select',
			'values' => array( '2', '3', '4' ),
			'default' => 3,
		),
		'content' => join( PHP_EOL, array(
			'[icon_item icon="td-earth" title="Item1"]text[/icon_item]',
			'[icon_item icon="td-heart" title="Item2"]text[/icon_item]',
			'[icon_item icon="td-lifebuoy" title="Item3"]text[/icon_item]',
		)),
	));
