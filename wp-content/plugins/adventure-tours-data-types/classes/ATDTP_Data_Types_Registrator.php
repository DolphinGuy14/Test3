<?php
/**
 * Registers custom post types and taxonomies.
 *
 * @author    Themedelight
 * @package   Themedelight/ATDTP
 * @version   1.0.0
 */

class ATDTP_Data_Types_Registrator
{
	/**
	 * Determines post type used for tour items.
	 *
	 * @see register_tour_category
	 * @var string
	 */
	private $tour_post_type = 'product';

	/**
	 * Internal flag that determines if plugin has been inited.
	 *
	 * @var boolean
	 */
	private $inited = false;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Callback for init event.
	 *
	 * @return void
	 */
	public function init() {
		if ( $this->inited ) {
			return;
		}
		$this->inited = true;

		$this->register_tour_category();
		$this->register_media_categories();
		$this->register_faq();
	}

	/**
	 * Registers 'tour_category' taxonomy tour post type.
	 *
	 * @return void
	 */
	protected function register_tour_category() {
		register_taxonomy(
			'tour_category',
			$this->tour_post_type,
			array(
				'hierarchical' => true,
				'label' => __( 'Tour Categories', 'adventure-tours-data-types' ),
				'singular_name' => __( 'Tour Category', 'adventure-tours-data-types' ),
				'rewrite' => array(
					'slug' => 'tour-category',
					'with_front' => true,
				),
			)
		);
	}

	/**
	 * Registers 'madia_category' taxonomy for attachments.
	 *
	 * @return void
	 */
	protected function register_media_categories() {
		register_taxonomy( 'media_category', 'attachment', array(
			'labels' => array(
				'name' => __( 'Media Categories', 'adventure-tours-data-types' ),
				'singular_name' => __( 'Media Category', 'adventure-tours-data-types' ),
			),
			'hierarchical' => true,
			'show_admin_column' => true,
			'query_var' => false,
		) );
	}

	/**
	 * Registers 'faq' custom post type and 'faq_category' taxonomy.
	 *
	 * @return void
	 */
	protected function register_faq() {
		register_post_type('faq', array(
			'label' => __( 'FAQs', 'adventure-tours-data-types' ),
			'labels' => array(
				'add_new' => __( 'Add New Question', 'adventure-tours-data-types' ),
				'edit_item' => __( 'Edit Question', 'adventure-tours-data-types' ),
			),
			'exclude_from_search' => true,
			'publicly_queryable' => true,
			'public' => true,
			'show_ui' => true,
			'show_in_nav_menus' => false,
			'has_archive' => false,
			'menu_icon' => plugin_dir_url( dirname( __FILE__ ) ) . 'assets/images/ico-faq.png',
			'menu_position' => 9,
			'rewrite' => array(
				'slug' => 'faq',
				'with_front' => false,
			),
			'supports' => array(
				'title',
				'editor',
			),
		));

		register_taxonomy( 'faq_category', 'faq', array(
			'hierarchical' => true,
			'label' => __( 'FAQ Categories', 'adventure-tours-data-types' ),
			'singular_name' => __( 'FAQ Category', 'adventure-tours-data-types' ),
			'rewrite' => true,
			'query_var' => true,
		));
	}
}
