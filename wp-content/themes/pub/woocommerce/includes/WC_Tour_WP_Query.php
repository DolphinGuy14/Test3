<?php
/**
 * Class for building tour related queries.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

class WC_Tour_WP_Query
{
	public function init() {
		if ( ! is_admin() ) {
			add_action( 'pre_get_posts', array( $this, 'filter_pre_get_posts' ) );
			add_filter( 'query_vars', array( $this, 'filter_query_vars' ) );
		}
	}

	public function filter_pre_get_posts($q) {
		if ( ! $q->is_main_query() ) {
			if ( 'product' == $q->get( 'post_type' ) ) {
				// For all queries that is not marked as tours query we excluding products with 'tour' product_type value.
				$is_tour_query = 'tours' == $q->get('wc_query'); // checking is this is tour query
				if ( ! $is_tour_query  ) {
					$q->set( 'tax_query', array(
						'relation' => 'AND',
						array(
							'taxonomy' => 'product_type',
							'terms' => 'tour',
							'field' => 'slug',
							'operator' => 'NOT IN',
						),
					) );
				}
			}
			return;
		}

		$isTourArchivePage = false;
		if ( ! empty( $q->query_vars['toursearch'] ) ) {
			$isTourArchivePage = true;
		} else if ( $tours_page_id = $this->get_tours_page_id() ) {
			if ( $q->get('page_id') == $tours_page_id ) {
				$q->set('page_id' ,'');
				$isTourArchivePage = true;
			}
		}

		if ( $isTourArchivePage ) {
			$q->set( 'is_tour_query', 1 );

			$q->set( 'post_type', 'product' );
			$q->set( 'page', '' );
			$q->set( 'pagename', '' );
			$q->set( 'wc_query', 'tours' );

			$q->is_archive           = true;
			$q->is_post_type_archive = true;
			$q->is_singular          = false;
			$q->is_page              = false;
			if ( $q->is_home ) {
				$q->is_home = false;
				/*if ( 'page' != get_option( 'show_on_front') ) {
					$q->is_home = false;
				} else {
					$tours_page_id = $this->get_tours_page_id();
					if ( ! $tours_page_id || $tours_page_id != get_option( 'page_on_front' ) ) {
						$q->is_home = false;
					}
				}*/
			}
		}

		if ( 'product' == $q->get( 'post_type' ) ) {
			$taxQuery = array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'product_type',
					'terms' => 'tour',
					'field' => 'slug',
					'operator' => $isTourArchivePage ? 'IN' : 'NOT IN',
				),
			);

			if ( $isTourArchivePage ) {
				$tourTaxonomies = ! empty( $q->query_vars['tourtax'] ) ? $q->query_vars['tourtax'] : array();
				$taxConditions = array(
					// Alternative logic: 'relation' => 'OR'
				);
				if ( $tourTaxonomies ) {
					foreach ( $tourTaxonomies as $taxName => $taxValue ) {
						if ( ! $taxValue ) {
							continue;
						}

						$taxConditions[] = array(
							'taxonomy' => $taxName,
							'terms' => wp_unslash( (array) $taxValue ),
							'field' => 'slug',
						);
					}
				}

				if ( $taxConditions ) {
					$taxQuery[] = $taxConditions;
				}
			}

			if ( $taxQuery ) {
				$q->set( 'tax_query', $taxQuery );
			}
		}
		return $q;
	}

	/**
	 * Adds query vars used for tours filtering.
	 *
	 * @param  array $vars set of query vars.
	 * @return array
	 */
	public function filter_query_vars($vars) {
		$vars[] = 'toursearch';
		$vars[] = 'tourtax';
		return $vars;
	}

	public function get_tours_page_id() {
		static $result;
		if ( null == $result ) {
			$result = adventure_tours_get_option( 'tours_page', 0 );
		}
		return $result;
	}
}
