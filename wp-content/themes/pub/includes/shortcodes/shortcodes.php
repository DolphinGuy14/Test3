<?php
/**
 * Shortcodes definition core file.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */


if ( is_admin() ) {
	$shortcodesDir = dirname( __FILE__ );

	// To init TinyMCE integrator.
	adventure_tours_di( 'shortcodes_tiny_mce_integrator' );

	// Includeing file with menu options for shortcodes management.
	require $shortcodesDir . '/menu.php';
}
