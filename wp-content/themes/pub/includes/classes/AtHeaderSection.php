<?php
/**
 * Component for handling page header section settings.
 * Require vaffpress framework.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

class AtHeaderSection extends TdComponent
{
	public $page_meta_key = 'header_section_meta';

	public function get_section_meta() {
		$meta_key = $this->page_meta_key;
		$section_meta = array();

		if ( $meta_key ) {
			$is_single = is_singular();
			$banner_page_id = 0;
			if ( $is_single ) {
				$banner_page_id = get_the_ID();
			} else {
				if ( adventure_tours_check( 'is_tour_search' ) ) {
					// static page for tours
					$banner_page_id = adventure_tours_get_option( 'tours_page' );
				} elseif ( is_home() ) {
					$banner_page_id = get_option( 'page_for_posts' );
				} elseif ( is_post_type_archive('product') ) {
					$banner_page_id = wc_get_page_id( 'shop' );
				}
			}

			$metaObject = $banner_page_id > 0 ? vp_metabox( $meta_key, null, $banner_page_id ) : null;
			if ( $metaObject && $metaObject->meta ) {
				$section_meta = $metaObject->meta;
			}

			if ( ! $is_single && empty( $section_meta ) ) {
				$default_image_url = adventure_tours_get_option( 'banner_default_image' );
				$section_meta['section_mode'] = $default_image_url ? 'banner' : 'hide';
				$section_meta['banner_image'] = $default_image_url;
				$section_meta['banner_subtitle'] = adventure_tours_get_option( 'banner_default_subtitle' );
				$section_meta['is_banner_image_parallax'] = adventure_tours_get_option( 'is_banner_default_image_parallax' );
				$section_meta['banner_image_repeat'] = adventure_tours_get_option( 'banner_default_image_repeat' );
				$section_meta['banner_mask'] = adventure_tours_get_option( 'banner_default_mask' );
			}
		}

		$section_meta['title'] = $this->get_title();
		return $section_meta;
	}

	public function get_title() {
		$separator = '';

		// Disabling 'title-tag' feature.
		$activate_title_tag_back = false;
		if ( get_theme_support( 'title-tag' ) ) {
			remove_theme_support( 'title-tag' );
			$activate_title_tag_back = true;
		}

		$q = $GLOBALS['wp_query'];
		if ( $q->get( 'wc_query' ) && function_exists( 'woocommerce_page_title' ) ) {
			if ( $separator ) { 
				$separator = ''; 
			}
			$title = woocommerce_page_title( false );
		} else {
			$title = wp_title( $separator, false );
		}

		// Restoring 'title-tag' feature.
		if ( $activate_title_tag_back ) {
			//add_theme_support( 'title-tag' );
			$GLOBALS['_wp_theme_features']['title-tag'] = true;
		}

		if ( empty( $title ) ) {
			$is_home = is_home();
			$is_front_page = is_front_page();

			if ( $is_front_page && $is_home ) {
				$title = get_bloginfo( 'name' );
			} elseif ( $is_home ) {
				$title = get_the_title( get_option( 'page_for_posts' ) );
			} elseif ( $is_front_page ) {
				$title = get_the_title( get_option( 'page_on_front' ) );
			}
		} elseif ( $separator ) {
			$title = substr( $title, strlen( $separator ) + 1 );
		}

		return $title;
	}
}
