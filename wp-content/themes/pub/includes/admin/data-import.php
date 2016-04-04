<?php
class AdventureImport {

	public $capability = 'manage_options';

	public $ajax_action_name = 'theme_import';

	/**
	 * Array used for posts filtering during the import.
	 *
	 * @see filter_post_for_import
	 * @var array
	 */
	protected $filter_types_only = array();

	protected $allow_product_terms = false;

	protected $allow_categories_import = false;

	protected $woo_terms = array();

	private $allowed_import_types = array(
		'post',
		'page',
		'product',
		'configurate_woocommerce',
		'theme_options',
		//'theme_widgets',
	);

	public function __construct() {
		if ( is_admin() ) {
			$this->init();
		}

		add_action( 'wp_ajax_' . $this->ajax_action_name, array( $this, 'ajax_handler' ) );
	}

	public function get_import_settings( $with_full_check = false ) {
		$posts_xml_file = 'demo-data-full.xml';

		$cfg = array(
			'post' => array(
				'enabled' => true,
				'title' => 'Posts',
				'description' => 'Imports blog posts and categories from the demo site.',
				//'file' => $this->locate_import_file( 'demo-data-post.xml' ),
				'file' => $this->locate_import_file( $posts_xml_file ),
			),
			'page' => array(
				'enabled' => true,
				'title' => 'Pages',
				'description' => 'Imports pages from the demo site.',
				//'file' => $this->locate_import_file( 'demo-data-page.xml' ),
				'file' => $this->locate_import_file( $posts_xml_file ),
			),
			'product' => array(
				'enabled' => true,
				'title' => 'Tours and Products',
				'description' => 'Imports tours and products from the demo site.',
				//'file' => $this->locate_import_file( 'demo-data-product.xml' ),
				'file' => $this->locate_import_file( $posts_xml_file ),
				'file_adventure_addons' => $this->locate_import_file( 'demo-data-adv-categories-addons.json' ),
			),
			'configurate_woocommerce' => array(
				'enabled' => true,
				'title' => 'Configurate Woocommerce',
				'description' => 'Configurates right image sizes for shop section and the products in the widgets.',
				'file' => $this->locate_import_file( 'demo-data-woocommerce-options.json' ),
			),
			'theme_options' => array(
				'enabled' => true,
				'title' => 'Theme Options',
				'description' => 'Updates values in "Appearance" > "Theme Options" section.<br><b>NOTE:</b> will reset all your changes in Theme Options section.',
				'file' => $this->locate_import_file( 'demo-data-theme-options.json' ),
			),
			/*'theme_widgets' => array(
				'enabled' => false,
				'title' => 'Theme Widgets',
				'description' => '',
				'file' => ''
			),*/
		);

		foreach ($cfg as $type => $options) {
			$available_info = $this->check_import_type_requirements($type, $options, $with_full_check);
			if ( ! $available_info ) {
				$available_info['available'] = false;
			}

			foreach ($available_info as $key => $value) {
				$cfg[$type][$key] = $value;
			}
		}

		return $cfg;
	}

	protected function check_import_type_requirements( $type, $options, $full_check = false ) {
		$errors = array();

		$allowed_types = $this->allowed_import_types;

		if ( empty( $options['enabled'] ) ) {
			$errors[] = 'This option is disabled.';
		} elseif ( ! in_array($type, $allowed_types) ) {
			$errors[] = 'Unknown import type.';
		}

		if ( ! $errors ) {
			if ( !empty( $options['file'] ) && !file_exists( $options['file'] ) ) {
				$errors[] = 'Import file is missed. Please contact support.';
			}
			if ( !empty( $options['file_adventure_addons'] ) && !file_exists( $options['file_adventure_addons'] ) ) {
				$errors[] = 'Import file is missed. Please contact support.';
			}
		}

		switch ($type) {
		case 'page':
		case 'post':
		case 'product':
			//wordpress-importer
			if ( $full_check ) {
				$import_plugin_file = 'wordpress-importer/wordpress-importer.php';
				if ( ! is_plugin_active( $import_plugin_file ) ) { 
					$errors[] = 'Please install "WordPress Importer" plugin (slug is "wordpress-importer").';
				}
				if ( ! class_exists('WP_Import') ) {

					/*if (!defined('WP_LOAD_IMPORTERS') ) {
						define('WP_LOAD_IMPORTERS', true);
						include ABSPATH . '/wp-content/plugins/' . $import_plugin_file;
					}*/

					if ( ! class_exists('WP_Import') ) {
						$errors[] = 'Can not load WP_Import class.';
					}
				}
			}

			if ( 'product' == $type ) {
				if ( ! class_exists( 'woocommerce' ) ) {
					$errors[] = 'Please install and activate Woocommerce plugin.';
				}

				if ( ! adventure_tours_check( 'tour_category_taxonomy_exists' ) ) {
					$errors[] = 'Please install and activate Data types for Adventure Tours theme plugin.';
				}
			}
			break;

		case 'configurate_woocommerce':
			if ( ! class_exists( 'woocommerce' ) ) {
				$errors[] = 'Please install and activate Woocommerce plugin.';
			}
			break;
		}

		$result = array(
			'available' => empty( $errors )
		);

		if ( $errors ) {
			$result['errors'] = $errors;
		}

		return $result;
	}

	protected function locate_import_file( $file ) {
		static $data_folder_path;
		if ( null === $data_folder_path) {
			$data_folder_path = get_template_directory() . '/includes/data/demo/';
		}
		return $file ? $data_folder_path . $file : null;
	}

	protected function init() {
		add_action( 'admin_menu', array( $this, 'action_admin_menu' ) );
	}

	public function action_admin_menu() {
		add_management_page(
			'Adventure Tours Import',
			'Adv. Tours Import',
			$this->capability,
			'adventure_tours_import',
			array( $this, 'render_page' )
		);
	}

	public function render_page() {

		$results = array();

		if ( !empty( $_GET['import'] ) ) {
			$results = $this->do_imports( !empty($_POST['do_import_type']) ? $_POST['do_import_type'] : array() );

			// tmp solution to fix permalinks in case if theme options have been updated
			//flush_rewrite_rules();

			$need_resave_permalinks = array('product', 'page', 'theme_options');

			foreach ($need_resave_permalinks as $key) {
				if ( isset($results[$key]) ) {
					$results[$key] .= '<br><b>NOTE:</b> Please resave your settings for section "Settings" > "Permalinks".';
					break;
				}
			}
		}

		adventure_tours_render_template_part( 'includes/admin/views/import', '', array(
			'gateways' => $this->get_import_settings(),
			// 'form_action' => admin_url('admin-ajax.php') . '?import=1&action=' . $this->ajax_action_name
			'form_action' => '?page=adventure_tours_import&import=1',
			'results' => $results
		) );
	}

	public function ajax_handler() {
		$r = $this->do_imports( !empty($_POST['do_import_type']) ? $_POST['do_import_type'] : array() );

		echo wp_json_encode( $r );
		die();
	}

	// Import functions START
	protected function do_imports( $imports ) {
		$checks_status = $this->get_import_settings(1);

		$result = array();

		if ( $imports ) {
			foreach ($imports as $type) {
				$type_cfg = !empty( $checks_status[$type] ) ? $checks_status[$type] : array();
				if ( !empty( $type_cfg['enabled'] ) ) {
					try {
						switch ($type) {
						case 'post':
						case 'page':
							$allowed_types = array($type);
							if ( 'page' == $type ) {
								$allowed_types[] = 'wpcf7_contact_form';
							}
							$r = $this->do_import_posts( $type_cfg, $allowed_types );
							break;

						case 'product':
							$allowed_types = array($type);
							$allowed_types[] = 'product_variation';

							$r = $this->do_import_posts( $type_cfg, $allowed_types );
							// complete woocommerce and icons
							break;

						case 'configurate_woocommerce':
							$r = $this->do_import_configurate_woocommerce( $type_cfg );
							break;

						case 'theme_options':
							$r = $this->do_import_theme_options( $type_cfg );
							break;

						default:
							$r = __( 'System error. Please contact support', 'adventure-tours' );
							break;
						}

						$r = preg_replace('/Failed to import pa_[^<]+<br\s?\/?>/', '', $r);
						$r = preg_replace('/Failed to import Media[^<]+<br\s?\/?>/', '', $r);

						$result[$type] = $r;
					} catch (Exception $e) {
						$result[$type] = $e->getMessage();
						$result['errors'][$type] = true;
					}
				}
			}
		}

		return $result;
	}

	protected function do_import_posts( $cfg, $type = null ) {
		$file = $cfg['file'];

		$import_adventure_data = false;

		$this->filter_types_only = (array) $type;
		$filter_posts_hook = array( $this, 'filter_wp_import_posts' );
		add_filter( 'wp_import_posts', $filter_posts_hook );

		if ( !$this->filter_types_only || in_array( 'post', $this->filter_types_only ) ) {
			$this->allow_categories_import = true;
		}
		$filter_categories_hook = array( $this, 'filter_wp_import_categories' );
		add_filter( 'wp_import_categories', $filter_categories_hook );

		if ( !$this->filter_types_only || in_array( 'product', $this->filter_types_only ) ) {
			$this->allow_product_terms = true;
			$import_adventure_data = !empty( $cfg['file_adventure_addons'] );
		}
		$filter_terms_hook = array( $this, 'filter_wp_import_terms' );
		add_filter( 'wp_import_terms', $filter_terms_hook );

		$filter_postmeta_key = array( $this, 'filter_wp_postmeta_key' );
		add_filter( 'import_post_meta_key', $filter_postmeta_key, 20, 3 );

		// required for vafpress
		$GLOBALS['wp_import'] = $import = new WP_Import();

		$wp_import = $import;
		ob_start();
		$import->fetch_attachments = false;
		$import->import( $file );

		if ( $import_adventure_data ) {
			$this->do_import_adventure_data( $cfg['file_adventure_addons'] );
		}

		$output = ob_get_clean();

		remove_filter( 'wp_import_posts', $filter_posts_hook );
		remove_filter( 'wp_import_categories', $filter_categories_hook );
		remove_filter( 'wp_import_terms', $filter_terms_hook );
		remove_filter( 'import_post_meta_key', $filter_postmeta_key, 20 );

		return $output;
	}

	protected function do_import_adventure_data( $file ) {
		$import_data = array();

		$content = file_get_contents( $file );
		if ( ! $content ) {
			return;
		}
		$import_data = json_decode( $content );

		foreach( $import_data as $storage_key => $storage_data ) {
			if ( ! $storage_data ) {
				continue;
			}

			$ad_storages = array(
				'product_attribute_icon' => 'product_attribute_icons_storage',
				'tour_cat_display_type' => 'tour_category_display_type_storage',
				'tour_cat_thumb_id' => 'tour_category_images_storage',
				'tour_cat_icon' => 'tour_category_icons_storate',
			);

			$ab_storage = !empty( $ad_storages[ $storage_key ] ) ? adventure_tours_di( $ad_storages[ $storage_key ] ) : null;
			if ( ! $ab_storage ) {
				continue;
			}

			switch( $storage_key ) {
			case 'product_attribute_icon':
				delete_transient( 'wc_attribute_taxonomies' );
				$attributes = wc_get_attribute_taxonomies();
				if ( ! $attributes ) {
					continue;
				}

				foreach ($storage_data as $storage_data_item_slug => $storage_data_item_val) {
					foreach( $attributes as $attribute ) {
						if ( $storage_data_item_slug == $attribute->attribute_name ) {
							if ( ! $ab_storage->getData( $attribute->attribute_id ) ) {
								$ab_storage->setData( $attribute->attribute_id, $storage_data_item_val );
							}
						}
					}
				}
				break;

			//case 'tour_cat_thumb_id' :
			case 'tour_cat_display_type':
			case 'tour_cat_icon' :
				$tour_categories = get_terms( 'tour_category' );
				if ( ! $tour_categories ) {
					continue;
				}
				foreach( $storage_data as $storage_data_item_slug => $storage_data_item_val ) {
					foreach( $tour_categories as $category ) {
						if ( $storage_data_item_slug == $category->slug ) {
							if ( ! $ab_storage->getData( $category->term_id ) ) {
								$ab_storage->setData( $category->term_id, $storage_data_item_val );
							}
						}
					}
				}
				break;
			}
		}
	}

	/**
	 * Filter function for the 'wp_import_posts' filter.
	 * Filters posts during the import process.
	 *
	 * @param  array $posts
	 * @return array
	 */
	public function filter_wp_import_posts( $posts ) {
		foreach ($posts as $key => $post) {
			if ( ! $this->filter_post_for_import( $post ) ) {
				unset($posts[$key]);
			}
		}

		return $posts;
	}

	/**
	 * Desides if post can be imported or not.
	 *
	 * @see    filter_wp_import_posts
	 * @param  assoc   $post
	 * @return boolean
	 */
	protected function filter_post_for_import( $post ) {
		$type = $post['post_type'];
		if ( 'attachment' == $type ) {
			return true;
		}
		// nav_menu_item
		$status = $post['status'];
		if ( 'publish' != $status ) {
			return false;
		}

		if ( $this->filter_types_only && !in_array( $type, $this->filter_types_only ) ) {
			return false;
		}

		return true;
	}

	public function filter_wp_import_categories( $categories ) {
		if ( ! $this->allow_categories_import ) {
			return array();
		} else {
			return $categories;
		}
	}

	public function filter_wp_import_terms( $terms ) {
		$this->woo_terms = array();

		foreach ($terms as $key => $term) {
			$remove = false;

			if ( 'faq_category' == $term['term_taxonomy'] ) {
				$remove = true;
			}

			if ( !$remove && ! $this->allow_product_terms ) {
				if ( in_array( $term['term_taxonomy'], array('tour_category', 'product_cat') ) ) {
					$remove = true;
				}

				if ( !$remove && preg_match('/^pa_/', $term['term_taxonomy']) ) {
					$remove = true;
				}
			}

			if ( $remove ) {
				unset($terms[$key]);
			} else {
				if ( strstr( $term['term_taxonomy'], 'pa_' ) ) {
					$this->woo_terms[] = $term;
				}
			}
		}

		if ( $this->woo_terms ) {
			$this->register_woocommerce_attributes();
		}

		return $terms;
	}

	public function filter_wp_postmeta_key( $key, $post_id, $post ) {
		$ignore_meta_keys = array(
			'_thumbnail_id', '_product_image_gallery', 'header_section_meta'
		);
		if ( in_array( $key, $ignore_meta_keys ) ) {
			return false;
		}
		return $key;
	}

	protected function register_woocommerce_attributes() {
		if ( ! $this->woo_terms ) {
			return;
		}

		global $wpdb;

		foreach ( $this->woo_terms as $key => $term ) {
			//$domain = $term['domain'];
			$domain = $term['term_taxonomy'];

			if ( ! taxonomy_exists( $domain ) ) {

				$nicename = strtolower( sanitize_title( str_replace( 'pa_', '', $domain ) ) );

				$exists_in_db = $wpdb->get_var( $wpdb->prepare( "SELECT attribute_id FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = %s;", $nicename ) );

				// Create the taxonomy
				if ( ! $exists_in_db ) {
					$wpdb->insert( $wpdb->prefix . "woocommerce_attribute_taxonomies", 
						array(
							'attribute_name' => $nicename,
							'attribute_label' => ucwords( str_replace('-', '', $nicename) ),
							'attribute_type' => 'select', //'text', 
							'attribute_orderby' => 'menu_order',
							'attribute_public' => 0,
						), array( '%s', '%s' , '%s', '%s' )
					);
				}

				// Register the taxonomy now so that the import works!
				register_taxonomy(
					$domain,
					apply_filters( 'woocommerce_taxonomy_objects_' . $domain, array( 'product' ) ),
					apply_filters( 'woocommerce_taxonomy_args_' . $domain, array(
						'hierarchical' => true,
						'show_ui' => false,
						'query_var' => true,
						'rewrite' => false,
					) )
				);
			}
		}

		delete_transient( 'wc_attribute_taxonomies' );
	}


	protected function do_import_configurate_woocommerce( $cfg ) {
		$source_file = $cfg['file'];

		$action_config = json_decode( file_get_contents( $source_file ), true );
		ob_start();
		if ( !empty( $action_config['options'] ) ) {
			foreach ($action_config['options'] as $option_name => $option_value ) {
				update_option( $option_name, $option_value );
			}
		}

		return 'Woocommerce options have been updated';
	}

	protected function do_import_theme_options( $cfg ) {
		$theme_options_file = $cfg['file'];

		$theme_option_component = adventure_tours_di( 'register' )->getVar( '_vp_theme_option' );
		if ( ! $theme_option_component ) {
			throw new Exception( '[di500] Data import error. Please contact support.' );
		}
		$theme_option_values = json_decode( file_get_contents( $theme_options_file ), true );
		if ( ! $theme_option_values ) {
			throw new Exception( '[di501] Theme options parsing error. Please contact support.' );
		}

		$theme_option_component->init_options_set();
		$theme_option_component->init_options();

		$set = $theme_option_component->get_options_set();

		$tour_page_id = adventure_tours_get_option('tours_page');
		$tour_page_instance = $tour_page_id ? get_page( $tour_page_id ) : null;
		if ( ! $tour_page_instance ) {
			$tour_page_instance = get_page_by_path('tours', OBJECT, 'page');
			if ( $tour_page_instance ) {
				$tour_page_id = $tour_page_instance->ID;
			}
		}
		$theme_option_values['tours_page'] = $tour_page_id;

		// populate new values
		$theme_option_component->get_options_set()->populate_values( $theme_option_values, false );

		$theme_options_saving_result = $theme_option_component->save_and_reinit();

		if ( true != $theme_options_saving_result['status'] ) {
			throw new Exception( '[to503] ' . $theme_options_saving_result['message'] );
		} else {
			$saved_theme_values = $theme_option_component->get_options_set()->get_values();
		}

		return 'Settings have been imported.';
	}
}

new AdventureImport();
