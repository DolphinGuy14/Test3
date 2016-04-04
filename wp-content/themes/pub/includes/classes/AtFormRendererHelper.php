<?php
/**
 * Helper for form rendering/fields rendering, js related init fuctions running.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

class AtFormRendererHelper extends TdComponent
{
	/**
	 * Assoc of field values.
	 *
	 * @var array
	 */
	public $field_vals = array();

	/**
	 * Assoc of placeholders for fields.
	 *
	 * @var array
	 */
	public $field_placeholders = array();

	/**
	 * Assoc of fild errors.
	 *
	 * @var array
	 */
	public $field_errors = array();

	/**
	 * Renders form field value and title attributes.
	 * Value for the 'value' attribute taken from 'field_vals' set,
	 * the value for the 'title' attribute - from field errors.
	 *
	 * @param  string  $name   field key.
	 * @param  boolean $return if value should be returned instead of outputted.
	 * @return string
	 */
	public function render_field_attributes( $name, $return = false ) {
		$val = isset( $this->field_vals[$name] ) ? $this->field_vals[$name] : '';
		$attributes = array(
			'value="' . esc_attr( $val ) . '"',
		);
		if ( isset( $this->field_errors[$name] ) ) {
			$attributes[] = 'title="' . esc_attr( join( '<br>', $this->field_errors[$name] ) ) . '"';
		}

		if ( isset ( $this->field_placeholders[$name] ) ) {
			$attributes[] = 'placeholder="' . esc_attr( $this->field_placeholders[$name] ) . '"';
		}
		if ( $return ) {
			return join( ' ', $attributes );
		} else {
			print join( ' ', $attributes );
		}
	}

	public function init_js_errors( $items_selector ) {
		if ( ! $items_selector ) {
			return;
		}

		TdJsClientScript::addScript('initValidationBookTour', <<<SCRIPT
			Theme.FormValidationHelper
				.initTooltip('{$items_selector}')
				.addClass('form-validation-item')
				.tooltip('show');
SCRIPT
		);
	}
}
