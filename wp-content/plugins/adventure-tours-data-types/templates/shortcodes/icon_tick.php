<?php
/**
 * Shortcode [icon_tick] view.
 * For more detailed list see list of shortcode attributes.
 *
 * @var boolean $state
 * @var string  $view
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

if ( $state ) {
	echo '<i class="fa fa-check at-icon-tick at-icon-tick--on"></i>';
} else {
	echo '<i class="fa fa-times at-icon-tick at-icon-tick--off"></i>';
}
