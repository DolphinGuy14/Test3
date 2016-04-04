<?php
/**
 * Helper for the WC_Product_Tour integration with Woocommerce plugin.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

class WC_Tour_Integration_Helper
{
	/**
	 * Option that defines base url for tour details page.
	 * If empty - tours page will be used, if no - option $tour_rewrite_base_default will be used.
	 *
	 * @see filter_init
	 * @var string
	 */
	public $tour_rewrite_base = '';

	/**
	 * Default value for tour details page.
	 *
	 * @see filter_init
	 * @var string
	 */
	protected $tour_rewrite_base_default = 'tour-item/';

	public $placeholder_image_url;

	public $is_show_woocommerce_title = false;

	/**
	 * @var WC_Tour_Integration_Helper
	 */
	private static $instance;

	protected function __construct() {
		$this->init();
	}

	private function __clone() {
	}

	/**
	 * @return WC_Tour_Integration
	 */
	public static function getInstance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Init method. Adds all hooks for tour integration.
	 *
	 * @return void
	 */
	protected function init() {
		$this->booking_form = adventure_tours_di( 'booking_form' );

		add_filter( 'woocommerce_show_page_title', array( $this, 'filter_woocommerce_show_page_title' ), 19 );
		add_filter( 'woocommerce_page_title', array( $this, 'filter_woocommerce_page_title' ) );
		add_filter('post_type_archive_title', array( $this, 'filter_post_type_archive_title' ), 10, 2);

		add_filter( 'template_include', array( $this, 'filter_template_include' ) );

		add_filter( 'init', array( $this, 'filter_init' ) );

		$own_dir = dirname( __FILE__ );
		if ( is_admin() ) {
			require_once $own_dir . '/WC_Tour_Integration_Helper_Admin.php';
			$adminIntegrator = new WC_Tour_Integration_Helper_Admin();
		} else {
			require_once $own_dir . '/WC_Tour_WP_Query.php';
			$queryIntegrator = new WC_Tour_WP_Query();
			$queryIntegrator->init();

			// filter for checking limits for tour tickets during shopping cart update
			add_action( 'woocommerce_after_cart_item_quantity_update', array( $this, 'filter_woocommerce_after_cart_item_quantity_update' ), 20, 3 );

			add_filter('woocommerce_get_breadcrumb', array( $this, 'filter_woocommerce_get_breadcrumb' ) );

			// tours rating functionality integration
			add_filter( 'woocommerce_product_review_list_args', array( $this, 'filter_woocommerce_product_review_list_args') );
			add_action( 'comment_post', array( $this, 'check_is_tour_rating_comment' ), 20 );

			add_filter( 'woocommerce_output_related_products_args', array( $this, 'filter_woocommerce_output_related_products_args' ) );

			// Removing woocommerce fixer and using own one.
			remove_filter( 'wp_nav_menu_objects', 'wc_nav_menu_item_classes', 2 );
			add_filter( 'wp_nav_menu_objects', array( $this, 'filter_nav_menu_item_classes' ), 2 );
		}
	}

	/**
	 * Filter for init hook.
	 *
	 * @return void
	 */
	public function filter_init(){
		$is_permalinks_on = get_option( 'permalink_structure' ) ? true : false;

		// configurating the tour rewrite base url, if it has not been set up
		if ( ! $this->tour_rewrite_base ) {
			$toursPageId = adventure_tours_get_option( 'tours_page' );
			$this->tour_rewrite_base = $toursPageId ? get_page_uri( $toursPageId ) . '/' : $this->tour_rewrite_base_default;
		}

		if ( $this->tour_rewrite_base ) {
			// move to admins
			add_filter( 'product_rewrite_rules', array( $this, 'filter_product_rewrite_rules' ), 20 );
			if ( $is_permalinks_on ) {
				add_filter( 'post_type_link', array( $this, 'filter_post_type_link' ), 20, 4 );
			}
		}

		if ( ! $this->placeholder_image_url ) {
			$this->placeholder_image_url = adventure_tours_placeholder_img_src();
			if ( $this->placeholder_image_url ) {
				add_filter('woocommerce_placeholder_img_src', array( $this, 'filter_woocommerce_placeholder_img_src' ) );
				/*add_filter('woocommerce_placeholder_img', function($img_html, $size, $dimensions) { return $img_html; });*/
			}
		}
	}

	/**
	 * Filter checks if main query related to the tours and use spec template for tours archive page.
	 *
	 * @param  string $template path to selected template file.
	 * @return string
	 */
	public function filter_template_include($template) {
		if ( $GLOBALS['wp_query']->get( 'is_tour_query' ) ) {
			$template = locate_template( 'templates/tour/archive.php' );
		}
		return $template;
	}

	public function get_tours_page_title() {
		static $title;
		if ( null === $title ) {
			$tourPageId = adventure_tours_get_option( 'tours_page' );
			$title = $tourPageId ? get_the_title( $tourPageId ) : '';
			if ( ! $title ) {
				$title = __( 'Tours', 'adventure-tours' );
			}
		}

		return $title;
	}

	/**
	 * Filter function to fix tour search page title.
	 *
	 * @param  string $label     custom post type label.
	 * @param  string $post_type post type code.
	 * @return string
	 */
	public function filter_post_type_archive_title( $label, $post_type ){
		if ( $post_type == 'product' && is_archive() && adventure_tours_check( 'is_tour_search' ) ) {
			return $this->get_tours_page_title();
		}

		return $label;
	}

	/**
	 * Filters page titles generated by woocommerce plugin.
	 *
	 * @param  string $title page title.
	 * @return string
	 */
	public function filter_woocommerce_page_title($title) {
		if ( is_archive() && adventure_tours_check( 'is_tour_search' ) ) {
			return $this->get_tours_page_title();
		}

		return $title;
	}

	/**
	 * Determines if woocommerce page title should be rendered.
	 *
	 * @return boolean
	 */
	public function filter_woocommerce_show_page_title() {
		return $this->is_show_woocommerce_title;
	}

	/**
	 * Fix active class in nav for shop page.
	 *
	 * @param array $menu_items current set of menu items.
	 * @return array
	 */
	public function filter_nav_menu_item_classes( $menu_items ) {
		if ( ! is_woocommerce() || ! $menu_items ) {
			return $menu_items;
		}

		$isTourQuery = adventure_tours_check( 'is_tour_search' );
		$tourPage = $isTourQuery ? adventure_tours_get_option( 'tours_page' ) : '';

		if ( ! $tourPage ) {
			return wc_nav_menu_item_classes( $menu_items );
		}

		$page_for_posts = (int) get_option( 'page_for_posts' );

		foreach ( $menu_items as $key => $menu_item ) {
			// Unset active class for blog page
			$classes = (array) $menu_item->classes;
			$classes_changed = false;
			if ( $page_for_posts == $menu_item->object_id ) {
				$menu_items[$key]->current = false;

				if ( in_array( 'current_page_parent', $classes ) ) {
					unset( $classes[ array_search( 'current_page_parent', $classes ) ] );
				}

				if ( in_array( 'current-menu-item', $classes ) ) {
					unset( $classes[ array_search( 'current-menu-item', $classes ) ] );
				}
				$classes_changed = true;

				// Set active state if this is the shop page link
			} elseif ( $tourPage == $menu_item->object_id ) {

				$menu_items[ $key ]->current = true;
				$classes[] = 'current-menu-item';
				$classes[] = 'current_page_item';
				$classes_changed = true;
			}

			if ( $classes_changed ) {
				$menu_items[ $key ]->classes = array_unique( $classes );
			}
		}

		return $menu_items;
	}

	/**
	 * Tour special permalink filter.
	 * Add spec rule for tour items.
	 *
	 * @param  assoc $rules wp defined urls.
	 * @return assoc
	 */
	public function filter_product_rewrite_rules($rules) {
		if ( $this->tour_rewrite_base ) {
			$rules = array_merge(
				array(
					$this->tour_rewrite_base . '([^/]+)/comment-page-([0-9]{1,})/?' => 'index.php?product=$matches[1]&cpage=$matches[2]',
					$this->tour_rewrite_base . '(.+)' => 'index.php?product=$matches[1]',
				),
				$rules
			);
		}
		return $rules;
	}

	/**
	 * Tour special permalink filter.
	 * Rewrites product url if it is tour type.
	 *
	 * @param  string  $post_link
	 * @param  WP_Post $post
	 * @param  boolean $leavename
	 * @param  boolean $sample
	 * @return string
	 */
	public function filter_post_type_link($post_link, $post, $leavename, $sample) {
		if ( $this->tour_rewrite_base && 'product' == $post->post_type ) {
			$product = wc_get_product( $post );
			if ( $product->is_type( 'tour' ) ) {
				$post_link = home_url( user_trailingslashit( $this->tour_rewrite_base . $post->post_name ) );
			}
		}
		return $post_link;
	}

	/**
	 * Filter function that returns placeholder image url used by woocommerce plugin.
	 *
	 * @param  string $src
	 * @return string
	 */
	public function filter_woocommerce_placeholder_img_src( $src ) {
		if ($this->placeholder_image_url) {
			return $this->placeholder_image_url;
		}
		return $url;
	}

	/**
	 * Filter for checking limits for tour tickets during shopping cart update action.
	 *
	 * @param  string $cart_item_key
	 * @param  int    $quantity
	 * @param  int    $old_quantity
	 * @return void
	 */
	public function filter_woocommerce_after_cart_item_quantity_update($cart_item_key, $quantity, $old_quantity) {
		if ( $quantity > $old_quantity ) {
			$cart = WC()->cart;
			$item = $cart->get_cart_item( $cart_item_key );
			$product = isset($item['data']) ? $item['data'] : null;
			$booking_date = ! empty( $item['date'] ) ? $item['date'] : null;

			if ( $booking_date && $product && $product->is_type( 'tour' ) ) {
				$booking_form = adventure_tours_di( 'booking_form' );
				$max_quantity = adventure_tours_di( 'tour_booking_service' )->get_open_tickets(
					$product->id,
					$booking_form->convert_date( $booking_form->get_date_format(), $booking_date )
				);

				if ( $quantity > $max_quantity ) {
					wc_add_notice(
						sprintf( esc_html__( 'Only %s tickets available for %s on %s.', 'adventure-tours' ),
							$max_quantity,
							$product->get_title(),
							$booking_date
						),
						'error'
					);
					$cart->set_quantity($cart_item_key, $max_quantity, false );
				}
			}
		}
	}

	/**
	 * Adjust reviews rendering agruments.
	 *
	 * @see    wp_list_comments to get more details about available options
	 * @param  assoc $args
	 * @return assoc
	 */
	public function filter_woocommerce_product_review_list_args( $args ) {
		$args['style'] = 'div';
		return $args;
	}

	/**
	 * Checks if specefied comment is a rating that belongs to the tour item.
	 * Marks such comments with special 'is_tour_rating' comment meta flag. This flag required for separation
	 * reviews related to tours from reviews that belongs to all products.
	 *
	 * @param  int       $comment_id
	 * @param  StdObject $comment    optional comment object
	 * @return bool                  true if comment is tour rating, otherwise returns false
	 */
	public function check_is_tour_rating_comment( $comment_id, $comment = null ) {
		$is_tour_rating = false;
		$meta_val = get_comment_meta( $comment_id, 'rating', true );
		if ( $meta_val >= 0 ) {
			if ( ! $comment ) {
				$comment = get_comment($comment_id);
			}
			$post_id = $comment ? $comment->comment_post_ID : null;
			if ( $post_id && 'product' === get_post_type( $post_id ) ) {
				$product = wc_get_product( $post_id );
				if ( $product && $product->is_type('tour') ) {
					$is_tour_rating = 1;
				}
			}
		}

		if ( $is_tour_rating ) {
			add_comment_meta( $comment_id, 'is_tour_rating', 1, true );
		} else {
			$current_flag_value = get_comment_meta( $comment_id, 'is_tour_rating', true );
			if ( '' !== $current_flag_value ) {
				delete_comment_meta( $comment_id, 'is_tour_rating' );
			}
		}

		return $is_tour_rating;
	}

	/**
	 * Rechecks all tour rating comments and tour rating comments to fix 'is_tour_rating' rating flag value.
	 * Use for repari purposes. Call after init event to rapair flag values:
	 * <pre>
	 * WC_Tour_Integration_Helper::getInstance()->refresh_tour_rating_flags();
	 * </pre>
	 *
	 * @return void
	 */
	public function refresh_tour_rating_flags()
	{
		$checked_map = array();
		$sets_for_check = array();

		// 1 need recheck state of all comments that has flag
		$sets_for_check['marked_comments'] = get_comments( array(
			'meta_key' => 'is_tour_rating',
			'meta_compare' => 'EXISTS',
		) );

		// 2 selecting all comments with 'rating' meta
		$sets_for_check['product_ratings'] = get_comments( array(
			// 'post_type' => 'product',
			'meta_key' => 'rating',
			'meta_compare' => 'EXISTS',
		) );

		foreach ( $sets_for_check as $comments_set ) {
			if ( ! $comments_set ) {
				continue;
			}
			foreach ($comments_set as $item) {
				if ( isset( $checked_map[$item->comment_ID] ) ) {
					continue;
				}

				$this->check_is_tour_rating_comment($item->comment_ID, $item);
				$checked_map[$item->comment_ID] = true;
			}
		}
	}

	/**
	 * Filter for woocommerce bredcrumbs generation function.
	 * To fix breadcrumbs for tours and tour categories.
	 *
	 * @param  array $list
	 * @return array
	 */
	public function filter_woocommerce_get_breadcrumb( $list ) {
		$isTourDetails = false;
		if ( ! empty( $GLOBALS['product'] ) ) {
			$p = wc_get_product();
			if ( $p && $p->is_type( 'tour' ) ) {
				$isTourDetails = true;
			}
		}

		$tour_cat_tax_name = 'tour_category';

		$is_set_tour = $isTourDetails || adventure_tours_check( 'is_tour_search' );
		$is_tour_cat = !$is_set_tour ? is_tax( $tour_cat_tax_name ) : false;

		$tour_element = array();
		if ( $is_set_tour || $is_tour_cat ) {
			$toursPageId = adventure_tours_get_option( 'tours_page' );
			$toursLink = $toursPageId ? get_permalink( $toursPageId ) : '';
			if ($toursLink) {
				$tour_element = array(
					get_the_title( $toursPageId ),
					$toursLink
				);
			}
		}

		if ( $is_set_tour ) {
			if ($tour_element) {
				$first_element = isset($list[1]) ? $list[1] : null;
				$list[1] = $tour_element;
				// case when init list contatins only Home/ Tour X
				if ( $isTourDetails && $first_element && empty($list[2]) && is_single() ) {
					$list[] = $first_element;
				}
			}
		} elseif ( $is_tour_cat ) {
			$new_list = array(
				$list[0],
				$tour_element,
			);

			$current_term = $GLOBALS['wp_query']->get_queried_object();

			$ancestors = get_ancestors( $current_term->term_id, $tour_cat_tax_name );
			$ancestors = array_reverse( $ancestors );

			foreach ( $ancestors as $ancestor ) {
				$ancestor = get_term( $ancestor, $tour_cat_tax_name );
				if ( ! is_wp_error( $ancestor ) && $ancestor ) {
					$new_list[] = array(
						$ancestor->name, get_term_link( $ancestor )
					);
				}
			}
			$new_list[] = array($current_term->name);

			return $new_list;
		}

		return $list;
	}

	/**
	 * Configuration related products section.
	 *
	 * @param  assoc $args
	 * @return assoc
	 */
	public function filter_woocommerce_output_related_products_args( $args ) {
		$args['posts_per_page'] = 3;
		$args['columns'] = 3;
		//$args['orderby'] = 'rand';

		return $args;
	}
}
