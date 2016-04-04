<?php
/**
 * Social icons rendering template part.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

$social_icons = array(
	'facebook' => 'facebook',
	'twitter' => 'twitter',
	'googleplus' => 'google-plus',
	'pinterest' => 'pinterest',
	'linkedin' => 'linkedin',
	'instagram' => 'instagram',
	'dribbble' => 'dribbble',
	'tumblr' => 'tumblr',
	'vk' => 'vk',
);

$social_icons_html = '';
foreach ( $social_icons as $key => $icon_class ) {
	$url = adventure_tours_get_option( 'social_link_' . $key );
	if ( $url ) {
		$social_icons_html .= '<a href="' . esc_url( $url ) . '"><i class="fa fa-' . esc_attr( $icon_class ) . '"></i></a>';
	}
}
if ( $social_icons_html ) {
	printf( '<div class="header__info__item header__info__item--delimiter header__info__item--social-icons">%s</div>',
		$social_icons_html
	);
}
