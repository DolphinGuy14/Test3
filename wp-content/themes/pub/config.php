<?php
/**
 * Main application configuration file.
 * Used to configurate set of services that available in the application.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

return array(
	'app' => array(
		'AtApplication',
	),
	'register' => array(
		'TdRegister',
		array(
			'data' => array(
				'main_less_file' => '/assets/less/main.less',
				'autoinit_services' => array(
					'theme_customizer',
					'image_manager',
					'icons_manager',
				),
			),
		),
	),
	'header_section' => array(
		'AtHeaderSection',
	),
	'breadcrumbs' => array(
		'TdBreadcrumbs',
		array(
			'show_on_home' => false,
			'page_type_formats' => array(
				'home' => __( 'Home', 'adventure-tours' ),
				'category' => __( 'Category %s', 'adventure-tours' ),
				'search' => __( 'Result search "%s"', 'adventure-tours' ),
				'tag' => __( 'Tag "%s"', 'adventure-tours' ),
				'author' => __( 'Author %s', 'adventure-tours' ),
				'404' => __( 'Error 404', 'adventure-tours' ),
				'format' => __( 'Format %s', 'adventure-tours' ),
			),
		),
	),
	'icons_manager' => array(
		'TdFontIconsManager',
		array(
			'font_file_url' => PARENT_URL . '/assets/csslib/adventure-tours-icons.css',
			'pattern' => '/\.(td-(?:\w+(?:-)?)+):before\s*{\s*content/',
			'cache_key' => 'at-font-icons-list',
		),
	),
	'image_manager' => array(
		'TdImageManager',
		array(
			'sizes' => array(
				'thumb_single' => array(
					'width' => 1140,
					'height' => 530, //mockup height is 460
					'crop' => true,
				),
				'thumb_last_posts_widget' => array(
					'width' => 60,
					'height' => 60,
					'crop' => true,
				),
				'thumb_gallery' => array(
					'width' => 720,
					'height' => 480,
					'crop' => true,
				),
				'thumb_last_posts_shortcode' => array(
					'width' => 1140,
					'height' => 760,
					'crop' => true,
				),
				// tour images ratio is 0.841736694678
				'thumb_tour_box' => array(
					'width' => 720,
					'height' => 606,
					'crop' => true,
				),
				'thumb_tour_listing' => array(
					'width' => 720,
					'height' => 480,
					'crop' => true,
				),
				'thumb_tour_medium' => array(
					'width' => 531,
					'height' => 354,
					'crop' => true,
				),
				'thumb_tour_listing_small' => array(
					'width' => 360,
					'height' => 240,
					'crop' => true,
				),
				'thumb_tour_widget' => array(
					'width' => 270,
					'height' => 180,
					'crop' => true,
				),
			),
		),
	), //'image_manager'
	'theme_customizer' => array(
		'AtThemeCustomizer',
	),
	'theme_updater' => array(
		'TdThemeUpdater',
		array(
			'themeName' => 'Adventure Tours',
			'themeId' => 'adventure-tours',
			'cachePrefix' => 'adventure_tours',
			'updatesFileUrl' => 'http://adventure-tours.themedelight.com/adventure-tours-versions.json',
		),
	),
	'shortcodes_helper' => array(
		'AtShortcodesHelperService',
	),
	'shortcodes_register' => array(
		'TdShortcodesRegister',
	),
	'shortcodes_tiny_mce_integrator' => array(
		'TdShortcodesTinyMCEIntegrator',
		array(
			'registerService' => '@shortcodes_register',
		),
	),
	// Storage used by WC_Admin_Attributes_Extended class as a storage.
	'product_attribute_icons_storage' => array(
		'AtSqlStorage',
		array(
			'storage_key' => 'product_attribute_icon',
		),
	),
	'tour_category_display_type_storage' => array(
		'AtSqlStorage',
		array(
			'storage_key' => 'tour_cat_display_type',
		),
	),
	'tour_category_images_storage' => array(
		'AtSqlStorage',
		array(
			'storage_key' => 'tour_cat_thumb_id',
		),
	),
	'tour_category_icons_storate' => array(
		'AtSqlStorage',
		array(
			'storage_key' => 'tour_cat_icon',
		),
	),
	'taxonomy_display_data' => array(
		'AtTaxonomyDisplayTypes',
		array(
			'taxonomies' => array( 'tour_category' ),
			'storage' => '@tour_category_display_type_storage',
			'tableColumnId' => 'td_taxonomy_display_type',
			'fieldLabel' => __( 'Display Mode', 'adventure-tours' ),
			'postVariableFieldData' => 'td_taxonomy_display_type',
			'selectOptions' => array(
				'default' => __( 'Default', 'adventure-tours' ),
				'products' => __( 'Tours', 'adventure-tours' ),
				'subcategories' => __( 'Categories', 'adventure-tours' ),
				'both' => __( 'Both', 'adventure-tours' ),
			),
		),
	),
	'taxonomy_images' => array(
		'AtTaxonomyImages',
		array(
			'taxonomies' => array( 'tour_category' ),
			'storage' => '@tour_category_images_storage',
			'tableColumnId' => 'td_taxonomy_image',
			'fieldLabel' => __( 'Image', 'adventure-tours' ),
			'postVariableFieldData' => 'td_taxonomy_image',
			'imagePlaceholderUrl' => PARENT_URL . '/assets/td/images/td-taxonomy-images-placeholder.png',
			'buttonUploadImageLabel' => __( 'Upload/Add Image', 'adventure-tours' ),
		),
	),
	'taxonomy_icons' => array(
		'AtTaxonomyIcons', 
		array(
			'taxonomies' => array( 'tour_category' ),
			'storage' => '@tour_category_icons_storate',
			'tableColumnId' => 'td_taxonomy_icons',
			'fieldLabel' => __( 'Icon', 'adventure-tours' ),
			'postVariableFieldData' => 'td_taxonomy_icon',
			'iconSize' => '40px',
			'selectOptionNoneLabel' => __( 'None', 'adventure-tours' ),
		),
	),
	'booking_form' => array(
		'AtBookingFormHelper',
	),
	'tour_booking_service' => array(
		'AtTourBookingService',
	),
	'tour_badge_service' => array(
		'AtBadgeService', array(
			'count' => 3,
		),
	),
);
