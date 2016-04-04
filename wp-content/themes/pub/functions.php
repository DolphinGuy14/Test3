<?php
/**
 * Theme core file.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

if ( ! defined( 'THEME_IS_DEV_MODE' ) ) {
	define( 'THEME_IS_DEV_MODE', false );
}

define( 'PARENT_DIR', get_template_directory() );
define( 'PARENT_URL', get_template_directory_uri() );

define( 'TOUR_POST_TYPE', 'product' );

// for page with sidebar
if ( ! isset( $content_width ) ) {
	$content_width = 748;
}

require PARENT_DIR . '/includes/loader.php';

/**
 * Returns dependency injection container/element from container by key.
 *
 * @param  string $key
 * @return mixed
 */
function &adventure_tours_di( $key = null ) {
	static $di;
	if ( ! $di ) {
		$di = new JuiceContainer();
	}
	if ( $key ) {
		$result = $di[ $key ];
		return $result;
	}
	return $di;
}

add_action( 'adventure_tours_init_di', 'adventure_tours_init_di_callback', 10, 2 );
function adventure_tours_init_di_callback($di, $config) {
	if ( $config ) {
		foreach ( $config as $key => $value ) {
			$instance = null;
			$class = '';
			$typeof = gettype( $value );
			switch ( $typeof ) {
			case 'string':
				$class = $value;
				break;

			case 'array':
				$class = array_shift( $value );
				break;

			default:
				$instance = $value;
				$class = get_class( $instance );
				break;
			}
			$diKey = is_string( $key ) ? $key : $class;
			if ( isset( $di[$diKey] ) ) {
				continue;
			}

			$di[$diKey] = $instance ? $instance : JuiceDefinition::create( $class, $value );
		}
	}
}

do_action( 'adventure_tours_init_di', adventure_tours_di(), require PARENT_DIR . '/config.php' );

$autoinit_services = adventure_tours_di( 'register' )->getVar( 'autoinit_services' );
if ( $autoinit_services ) {
	foreach ( $autoinit_services as $service_id ) {
		adventure_tours_di( $service_id );
	}
}

// -----------------------------------------------------------------#
// Theme settings functions
// -----------------------------------------------------------------#
/**
 * Option name used for storing theme settings.
 *
 * @see adventure_tours_get_option
 * @see adventure_tours_filter_after_theme_setup
 */
if ( ! defined( 'VP_OPTION_KEY' ) ) { define( 'VP_OPTION_KEY', 'adventure_tours_theme_options' ); }

// Vafpress framework integration.
if ( ! defined( 'VP_URL' ) ) {
	define( 'VP_URL', PARENT_URL . '/vendor/vafpress' );
}
require PARENT_DIR . '/vendor/vafpress/bootstrap.php';

// Additional vafpress fields implementation.
// VP_AutoLoader::add_directories(PARENT_DIR .'/includes/vafpress-addon/classes', 'VP_'); .
// VP_FileSystem::instance()->add_directories('views', PARENT_DIR .'/includes/vafpress-addon/views'); .
if ( ! function_exists( 'adventure_tours_get_option' ) ) {
	/**
	 * Returns theme option value.
	 *
	 * @param  string $name    option name.
	 * @param  mixed  $default default value.
	 * @return mixed
	 */
	function adventure_tours_get_option($name, $default = null) {
		return vp_option( VP_OPTION_KEY .'.'.$name, $default );
	}
}

if ( ! function_exists( 'adventure_tours_filter_after_theme_setup' ) ) {
	/**
	 * Init theme function.
	 *
	 * @return void
	 */
	function adventure_tours_filter_after_theme_setup() {
		// Initing Vafpress Framework theme options.
		$vp_theme_option = new VP_Option(array(
			// 'is_dev_mode'           => THEME_IS_DEV_MODE,
			'option_key'            => VP_OPTION_KEY,
			'page_slug'             => 'theme_options_page',
			'template'              => PARENT_DIR . '/includes/theme-options-config.php',
			'menu_page'             => 'themes.php',
			'use_auto_group_naming' => true,
			'use_exim_menu'         => true,
			'minimum_role'          => 'edit_theme_options',
			'layout'                => 'fixed',
			'page_title'            => __( 'Theme Options', 'adventure-tours' ),
			'menu_label'            => __( 'Theme Options', 'adventure-tours' ),
		));
		adventure_tours_di( 'register' )->setVar( '_vp_theme_option', $vp_theme_option );

		load_theme_textdomain( 'adventure-tours', PARENT_DIR . '/languages' );

		if ( is_super_admin() && !THEME_IS_DEV_MODE ) {
			if ( adventure_tours_get_option( 'update_notifier' ) ) {
				adventure_tours_di( 'theme_updater' );
			}
		}
	}

	add_action( 'after_setup_theme', 'adventure_tours_filter_after_theme_setup' );
}

if ( ! function_exists( 'adventure_tours_after_theme_options_save' ) ) {
	/**
	 * Callback that called on changes in theme options.
	 *
	 * @param  VP_Option $opt
	 * @param  boolean   $status
	 * @param  string    $option_key saved option key
	 * @return void
	 */
	function adventure_tours_after_theme_options_save( $opt, $status, $option_key ) {
		if ( VP_OPTION_KEY == $option_key && $status ) {
			flush_rewrite_rules();
		}
	}

	add_action( 'vp_option_save_and_reinit', 'adventure_tours_after_theme_options_save', 20, 3 );
}


if ( is_admin() ) {
	require 'admin/plugins.php';

	require 'admin/data-import.php';
}

require 'theme-options-functions.php';

require 'template-functions.php';

// Theme shortcodes.
require 'shortcodes/shortcodes.php';

if ( class_exists( 'woocommerce' ) ) {
	add_theme_support( 'woocommerce' );
	require_once PARENT_DIR . '/woocommerce/woocommerce.php';
}

// -----------------------------------------------------------------#
// Widgets registration
// -----------------------------------------------------------------#
if ( ! function_exists( 'adventure_tours_register_widgets' ) ) {
	/**
	 * Hook for widgets registration.
	 *
	 * @return void
	 */
	function adventure_tours_register_widgets() {
		// Make a Wordpress built-in Text widget process shortcodes.
		add_filter( 'widget_text', 'shortcode_unautop');
		add_filter( 'widget_text', 'do_shortcode', 11);

		register_widget( 'AtWidgetLatestPosts' );

		register_widget( 'AtWidgetContactUs' );

		register_widget( 'AtWidgetTwitterTweets' );

		if ( class_exists( 'woocommerce' ) ) {
			register_widget( 'AtWidgetTours' );
		}

		register_sidebar(array(
			'id'            => 'sidebar',
			'name'          => __( 'Sidebar', 'adventure-tours' ),
			'description'   => __( 'Sidebar located on the right side of blog page.', 'adventure-tours' ),
			'before_widget' => '<div id="%1$s" class="widget block-after-indent %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget__title">',
			'after_title'   => '</h3>',
		));

		register_sidebar(array(
			'id'            => 'shop-sidebar',
			'name'          => __( 'Shop Sidebar', 'adventure-tours' ),
			'description'   => __( 'Sidebar located on the right side on pages related to shop.', 'adventure-tours' ),
			'before_widget' => '<div id="%1$s" class="widget block-after-indent %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget__title">',
			'after_title'   => '</h3>',
		));

		register_sidebar(array(
			'id'            => 'tour-sidebar',
			'name'          => __( 'Tour Sidebar', 'adventure-tours' ),
			'description'   => __( 'Sidebar located on the right side on pages related to tour.', 'adventure-tours' ),
			'before_widget' => '<div id="%1$s" class="widget block-after-indent %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget__title">',
			'after_title'   => '</h3>',
		));

		register_sidebar(array(
			'id'            => 'faq-sidebar',
			'name'          => __( 'FAQs', 'adventure-tours' ),
			'description'   => __( 'Sidebar located on the FAQ page.', 'adventure-tours' ),
			'before_widget' => '<div id="%1$s" class="widget block-after-indent %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget__title">',
			'after_title'   => '</h3>',
		));

		register_sidebar(array(
			'id'            => 'footer1',
			'name'          => __( 'Footer 1', 'adventure-tours' ),
			'description'   => __( 'Located in 1st column on 4-columns footer layout.', 'adventure-tours' ),
			'before_widget' => '<div id="%1$s" class="widget block-after-indent %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget__title">',
			'after_title'   => '</h3>',
		));

		$footerColumnsCount = adventure_tours_get_footer_columns();
		if ( $footerColumnsCount >= 2 ) {
			register_sidebar(array(
				'id'            => 'footer2',
				'name'          => __( 'Footer 2', 'adventure-tours' ),
				'description'   => __( 'Located in 2nd column on 4-columns footer layout.', 'adventure-tours' ),
				'before_widget' => '<div id="%1$s" class="widget block-after-indent %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget__title">',
				'after_title'   => '</h3>',
			));
		}

		if ( $footerColumnsCount >= 3 ) {
			register_sidebar(array(
				'id'            => 'footer3',
				'name'          => __( 'Footer 3', 'adventure-tours' ),
				'description'   => __( 'Located in 3rd column on 4-columns footer layout.', 'adventure-tours' ),
				'before_widget' => '<div id="%1$s" class="widget block-after-indent %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget__title">',
				'after_title'   => '</h3>',
			));
		}

		if ( $footerColumnsCount >= 4 ) {
			register_sidebar(array(
				'id'            => 'footer4',
				'name'          => __( 'Footer 4', 'adventure-tours' ),
				'description'   => __( 'Located in 4th column on 4-columns footer layout.', 'adventure-tours' ),
				'before_widget' => '<div id="%1$s" class="widget block-after-indent %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget__title">',
				'after_title'   => '</h3>',
			));
		}
	}
	add_action( 'widgets_init', 'adventure_tours_register_widgets' );
}

// -----------------------------------------------------------------#
// Asserts registration
// -----------------------------------------------------------------#
if ( ! function_exists( 'adventure_tours_init_theme_asserts' ) ) {
	/**
	 * Defines theme assets.
	 *
	 * @return void
	 */
	function adventure_tours_init_theme_asserts() {
		$minExt = SCRIPT_DEBUG ? '' : '.min';

		if ( THEME_IS_DEV_MODE ) {
			wp_enqueue_style( 'bootstrap-custom', PARENT_URL . '/assets/csslib/bootstrap-custom.css' );
			wp_enqueue_style( 'fontawesome', PARENT_URL . '/assets/csslib/font-awesome.min.css' );
			wp_enqueue_style( 'slicknav', PARENT_URL . '/assets/csslib/slicknav.css' );
			wp_enqueue_style( 'bootstrap-select', PARENT_URL . '/assets/csslib/bootstrap-select.min.css' );
			wp_register_style( 'magnific-popup', PARENT_URL . '/assets/csslib/magnific-popup.css', array(), '1.0.0' );

			wp_register_style( 'swipebox', PARENT_URL . '/assets/csslib/swipebox.css' );
			wp_register_style( 'swiper', PARENT_URL . '/assets/csslib/swiper.min.css' );

			wp_enqueue_script( 'bootstrap', PARENT_URL . '/assets/jslib/bootstrap.min.js',array( 'jquery' ), '',true );
			wp_enqueue_script( 'bootstrap-select', PARENT_URL . '/assets/jslib/bootstrap-select/bootstrap-select.min.js', array( 'jquery', 'bootstrap' ), '', true );
			wp_enqueue_script( 'slicknav', PARENT_URL . '/assets/jslib/jquery.slicknav.js',array( 'jquery' ), '',true );
			wp_enqueue_script( 'tabcollapse', PARENT_URL . '/assets/jslib/bootstrap-tabcollapse.js', array( 'jquery' ), '', true );
			wp_enqueue_script( 'theme', PARENT_URL . '/assets/js/Theme.js', array( 'jquery' ), '', true );
			wp_register_script( 'magnific-popup', PARENT_URL . '/assets/jslib/jquery.magnific-popup.min.js', array( 'jquery' ), '1.0.0', true );

			if ( adventure_tours_get_option( 'show_header_search' ) ) {
				wp_enqueue_style( 'magnific-popup' );
				wp_enqueue_script( 'magnific-popup' );
			}

			wp_register_script( 'swipebox', PARENT_URL . '/assets/jslib/jquery.swipebox.js', array( 'jquery' ), '1.3.0.2', true );
			wp_register_script( 'swiper', PARENT_URL . '/assets/jslib/swiper/swiper.jquery.min.js', array(), '', true );

			wp_register_script( 'parallax', PARENT_URL . '/assets/jslib/jquery.parallax-1.1.3.js', array( 'jquery' ), '1.1.3', true );

			wp_register_script( 'sharrre', PARENT_URL . '/assets/jslib/jquery.sharrre.js', array( 'jquery' ), '',true );
		} else {
			wp_enqueue_style( 'theme-addons', PARENT_URL . '/assets/csslib/theme-addons' . $minExt . '.css', array(), '1.0.0' );
			wp_enqueue_script( 'theme', PARENT_URL . '/assets/js/theme-full' . $minExt . '.js', array( 'jquery' ), '1.0.0', true );
		}

		$styleCollection = apply_filters('get-theme-styles', array(
			'style-css' => get_stylesheet_uri(),
		));

		if ( $styleCollection ) {
			foreach ( $styleCollection as $_itemKey => $resourceInfo ) {
				$_styleText = null;
				$_styleUrl = null;
				if ( ! is_array( $resourceInfo ) ) {
					$_styleUrl = $resourceInfo;
				} else {
					if ( isset( $resourceInfo['text'] ) ) {
						$_styleText = $resourceInfo['text'];
					} elseif ( isset( $resourceInfo['url'] ) ) {
						$_styleUrl = $resourceInfo['url'];
					}
				}
				if ( $_styleUrl ) {
					wp_enqueue_style( $_itemKey, $_styleUrl );
				} elseif ( $_styleText ) {
					adventure_tours_di( 'register' )->pushVar('header_inline_css_text', array(
						'id' => $_itemKey,
						'text' => $_styleText,
					));
				}
			}
		}

		// wp_register_style( 'animate', PARENT_URL . '/assets/csslib/animate.css' );

		wp_register_script( 'jPages', PARENT_URL . '/assets/jslib/jPages.js', array( 'jquery' ), '', true );


		// wp_register_style( 'jquery-ui-datepicker-custom', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css', array(), '1.8.2' );
		wp_register_style( 'jquery-ui-datepicker-custom', PARENT_URL . '/assets/csslib/jquery-ui-custom/jquery-ui.min.css', array(), '1.11.4' );
	}

	add_action( 'wp_enqueue_scripts', 'adventure_tours_init_theme_asserts' );
}

add_theme_support( 'title-tag' );
add_theme_support( 'automatic-feed-links' );

add_theme_support( 'post-thumbnails' );

add_theme_support( 'menus' );
register_nav_menus(array(
	'header-menu' => __( 'Header Menu', 'adventure-tours' ),
	'footer-menu' => __( 'Footer Menu', 'adventure-tours' ),
));

add_theme_support( 'html5', array( 'gallery', 'caption') );

// -----------------------------------------------------------------#
// Rendering: filters & helpers
// -----------------------------------------------------------------#
if ( ! function_exists( 'adventure_tours_render_header_resources' ) ) {
	/**
	 * Renders theme header resources.
	 *
	 * @return void
	 */
	function adventure_tours_render_header_resources() {
		$inlinePices = adventure_tours_di( 'register' )->getVar( 'header_inline_css_text' );
		if ( $inlinePices ) {
			foreach ( $inlinePices as $inlinePiceInfo ) {
				if ( empty( $inlinePiceInfo['text'] ) ) {
					continue;
				}
				printf( "<style type=\"text/css\">%s</style>\n", $inlinePiceInfo['text'] );
			}
			adventure_tours_di( 'register' )->setVar( 'header_inline_css_text', array() );
		}

		$customCss = adventure_tours_get_option( 'custom_css_text' );
		if ( $customCss ) {
			printf( "<style type=\"text/css\">%s</style>\n", $customCss );
		}
	}
	add_action( 'wp_head', 'adventure_tours_render_header_resources' );
}

if ( ! function_exists( 'adventure_tours_filter_theme_styles' ) ) {
	/**
	 * Filter for theme style files list.
	 *
	 * @param  array $defaultSet list of default files that should be used.
	 * @return array
	 */
	function adventure_tours_filter_theme_styles(array $defaultSet) {
		$isCustomizeRequest = isset( $_POST['wp_customize'] ) && 'on' == $_POST['wp_customize'];

		$cacheId = $isCustomizeRequest || THEME_IS_DEV_MODE ? '' : 'adventure_tours_generated_styles_list';
		$cachedValue = $cacheId ? get_transient( $cacheId ) : false;
		if ( false == $cachedValue ) {
			$app = adventure_tours_di( 'app' );
			$styleOptions = $app->getStyleOptions( $isCustomizeRequest );
			// Special variable used to point url locations.
			if ( ! isset( $styleOptions['assetsUrl'] ) ) {
				$styleOptions['assetsUrl'] = '"' . PARENT_URL . '/assets/"';
			}

			$cachedValue = $app->generateCustomCss(
				adventure_tours_di( 'register' )->getVar( 'main_less_file' ),
				$styleOptions,
				$isCustomizeRequest ? 'preview-main' : 'main-custom'
			);

			$cachedValue = array_merge( $defaultSet, $cachedValue );
			if ( $cacheId ) {
				set_transient( $cacheId, $cachedValue );
			}
		}

		return $cachedValue ? $cachedValue : $defaultSet;
	}
	add_filter( 'get-theme-styles', 'adventure_tours_filter_theme_styles', 1, 1 );
}

if ( ! function_exists( 'adventure_tours_flush_style_cache' ) ) {
	/**
	 * Resets generated styles cache.
	 *
	 * @return void
	 */
	function adventure_tours_flush_style_cache() {
		delete_transient( 'adventure_tours_generated_styles_list' );
	}
	add_action( 'customize_save_after', 'adventure_tours_flush_style_cache' );
}

if ( ! function_exists( 'adventure_tours_get_tour_booking_range' ) ) {
	/**
	 * Returns range during that booking for specefied tour can be done.
	 *
	 * @param  int $tour_id
	 * @return assoc        contains 'start' and 'end' keys with dates during that booking is active
	 */
	function adventure_tours_get_tour_booking_range( $tour_id ) {
		static $start_days_in, $length;
		if ( null == $start_days_in ) {
			$start_days_in = (int) adventure_tours_get_option( 'tours_booking_start' );
			$length = (int) adventure_tours_get_option( 'tours_booking_length' );
			if ( $start_days_in < 0 ) {
				$start_days_in = 0;
			}
			if ( $length < 1 ) {
				$length = 1;
			}
		}

		$min_time = strtotime( '+' . $start_days_in . ' day' );
		$max_time = strtotime( '+' . $length . ' day', $min_time );

		return array(
			'start' => date( 'Y-m-d', $min_time ),
			'end' => date( 'Y-m-d', $max_time ),
		);
	}
}

if ( ! function_exists( 'adventure_tours_action_init' ) ) {
	/**
	 * Callback for 'init' action.
	 *
	 * @return void
	 */
	function adventure_tours_action_init() {
		if ( adventure_tours_check( 'tour_category_taxonomy_exists' ) ) {
			// Initing services related on images and icons processing for tour_category taxonomy.
			adventure_tours_di( 'taxonomy_display_data' )->init();
			adventure_tours_di( 'taxonomy_images' )->init();
			adventure_tours_di( 'taxonomy_icons' )->init();
		}
	}
	add_action( 'init', 'adventure_tours_action_init' );
}

if ( ! function_exists( 'adventure_tours_action_after_theme_setup' ) ) {
	/**
	 * Callback for 'after_setup_theme' action.
	 * Creates metaboxes for for pages and tours.
	 *
	 * @return void
	 */
	function adventure_tours_action_after_theme_setup() {
		new VP_Metabox(array(
			'id'           => 'tour_tabs_meta',
			'types'        => array( TOUR_POST_TYPE ),
			'title'        => __( 'Tour Data', 'adventure-tours' ),
			'priority'     => 'high',
			'is_dev_mode'  => false,
			'template'     => PARENT_DIR . '/includes/metabox/tour-tabs-meta.php',
		));

		new VP_Metabox(array(
			'id'           => 'header_section_meta',
			'types'        => array( 'page', 'post', TOUR_POST_TYPE ),
			'title'        => __( 'Header Section', 'adventure-tours' ),
			'priority'     => 'high',
			'is_dev_mode'  => false,
			'template'     => PARENT_DIR . '/includes/metabox/header-section-meta.php',
		));
	}
	add_action( 'after_setup_theme', 'adventure_tours_action_after_theme_setup' );
}

if ( ! function_exists( 'adventure_tours_check' ) ) {
	function adventure_tours_check( $check_name, $ignore_cache = false ) {
		static $cache = array();

		if ( ! isset( $cache[ $check_name ] ) || $ignore_cache ) {
			$result = false;

			switch( $check_name ) {
			case 'is_single_tour':
				if ( is_single() && 'product' == get_post_type() && function_exists( 'wc_get_product' ) ) {
					$product = wc_get_product();
					$result = $product && $product->is_type( 'tour' );
				}
				break;

			case 'is_tour_search':
				return $GLOBALS['wp_query']->get('is_tour_query');
				break;

			case 'tour_category_taxonomy_exists':
				$result = taxonomy_exists( 'tour_category' );
				break;

			case 'media_category_taxonomy_exists':
				$result = taxonomy_exists( 'media_category' );
				break;

			case 'faq_taxonomies':
				$result = taxonomy_exists( 'faq_category' ) && post_type_exists( 'faq' );
				break;

			case 'tours_active':
				$result = class_exists( 'woocommerce' );
				break;
			}

			$cache[ $check_name ] = $result;
		}

		return $cache[ $check_name ];
	}
}
