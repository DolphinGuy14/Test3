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
	echo '<i class="fa fa-check icon-tick icon-tick--on"></i>';
} else {
	echo '<i class="fa fa-times icon-tick icon-tick--off"></i>';
}
