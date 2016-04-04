<?php
/**
 * Class contains methods/helper functions related to tours.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

class AtTourHelper
{
	/**
	 * Map for woocommerce templates replacement functionality.
	 *
	 * @see filter_wc_template_rendering
	 * @var array
	 */
	public static $wcTemplatesMap = array(
		'single-product' => 'templates/tour/single',
		'content-single-product' => 'templates/tour/content',
		// 'content-product' => 'templates/tour/content',
	);

	public static function init() {
		if ( self::$wcTemplatesMap ) {
			add_action( 'adventure_tours_allow_wc_template_render', array( __CLASS__, 'filter_wc_template_rendering' ), 20 );
		}
	}

	/**
	 * Checks if current post is a product and has tour type.
	 *
	 * @param  mixed $product product id/instance.
	 * @return boolean
	 */
	public static function isTourProduct($product = null) {
		if ( ! $product ) {
			$product = isset( $GLOBALS['product'] ) ? $GLOBALS['product'] : null;
		}
		if ( $product ) {
			$curProduct = is_string( $product ) ? wc_get_product( false ) : $product;
			if ( $curProduct && $curProduct->is_type( 'tour' ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Filter that called before any woocommerce template rendering.
	 * If filter returns false - rendering should be stopped, filter function should take care about rendering.
	 *
	 * @param  string $file full path to template file that should be rendered.
	 * @return string|false
	 */
	public static function beforeWCTemplateRender($file) {
		return apply_filters( 'adventure_tours_allow_wc_template_render', $file );
	}

	/**
	 * Filter that replaces current woocommerce template with template defined in settings.
	 *
	 * @see beforeWCTemplateRender
	 * @param  string $file full path to currently rendered template
	 * @return mixed
	 */
	public static function filter_wc_template_rendering($file) {
		if ( $file && self::isTourProduct() ) {
			$anotherTemplate = '';
			$map = self::$wcTemplatesMap;
			$baseName = basename( $file, '.php' );
			if ( isset( $map[$baseName] ) ) {
				wc_get_template_part( $map[$baseName] ); // get_template_part($map[$baseName]);
				return false;
			}
		}
		return $file;
	}

	/**
	 * Returns list of attributes available for tour posts.
	 *
	 * @param  boolean $withLists if set to true each element will contains list of values.
	 * @param  boolean $putLabelAsEmptyValue if set to true -
	 *                                       each list will contains label as empty element for each list.
	 * @return array
	 */
	public static function get_available_attributes($withLists = false, $putLabelAsEmptyValue = false) {
		$result = array();

		$taxonomies = get_object_taxonomies( 'product', 'objects' );
		if ( empty( $taxonomies ) ) {
			return $result;
		}

		foreach ( $taxonomies as $tax ) {
			$taxName = $tax->name;
			if ( 0 !== strpos( $taxName, 'pa_' ) ) {
				continue;
			}
			if ( $withLists ) {
				if ( $putLabelAsEmptyValue ) {
					$result[$taxName] = array(
						'' => $tax->label,
					);
				} else {
					$result[$taxName] = array();
				}
			} else {
				$result[$taxName] = $tax->label;
			}
		}
		if ( $withLists && $result ) {
			$values = get_terms( array_keys( $result ), array(
				'orderby' => 'term_group' 
			));

			foreach ( $values as $term ) {
				$result[$term->taxonomy][$term->slug] = $term->name;
			}
		}

		return $result;
	}

	/**
	 * Returns set of taxonomies/tour attributes that should be used as additional fields for tour search form.
	 *
	 * @param  boolean $onlyAllowedInSettings if set to true only fields allwed in Tours > Search Form > Additional Fields option.
	 * @return array
	 */
	public static function get_search_form_fields($onlyAllowedInSettings = true) {
		$result = array();
		$allowedList = adventure_tours_get_option( 'tours_search_form_attributes' );
		if ( $allowedList || ! $onlyAllowedInSettings ) {
			$fullList = self::get_available_attributes( true, true );
			if ( ! $onlyAllowedInSettings ) {
				$result = $fullList;
			} else {
				foreach ( $allowedList as $attributeName ) {
					if ( ! empty( $fullList[$attributeName] ) ) {
						$result[$attributeName] = $fullList[$attributeName];
					} elseif ( '__tour_categories_filter' == $attributeName ) {
						$result[$attributeName] = array();
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Returns modified tour attributes where each element contains information about attribute label,
	 * value and icon class.
	 *
	 * @param  WC_Product  $product               product for that attributes should be retrived.
	 * @param  boolean     $onlyAllowedInSettings if attributes should be filtered with values allowed in theme options.
	 * @return array
	 */
	public static function get_tour_details_attributes($product, $onlyAllowedInSettings = true) {
		$result = array();
		$list = $product->get_attributes();
		$allowedList = adventure_tours_get_option( 'tours_page_top_attributes' );
		if ( ! $list || ( $onlyAllowedInSettings && ! $allowedList ) ) {
			return $result;
		}

		foreach ( $list as $name => $attribute ) {
			$attrib_name = $attribute['name'];

			if ( empty( $attribute['is_visible'] ) || ( $attribute['is_taxonomy'] && ! taxonomy_exists( $attrib_name ) ) ) {
				continue;
			}

			if ( false === $onlyAllowedInSettings &&  in_array( $attrib_name, $allowedList ) ) {
				continue;
			}

			if ( $attribute['is_taxonomy'] ) {
				$values = wc_get_product_terms( $product->id, $attrib_name, array( 'fields' => 'names' ) );
				$text = apply_filters( 'woocommerce_attribute', wptexturize( implode( ', ', $values ) ), $attribute, $values );
			} else {
				// Convert pipes to commas and display values
				$values = array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );
				$text = apply_filters( 'woocommerce_attribute', wptexturize( implode( ', ', $values ) ), $attribute, $values );
			}

			$result[$attrib_name] = array(
				'name' => $attrib_name,
				'label' => wc_attribute_label( $attrib_name ),
				'values' => $values,
				'text' => $text,
				'icon_class' => self::get_product_attribute_icon_class( $attribute ),
			);
		}

		// We need reorder items according order in settings.
		if ( $onlyAllowedInSettings && $result ) {
			$orderedList = array();

			foreach ( $allowedList as $attribKey ) {
				if ( ! empty( $result[$attribKey] ) ) {
					$orderedList[$attribKey] = $result[$attribKey];
				}
			}

			return $orderedList;
		}

		return $result;
	}

	/**
	 * Retrives icon class related to the tour category term.
	 *
	 * @param  mixed $tour_category term object or term id.
	 * @return string
	 */
	public static function get_tour_category_icon_class( $tour_category ) {
		$term_id = is_scalar( $tour_category ) ? $tour_category : (
			isset( $tour_category->term_id ) ? $tour_category->term_id : ''
		);
		if ( $term_id > 0 ) {
			$storage = adventure_tours_di( 'tour_category_icons_storate' );
			if ( $storage && $storage->is_active() ) {
				return $storage->getData( $term_id );
			}
		}
		// return default tour category ison class
		return '';
	}

	/**
	 * Retrives thumbnail id related to the tour category term.
	 *
	 * @param  mixed $tour_category term object or term id.
	 * @return string
	 */
	public static function get_tour_category_thumbnail( $tour_category ) {
		$term_id = is_scalar( $tour_category ) ? $tour_category : (
			isset( $tour_category->term_id ) ? $tour_category->term_id : ''
		);
		if ( $term_id > 0 ) {
			$storage = adventure_tours_di( 'tour_category_images_storage' );
			if ( $storage && $storage->is_active() ) {
				return $storage->getData( $term_id );
			}
		}

		return null;
	}

	/**
	 * Return tour attribute icon class.
	 *
	 * @param  string $product_attribute
	 * @return string
	 */
	public static function get_product_attribute_icon_class( $product_attribute ) {
		$result = '';

		$icons_storage = adventure_tours_di( 'product_attribute_icons_storage' );
		if ( ! $icons_storage || ! $icons_storage->is_active() ) {
			return $result;
		}

		$name = is_string( $product_attribute ) ? $product_attribute : $product_attribute['name'];

		static $attrMap;
		if ( null == $attrMap ) {
			$attrMap = array();

			$paTaxonomies = wc_get_attribute_taxonomies();
			if ( $paTaxonomies ) {
				foreach ( $paTaxonomies as $taxInfo ) {
					$attrMap[ 'pa_' . $taxInfo->attribute_name ] = $taxInfo->attribute_id;
				}
			}
		}

		if ( isset( $attrMap[$name] ) ) {
			if ( $savedValue = $icons_storage->getData( $attrMap[$name] ) ) {
				$result = $savedValue;
			}
		}

		return $result;
	}

	/**
	 * Returns display mode value for tour archive page.
	 * If $tour_category_id has been specefied - category specific value, otherwise value will be taken from the theme options.
	 *
	 * @param  int $tour_category_id
	 * @return string                possible values are: 'products', 'subcategories', 'both'.
	 */
	public static function get_tour_archive_page_display_mode ( $tour_category_id = null ) {
		$result = 'default';

		if ( $tour_category_id > 0 ) {
			$cat_display_storage = adventure_tours_di( 'tour_category_display_type_storage' );
			if ( $cat_display_storage && $cat_display_storage->is_active() ) {
				$result = $cat_display_storage->getData( $tour_category_id );
			}
		}

		if ( 'default' == $result ) {
			$result = adventure_tours_get_option( 'tours_archive_display_mode' );
		}

		return !$result || 'default' == $result ? 'both' : $result;
	}
}
