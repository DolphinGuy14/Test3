<?php
/**
 * Thankyou page
 *
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<div class="woocommerce-box">
<?php if ( $order ) : ?>
	<?php if ( $order->has_status( 'failed' ) ) : ?>

		<p><?php _e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction.', 'woocommerce' ); ?></p>

		<p><?php
			if ( is_user_logged_in() )
				_e( 'Please attempt your purchase again or go to your account page.', 'woocommerce' );
			else
				_e( 'Please attempt your purchase again.', 'woocommerce' );
		?></p>

		<p>
			<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php _e( 'Pay', 'woocommerce' ) ?></a>
			<?php if ( is_user_logged_in() ) : ?>
			<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php _e( 'My Account', 'woocommerce' ); ?></a>
			<?php endif; ?>
		</p>

	<?php else : ?>

		<p><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'woocommerce' ), $order ); ?></p>

		<ul class="order_details">
		<?php 
			printf('<li class="order">%s<strong>%s</strong></li>',
				__( 'Order Number:', 'woocommerce' ),
				$order->get_order_number()
			);
			printf('<li class="date">%s<strong>%s</strong></li>',
				__( 'Date:', 'woocommerce' ),
				date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) )
			);
			printf('<li class="total">%s<strong>%s</strong></li>',
				__( 'Total:', 'woocommerce' ),
				$order->get_formatted_order_total()
			);
			if ( $order->payment_method_title ) {
				printf('<li class="method">%s<strong>%s</strong></li>',
					__( 'Payment Method:', 'woocommerce' ),
					$order->payment_method_title
				);
			}
		?>
		</ul>
		<div class="clear"></div>

	<?php endif; ?>

	<?php do_action( 'woocommerce_thankyou_' . $order->payment_method, $order->id ); ?>
	<?php do_action( 'woocommerce_thankyou', $order->id ); ?>
<?php else : ?>

	<p><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'woocommerce' ), null ); ?></p>

<?php endif; ?>
</div>
