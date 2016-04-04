<?php
/**
 * Component for generation web fonts defenition rules/links.
 *
 * @author    Themedelight
 * @package   Themedelight/Components
 * @version   1.0.0
 */

class TdFontsManager extends TdComponent
{
	/**
	 * Font families config. Font family should be used as a key.
	 * Each element may include following keys:
	 *     'style'   optional set of allowed styles, array('normal') is a default value
	 *     'weight'  optional set of allowed weights, array('400') is a default value
	 *     'files'   optional set of font files.
	 * @var array
	 */
	public $font_set = array();

	/**
	 * Convert set of font settings for css that should be included to the document to connect defined font.
	 * Each element of $fonts shold
	 * @param  array $fonts each element should has following keys:
	 *                      'family' - required
	 *                      'style'  - optional, "normal" is default value
	 *                      'weight' - optional, "400" is default value
	 * @return array  each element will include
	 */
	public function generateDefinitions(array $fonts) {
		$googleApiElements = array();
		$inlineDefinitions = array();

		foreach ( $fonts as $key => $fontSettings ) {
			$family = ! empty( $fontSettings['family'] ) ? $fontSettings['family'] : '';
			if ( ! $family ) {
				continue;
			}
			$fontConfig = $this->getConfigByFamily( $family );
			// if font definition has not key 'files' - it is google web font
			$isGoogle = empty( $fontConfig['files'] );

			$weight = ! empty( $fontSettings['weight'] ) ? $fontSettings['weight'] : '';
			$style = ! empty( $fontSettings['style'] ) ? $fontSettings['style'] : '';

			if ( $isGoogle ) {
				$gStyleDefinition = str_replace(
					array( 'normal','regular' ),
					'400',
					$weight
				) . $style;
				$googleApiElements[$family][$gStyleDefinition] = $gStyleDefinition;
			} else {
				$inlineDefinitions[$family] = $this->renderFontFamilyDefinition( $family, $fontSettings );
			}
		}

		$result = array();
		if ( $googleApiElements ) {
			$gApiFamilies = array();
			foreach ( $googleApiElements as $family => $definitions ) {
				$paramText = str_replace( ' ', '+', $family );

				if ( $definitions ) {
					$paramText .= ':' . join( ',', $definitions );
				}

				$gApiFamilies[] = $paramText;
			}

			$result['google-fonts'] = array(
				'url' => 'http://fonts.googleapis.com/css?family=' . join( '|', $gApiFamilies ),
			);
		}

		if ( $inlineDefinitions ) {
			$result['inline-fonts'] = array(
				'text' => join( "\n\n", $inlineDefinitions ),
			);
		}
		return $result;
	}

	public function getConfigByFamily($family) {
		if ( $family && isset( $this->font_set[$family] ) ) {
			return $this->font_set[$family];
		}
		return array();
	}
}
