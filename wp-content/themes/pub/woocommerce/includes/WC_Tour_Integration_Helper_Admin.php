<?php
/**
 * Implements hooks for integration the tour entity with woocommerce plugin.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

class WC_Tour_Integration_Helper_Admin
{
	public static $booking_form_nonce_field_name = 'ncs';
	public static $booking_form_nonce_key = 'save_tour_booking';

	public function __construct() {
		$this->init();
	}

	protected function init() {
		add_filter( 'product_type_selector', array( $this, 'filter_product_type' ) );
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'filter_product_data_tabs' ), 20 );
		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'action_general_product_data_tab' ) );

		// tour booking periods management implementation
		add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'filter_woocommerce_product_write_panel_tabs' ), 6 );
		add_action( 'woocommerce_product_write_panels', array( $this, 'filter_woocommerce_product_write_panels' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'filter_woocommerce_process_product_meta' ), 20 );
		add_action( 'wp_ajax_save_tour_booking_periods', array( $this, 'ajax_action_save_tour_booking_periods'), 20 );
		add_action( 'wp_ajax_preview_booking_periods', array( $this, 'ajax_action_preview_booking_periods'), 20 );

		add_filter( 'custom_menu_order', array( $this, 'filter_custom_menu_order' ), 20 );
		add_filter( 'rewrite_rules_array', array( $this, 'filter_rewrite_rules_array' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'filter_admin_enqueue_scripts' ) );
	}

	public function filter_product_type( $types ) {
		$types['tour'] = __( 'Tour', 'adventure-tours' );
		return $types;
	}

	public function filter_product_data_tabs( $tabs ) {
		array_push( $tabs['shipping']['class'], 'hide_if_tour' );
		array_push( $tabs['inventory']['class'], 'show_if_tour' );
		return $tabs;
	}

	/**
	 * Used to make available price and inventory inputs.
	 *
	 * @return void
	 */
	public function action_general_product_data_tab() {
		// .filter('.pricing,.tips')
		echo '<script>jQuery(".show_if_simple").addClass("show_if_tour");</script>';
	}

	/**
	 * Filter function for 'custom_menu_order' filter.
	 * Used for adding new items to 'Products' section and making custom order for them.
	 *
	 * @param  boolean $order flag that indicates that custom order should be used.
	 * @return boolean
	 */
	public function filter_custom_menu_order( $order ) {
		$icons_storage = adventure_tours_di( 'product_attribute_icons_storage' );
		if ( $icons_storage && $icons_storage->is_active() ) {
			include_once dirname( __FILE__ ) . '/WC_Admin_Attributes_Extended.php';
			$extender = new WC_Admin_Attributes_Extended(array(
				'storage' => $icons_storage,
			));
			$extender->hook();
		}

		global $submenu;

		$productsMenu = &$submenu['edit.php?post_type=product'];
		array_unshift($productsMenu, array(
			__( 'Tours', 'adventure-tours' ),
			'edit_products',
			'edit.php?post_type=product&product_type=tour&is_tours_management=1',
		));

		// if currently loaded page is tours management section - adding js that highlight it as active menu item
		// as WP does not provide any other way to have few edit section for same custom post type
		// need improve this
		if ( ! empty( $_GET['is_tours_management'] ) ) {
			TdJsClientScript::addScript( 'activateTourItemMenu', $this->generate_tour_activation_js() );
		} else {
			add_filter('admin_footer-post.php', array( $this, 'filter_admin_footer_for_menu_activation' ) );
		}

		return $order;
	}

	public function filter_admin_footer_for_menu_activation(){
		if ( !empty($_GET['action']) && 'edit' == $_GET['action'] && 'product' == get_post_type() ) {
			$p = wc_get_product( get_post() );
			if ( $p && $p->is_type( 'tour' ) ) {
				echo '<script>jQuery(function(){'. $this->generate_tour_activation_js() .'});</script>';
			}
		}
	}

	protected function generate_tour_activation_js(){
		return <<<SCRIPT
		var activeLi = jQuery("#adminmenu").find("li.current"),
			newActiveLi = activeLi.parent().find("a[href$=\'is_tours_management=1\']").parent();
		if (newActiveLi.length) {
			activeLi.removeClass("current")
				.find("a.current").removeClass("current");
			newActiveLi.addClass("current")
				.find("a").addClass("current");
		}
SCRIPT;
	}

	/**
	 * Creates special rewrite url for tours archive section.
	 *
	 * @param  assoc $rules
	 * @return void
	 */
	public function filter_rewrite_rules_array( $rules ) {
		$newrules = array();

		$toursPageId = adventure_tours_get_option( 'tours_page' );
		if ( $toursPageId && 'page' == get_option( 'show_on_front' ) && $toursPageId == get_option( 'page_on_front' ) ) {
			$toursLink = '';
		} else {
			$toursLink = $toursPageId ? get_page_uri( $toursPageId ) : 'tours';
		}

		$newrules[$toursLink . '/page/([0-9]{1,})/?' ] = 'index.php?toursearch=1&paged=$matches[1]';
		$newrules[$toursLink . '$' ] = 'index.php?toursearch=1';// &post_type=product&product_type=tour

		$rules = $newrules + $rules;

		return $rules;
	}

/*** Tour Booking tab management implementation [start] ***/
	/**
	 * Renders tab name to list of tabs in on the product management page.
	 *
	 * @return void
	 */
	public function filter_woocommerce_product_write_panel_tabs() {
		echo '<li class="advanced_options show_if_tour"><a href="#tour_booking_tab">' . esc_html__( 'Tour Booking', 'adventure-tours' ) . '</a></li>';
	}

	/**
	 * Renders Tour Booking management tab on the product management page.
	 *
	 * @return void
	 */
	public function filter_woocommerce_product_write_panels() {
		wp_enqueue_script( 'theme-tools', PARENT_URL . '/assets/js/ThemeTools.js', array('jquery'), '1.0.0' );
		wp_enqueue_script( 'tour-booking-tab', PARENT_URL . '/assets/js/AdminTourBookingTab.js', array('jquery'), '1.0.0' );

		global $post;
		adventure_tours_render_template_part( 'templates/admin/tour-booking-tab', '', array(
			'periods' => adventure_tours_di( 'tour_booking_service' )->get_rows( $post->ID ),
			'nonce_field' => array(
				'name' => self::$booking_form_nonce_field_name,
				'value' => self::$booking_form_nonce_key,
			),
		) );
	}

	/**
	 * Filter called by woocommerce on the product data saving event.
	 * Saves tour booking periods.
	 *
	 * @param  int $post_id
	 * @return void
	 */
	public function filter_woocommerce_process_product_meta( $post_id ) {
		if ( ! isset( $_POST['tour-booking-row'] ) ) {
			return;
		}
		$this->save_booking_rows( $post_id, $_POST['tour-booking-row'] );
	}

	/**
	 * Ajax action used for saving tour booking periods data.
	 *
	 * @return void
	 */
	public function ajax_action_save_tour_booking_periods() {
		//need implement nonce field
		$post_id = isset( $_POST['booking_tour_id'] ) ? $_POST['booking_tour_id'] : null;
		$rows = isset( $_POST['tour-booking-row'] ) ? $_POST['tour-booking-row'] : null;
		$nonce = isset( $_POST[self::$booking_form_nonce_field_name] ) ? $_POST[self::$booking_form_nonce_field_name] : null;

		$response = array(
			'success' => false,
		);

		if ( $post_id && $rows && wp_verify_nonce( $nonce, self::$booking_form_nonce_key ) ) {
			$saving_errors = $this->save_booking_rows( $post_id, $_POST['tour-booking-row'] );
			if ( empty( $saving_errors ) ) {
				$response['success'] = true;
			} else {
				$response['errors'] = $saving_errors;
			}
		} else {
			$response['errors'] = array(
				'general' => array(
					__( 'Parameters error. Please contact support.', 'adventure-tours' ),
				)
			);
		}

		wp_send_json( $response );
	}

	/**
	 * Ajax action used by 'Preview Calendar' functionality on the tour booking management tab.
	 *
	 * @return void
	 */
	public function ajax_action_preview_booking_periods() {
		//need implement nonce field
		$post_id = isset( $_POST['booking_tour_id'] ) ? $_POST['booking_tour_id'] : null;
		$rows = isset( $_POST['tour-booking-row'] ) ? $_POST['tour-booking-row'] : null;

		$result = adventure_tours_di( 'tour_booking_service' )->expand_periods( $rows, $post_id );

		$response = array(
			'success' => true,
			'data' => $result
		);

		wp_send_json( $response );
	}

	/**
	 * Saves booking periods for specefied post.
	 *
	 * @param  int   $post_id
	 * @param  array $rows
	 * @return assoc
	 */
	protected function save_booking_rows( $post_id, $rows ) {
		return adventure_tours_di( 'tour_booking_service' )->set_rows( $post_id, $rows );
	}

/*** Tour Booking tab management implementation [end] ***/

	/**
	 * Filter for admin enqueue scripts.
	 *
	 * @return void
	 */
	public function filter_admin_enqueue_scripts() {
		$screen = get_current_screen();
		if ( in_array( $screen->id, array( 'product', 'edit-product' ) ) ) {
			wp_enqueue_style( 'tour_admin_style', PARENT_URL . '/assets/admin/manage-product.css', array(), '1.0' );
		}
	}
}
