<?php
/**
 * Contact us widget component.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

class AtWidgetContactUs extends WP_Widget
{
	public function __construct() {
		parent::__construct(
			'contact_us_adventure_tours',
			'AdventureTours: ' . esc_html__( 'Contact Us', 'adventure-tours' ),
			array(
				'description' => esc_html__( 'Contact Us Widget', 'adventure-tours' ),
			)
		);
	}

	public function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );

		$elements_html = '';

		if ( $address ) {
			$elements_html .= '<div class="widget-contact-info__item">' .
				'<div class="widget-contact-info__item__icon"><i class="fa fa-map-marker"></i></div>' .
				'<div class="widget-contact-info__item__text"><span>' . esc_html( $address ) . '</span></div>' .
			'</div>';
		}

		if ( $phone ) {
			$elements_html .= '<div class="widget-contact-info__item">' .
				'<div class="widget-contact-info__item__icon"><i class="fa fa-phone"></i></div>' .
				'<div class="widget-contact-info__item__text"><span>' . esc_html( $phone ) . '</span></div>' .
			'</div>';
		}

		if ( $email ) {
			$elements_html .= '<div class="widget-contact-info__item">' .
				'<div class="widget-contact-info__item__icon"><i class="fa fa-envelope"></i></div>' .
				'<div class="widget-contact-info__item__text"><span>' . esc_html( $email ) . '</span></div>' .
			'</div>';
		}

		if ( $skype ) {
			$elements_html .= '<div class="widget-contact-info__item">' .
				'<div class="widget-contact-info__item__icon"><i class="fa fa-skype"></i></div>' .
				'<div class="widget-contact-info__item__text"><span>' . esc_html( $skype ) . '</span></div>' .
			'</div>';
		}

		if ( $elements_html ) {
			printf(
				'%s<div class="widget-contact-info">%s%s</div>%s',
				$before_widget,
				$title ? $before_title . esc_html( $title ) . $after_title : '',
				$elements_html,
				$after_widget
			);
		}
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $new_instance;
		return $instance;
	}

	public function form( $instance ) {
		$default = array(
			'title' => '',
			'address' => '',
			'phone' => '',
			'email' => '',
			'skype' => '',
		);

		$itemTitles = array(
			'title' => __( 'Title', 'adventure-tours' ),
			'address' => __( 'Address', 'adventure-tours' ),
			'phone' => __( 'Phone', 'adventure-tours' ),
			'email' => __( 'Email', 'adventure-tours' ),
			'skype' => __( 'Skype', 'adventure-tours' ),
		);

		$instance = wp_parse_args( (array) $instance, $default );

		foreach ( $instance as $key => $val ) {
			$itemTitle = isset( $itemTitles[$key] ) ? $itemTitles[$key] : '';

			echo '<p>' .
				'<label for="' . esc_attr( $this->get_field_id( $key ) ) . '">' . esc_html( $itemTitle ) . ':</label>' .
				'<input class="widefat" id="' . esc_attr( $this->get_field_id( $key ) ) . '" name="' . esc_attr( $this->get_field_name( $key ) ) . '" type="text" value="' . esc_attr( $val ) . '">' .
			'</p>';
		}
	}
}
