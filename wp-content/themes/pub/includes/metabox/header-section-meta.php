<?php
/**
 * Config file for metabox fields defenition for header section block.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

return array(
	array(
		'name' => 'section_mode',
		'label' => esc_html__( 'Display Mode','adventure-tours' ),
		'type' => 'radiobutton',
		'items' => array(
			array(
				'value' => 'hide',
				'label' => esc_html__( 'Default', 'adventure-tours' ),
			),
			array(
				'value' => 'banner',
				'label' => esc_html__( 'Image', 'adventure-tours' ),
			),
			array(
				'value' => 'slider',
				'label' => esc_html__( 'Slider', 'adventure-tours' ),
			),
		),
		'default' => '{{first}}',
	),
	_adventure_tours_getSliderSelector(),
	array(
		'type' => 'textbox',
		'name' => 'banner_subtitle',
		'label' => esc_html__( 'Subtitle', 'adventure-tours' ),
		'dependency' => array(
			'field' => 'section_mode',
			'function' => 'adventure_tours_vp_header_section_is_banner',
		),
	),
	array(
		'name' => 'banner_image',
		'type' => 'upload',
		'label' => esc_html__( 'Image', 'adventure-tours' ),
		'default' => '',
		'dependency' => array(
			'field' => 'section_mode',
			'function' => 'adventure_tours_vp_header_section_is_banner',
		),
	),
	array(
		'name' => 'is_banner_image_parallax',
		'type' => 'toggle',
		'label' => esc_html__( 'Use Parallax', 'adventure-tours' ),
		'default' => '1',
		'dependency' => array(
			'field' => 'section_mode',
			'function' => 'adventure_tours_vp_header_section_is_banner',
		),
	),
	array(
		'name' => 'banner_image_repeat',
		'label' => esc_html__( 'Image repeat','adventure-tours' ),
		'type' => 'select',
		'items' => array(
			array(
				'value' => 'repeat',
				'label' => esc_html__( 'Repeat', 'adventure-tours' ),
			),
			array(
				'value' => 'no-repeat',
				'label' => esc_html__( 'No repeat', 'adventure-tours' ),
			),
			array(
				'value' => 'repeat-x',
				'label' => esc_html__( 'Repeat horizontally', 'adventure-tours' ),
			),
			array(
				'value' => 'repeat-y',
				'label' => esc_html__( 'Repeat vertically', 'adventure-tours' ),
			),
		),
		'default' => '{{first}}',
		'dependency' => array(
			'field' => 'section_mode',
			'function' => 'adventure_tours_vp_header_section_is_banner',
		),
	),
	array(
		'type' => 'select',
		'name' => 'banner_mask',
		'label' => esc_html__( 'Mask', 'adventure-tours' ),
		'dependency' => array(
			'field' => 'section_mode',
			'function' => 'adventure_tours_vp_header_section_is_banner',
		),
		'items' => array(
			array(
				'label' => esc_html__( 'None', 'adventure-tours' ),
				'value' => '',
			),
			array(
				'label' => esc_html__( 'Default', 'adventure-tours' ),
				'value' => 'default',
			),
		),
		'default' => '',
	),
);

/**
 * Local function that returns field that allows to select revolution slider.
 *
 * @return assoc
 */
function _adventure_tours_getSliderSelector() {

	$isRevoSliderInstalled = class_exists( 'RevSlider' );

	$revoSlidersList = array();
	if ( $isRevoSliderInstalled ) {
		$slider = new RevSlider();
		if ( $arrSliders = $slider->getArrSlidersShort() ) {
			foreach ( $arrSliders as $sid => $stitle ) {
				$revoSlidersList[] = array(
					'value' => $sid,
					'label' => $stitle,
				);
			}
		}
	}

	$descriptionNoticeText = '';
	if ( ! $isRevoSliderInstalled ) {
		$descriptionNoticeText = esc_html__( 'Please install and activate the Slider Revolution plugin.','adventure-tours' );
	} else if ( empty( $revoSlidersList ) ) {
		$descriptionNoticeText = esc_html__( 'Please go to Slider Revolution plugin and create a slider.','adventure-tours' );
	}

	return array(
		'label' => esc_html__( 'Choose Slider', 'adventure-tours' ),
		'type' => 'select',
		'name' => 'slider_alias',
		'description' => $descriptionNoticeText ? '<span style="color:#EE0000">' . $descriptionNoticeText . '</span>' : '',
		'items' => $revoSlidersList,
		'dependency' => array(
			'field' => 'section_mode',
			'function' => 'adventure_tours_vp_header_section_is_slider',
		),
	);
}
