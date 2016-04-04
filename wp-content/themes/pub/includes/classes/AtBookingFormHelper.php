<?php
/**
 * Helper for the tour booking form implementation.
 * Contains methods related to data processing, validation and adding items to the shopping cart.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

class AtBookingFormHelper extends TdComponent
{
	/**
	 * Determines if user should be sent to the checkout page right after success booking event.
	 *
	 * @see handler_add_to_cart_handler_tour
	 * @var boolean
	 */
	public $booking_form_handler_send_to_checkout = true;

	/**
	 * Determines prefix used for booking saving data related to the tour.
	 *
	 * @see filter_woocommerce_add_order_item_meta
	 * @var string
	 */
	protected $bookingDataPrefixInOrderItem = 'tour_';

	/**
	 * Contains values for checkout form filled by user on the booking form.
	 *
	 * @see action_init_default_checkout_fileds
	 * @var assoc
	 */
	public $defaultCheckoutFields = array();

	/**
	 * Date format that should be used on the booking form.
	 * @see get_date_format
	 * @var string
	 */
	public $date_format = 'd/m/Y';

	/**
	 * Flag that prevents few times inition event.
	 *
	 * @see action_init_default_checkout_fileds
	 * @var boolean
	 */
	private $default_checkout_fields_is_inited = false;

	protected $form_errors = array();

	/**
	 * Cache option.
	 *
	 * @see get_booking_fields
	 * @var assoc
	 */
	protected $cache_booking_fields;

	public function init() {
		if ( parent::init() ) {

			add_action( 'woocommerce_before_checkout_form', array( $this, 'action_init_default_checkout_fileds' ), 1 );

			// booking and orders processing
			add_action( 'woocommerce_add_to_cart_handler_tour', array( $this, 'handler_add_to_cart_handler_tour' ) );
			add_filter( 'woocommerce_attribute_label', array( $this, 'filter_woocommerce_attribute_label' ), 20, 2 );
			add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'filter_woocommerce_get_cart_item_from_session' ), 1, 3 );
			add_filter('woocommerce_get_item_data', array($this, 'filter_woocommerce_get_item_data'), 20, 2);
			add_action( 'woocommerce_add_order_item_meta',array( $this, 'filter_woocommerce_add_order_item_meta' ),1,2 );

			add_filter( 'adventure_tours_load_booking_form_data', array( $this, 'filter_adventure_tours_load_booking_form_data' ), 1, 2 );

			return true;
		}
		return false;
	}

	/*** booking form integration [start] ***/
	/**
	 * Returns list of fields availabel for tour booking form.
	 *
	 * @return array|assoc
	 */
	public function get_booking_fields($withLabels = false) {
		if ( ! $this->cache_booking_fields ) {
			$this->cache_booking_fields = array(
				'date' => esc_html__( 'Booking Date', 'adventure-tours' ),
				'children' => esc_html__( 'Children', 'adventure-tours' ),
			);
		}

		if ( ! $withLabels ) {
			return array_keys( $this->cache_booking_fields );
		}
		return $this->cache_booking_fields;
	}

	/**
	 * Filter allows fill in default values to the tour booking form.
	 *
	 * @param  assoc  $data
	 * @param  string $productId
	 * @return assoc
	 */
	public function filter_adventure_tours_load_booking_form_data($formData, $productId) {

		$customer_id = get_current_user_id();

		// to load data for 'filter_default_checkout_field' function
		$this->action_init_default_checkout_fileds();

		$requestData = $this->read_request_data();
		$reqDataOrder = $requestData['order_data'];
		$reqDataTour = $requestData['tour_data'];

		if ( ! isset( $formData['fields'] ) ) {
			$formData['fields'] = array();
		}
		$data = &$formData['fields'];

		if ( $reqDataOrder ) {
			foreach ( array( 'name', 'email', 'phone' ) as $fieldName ) {
				if ( isset( $reqDataOrder[$fieldName] ) ) {
					$data[$fieldName] = $reqDataOrder[$fieldName];
				}
			}
		}

		if ( $reqDataTour ) {
			foreach ( array( 'date', 'children' ) as $fieldName ) {
				if ( isset( $reqDataTour[$fieldName] ) ) {
					$data[$fieldName] = $reqDataTour[$fieldName];
				}
			}
		}

		if ( isset( $data['name'] ) && empty( $data['name'] ) ) {
			$fname = $this->filter_default_checkout_field( null, 'billing_first_name' );
			$lname = $this->filter_default_checkout_field( null, 'billing_last_name' );
			if ( ! $fname && $customer_id ) {
				$fname = get_user_meta( $customer_id, 'billing_first_name', true ) || get_user_meta( $customer_id, 'first_name', true );
			}
			if ( ! $lname && $customer_id ) {
				$lname = get_user_meta( $customer_id, 'billing_last_name', true ) || get_user_meta( $customer_id, 'last_name', true );
			}

			if ( $fname || $lname ) {
				$data['name'] = trim(
					join( ' ', array( $fname, $lname ) )
				);
			}
		}

		if ( isset( $data['email'] ) && empty( $data['email'] ) ) {
			$email = $this->filter_default_checkout_field( null, 'billing_email' );
			if ( ! $email && $customer_id ) {
				$current_user = wp_get_current_user();
				$email = get_user_meta( $customer_id, 'billing_email', true ) || $current_user->user_email;
			}
			if ( $email ) {
				$data['email'] = $email;
			}
		}

		if ( isset( $data['phone'] ) && empty( $data['phone'] ) ) {
			$phone = $this->filter_default_checkout_field( null, 'billing_phone' );
			if ( ! $phone && $customer_id ) {
				$phone = get_user_meta( $customer_id, 'billing_phone', true );
			}
			if ( $phone ) {
				$data['phone'] = $phone;
			}
		}

		$requestErrors = $this->validateRequestData( $requestData );
		if ( $reqDataTour || $reqDataTour || $requestErrors ) {
			$formData['errors'] = $this->validateFields( $data );
			if ( $requestErrors ) {
				$formData['errors'] = array_merge( $formData['errors'], $requestErrors );
			}
		}

		return $formData;
	}

	/**
	 * Loads values filled on booking form to local cache.
	 * Adds 'woocommerce_checkout_get_value' filter.
	 * Subscribed on 'woocommerce_before_checkout_form' action.
	 *
	 * @return void
	 */
	public function action_init_default_checkout_fileds() {

		if ( $this->default_checkout_fields_is_inited ) {
			return;
		}
		$this->default_checkout_fields_is_inited = true;

		$bookingFormData = WC()->session->get( 'tour_order_data' );
		if ( $bookingFormData ) {
			$this->defaultCheckoutFields = ! $this->defaultCheckoutFields
				? $bookingFormData
				: array_merge( $this->defaultCheckoutFields, $bookingFormData );
		}

		if ( $this->defaultCheckoutFields ) {
			add_filter( 'woocommerce_checkout_get_value', array( $this, 'filter_default_checkout_field' ), 20, 2 );
		}
	}

	/**
	 * Filter that returns values defined for checkout durring booking form submission.
	 *
	 * @param  string $value current value
	 * @param  string $field field name
	 * @return mixed
	 */
	public function filter_default_checkout_field($value, $field) {

		if ( ! empty( $this->defaultCheckoutFields[$field] ) ) {
			return $this->defaultCheckoutFields[$field];
		}
		return $value;
	}
	/*** booking form integration [end] ***/

	public function read_request_data() {

		$result = array(
			'quantity' => isset( $_REQUEST['quantity'] ) ? $_REQUEST['quantity'] : 1,
			'tour_id' => isset( $_REQUEST['add-to-cart'] ) ? $_REQUEST['add-to-cart'] : 0,
			'tour_data' => isset( $_REQUEST['tour_data'] ) ? $_REQUEST['tour_data'] : array(),
			'order_data' => isset( $_REQUEST['order_data'] ) ? $_REQUEST['order_data'] : array(),
		);

		if ( ! is_array( $result['tour_data'] ) ) {
			$result['tour_data'] = array();
		}
		if ( ! is_array( $result['order_data'] ) ) {
			$result['order_data'] = array();
		}

		return $result;
	}

	/**
	 * Handler used for adding tour to the shopping card.
	 * Used by booking form.
	 *
	 * @param string $url redirect url
	 * @return void
	 */
	public function handler_add_to_cart_handler_tour( $url ) {

		$requestPid = @$_REQUEST['add-to-cart'];
		$product_id = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $requestPid ) );

		if ( ! $product_id ) {
			return;
		}

		$quantity = empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( $_REQUEST['quantity'] );

		$requestData = $this->read_request_data();

		// Add to cart validation
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

		$added_to_cart = array();
		$was_added_to_cart = false;

		$tourData = $requestData['tour_data'];
		$orderData = $requestData['order_data'];

		$orderDataErrors = array();
		$tourDataErrors = array();

		if ( $orderData ) {
			$convertedOrderData = array();

			$orderDataErrors = $this->validateFields( $orderData );
			if ( ! $orderDataErrors ) {
				if ( ! empty( $orderData['name'] ) ) {
					$nameParts = explode( ' ', $orderData['name'] );
					if ( ! empty( $nameParts[0] ) ) {
						$convertedOrderData['billing_first_name'] = $nameParts[0];
					}
					if ( ! empty( $nameParts[1] ) ) {
						$convertedOrderData['billing_last_name'] = $nameParts[1];
					}
				}

				if ( ! empty( $orderData['email'] ) ) {
					$convertedOrderData['billing_email'] = $orderData['email'];
				}

				if ( ! empty( $orderData['phone'] ) ) {
					$convertedOrderData['billing_phone'] = $orderData['phone'];
				}

				if ( $convertedOrderData ) {
					WC()->session->set( 'tour_order_data', $convertedOrderData );
				}
			}
		}

		if ( $tourData ) {
			$tourDataErrors = $this->validateFields( $tourData );
			/* if (!$tourDataErrors) {
				if ( isset( $tourData['date'] ) ) {
					if ( empty( $tourData['date'] ) ) {
						$custom_errors['date'] = esc_html__( 'Field is required.', 'adventure-tours' );
					}
				}
			} */
		}

		$orderDataErrors = $this->validateFields( $orderData );

		if ( $passed_validation && ! $tourDataErrors && ! $orderDataErrors ) {
			if ( WC()->cart->add_to_cart( $product_id, $quantity, '', array(), $tourData ) ) {
				$was_added_to_cart = true;
				$added_to_cart[] = $product_id;
			}
		}

		if ( $was_added_to_cart && wc_notice_count( 'error' ) == 0 ) {

			if ( ! $url || $this->booking_form_handler_send_to_checkout ) {
				$url = WC()->cart->get_checkout_url();
			}

			$url = apply_filters( 'woocommerce_add_to_cart_redirect', $url );

			// If has custom URL redirect there
			if ( $url ) {
				wp_safe_redirect( $url );
				exit;
			} // redirect to cart option
			elseif ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) {
				wp_safe_redirect( WC()->cart->get_cart_url() );
				exit;
			}

			wc_add_to_cart_message( $added_to_cart );
		}
	}

	public function filter_woocommerce_attribute_label($label, $key) {
		$list = $this->get_booking_fields( true );
		if ( isset( $list[$key] ) ) {
			return $list[$key];
		} elseif ( $this->bookingDataPrefixInOrderItem ) {
			$cleanKey = preg_replace( '/^' . $this->bookingDataPrefixInOrderItem . '/', '', $key );
			if ( $cleanKey != $key && isset( $list[$cleanKey] ) ) {
				return $list[$cleanKey];
			}
		}
		return $label;
	}

	/**
	 * Loads booking form fileds for each tour added to shopping cart.
	 */
	public function filter_woocommerce_get_cart_item_from_session($item,$values,$key) {
		$keys = $this->get_booking_fields();
		foreach ( $keys as $key ) {
			if ( array_key_exists( $key, $values ) ) {
				$item[$key] = $values[$key];
			}
		}
		return $item;
	}

	/**
	 * Filter for rendering tour booking attributes on the cart and checkout page.
	 *
	 * @param  array $current_set
	 * @param  assoc $cart_item
	 * @return array
	 */
	public function filter_woocommerce_get_item_data($current_set, $cart_item){

		/*$is_tour = $cart_item['data'] && $cart_item['data']->is_type('tour');
		if ( ! $is_tour ) {
			return $current_set;
		}*/

		$keys = $this->get_booking_fields(true);
		foreach ( $keys as $key => $label ) {
			if ( array_key_exists( $key, $cart_item ) && ! empty( $cart_item[$key] ) ) {
				$current_set[] = array(
					'name' => $label,
					'value' => $cart_item[$key]
				);
			}
		}

		return $current_set;
	}

	/**
	 * Action that saves tour related data to order item.
	 *
	 * @param  string $item_id
	 * @param  assoc  $values  item meta
	 * @return void
	 */
	public function filter_woocommerce_add_order_item_meta($item_id, $values) {
		$keys = $this->get_booking_fields();
		foreach ( $keys as $key ) {
			if ( ! empty( $values[$key] ) ) {
				$value = $values[$key];
				if ( $key == 'date' ) {
					if ( $formatted_value = $this->convert_date( $this->get_date_format(), $value ) ) {
						$value = $formatted_value;
					}
				}

				wc_add_order_item_meta( $item_id, $this->bookingDataPrefixInOrderItem . $key, $value );
			}
		}
	}

	public function get_open_tour_tickets($tour_id, $date) {
		// need convert date to Y-m-d format
		$converted_date = $this->convert_date( $this->get_date_format(), $date );

		return adventure_tours_di( 'tour_booking_service' )->get_open_tickets( $tour_id, $converted_date );
	}

	public function validateRequestData($requestData) {
		$errors = array();

		$tour_id = ! empty( $requestData['tour_id'] ) ? $requestData['tour_id'] : null;
		$date = ! empty( $requestData['tour_data']['date'] ) ? $requestData['tour_data']['date'] : null;

		if ( $tour_id && $date ) {
			$max_allowed_tickets = $this->get_open_tour_tickets( $tour_id, $date );
			$qty = isset( $requestData['quantity'] ) ? $requestData['quantity'] : 0;

			if ( $max_allowed_tickets < 1 ) {
				$errors['date'] = array(
					esc_html__( 'There are no tickets for this date.', 'adventure-tours' )
				);
			}

			if ( $qty < 1 ) {
				$errors['quantity'] = array(
					esc_html__( 'Please enter the amount of tickets.', 'adventure-tours' )
				);
			} else if ( $max_allowed_tickets > 0 && $qty > $max_allowed_tickets ) {
				$errors['quantity'] = array(
					$max_allowed_tickets > 1
						? sprintf( esc_html__( 'Only %s tickets are left.', 'adventure-tours' ), $max_allowed_tickets )
						: esc_html__( 'Only 1 ticket is left.', 'adventure-tours' )
				);
			}
		}

		return $errors;
	}

	public function validateFields($fields) {
		$errors = array();

		foreach ( $fields as $key => $value ) {
			$fieldErrors = $this->validateField( $value, $key );
			if ( $fieldErrors ) {
				$errors[$key] = $fieldErrors;
			}
		}

		return $errors;
	}

	public function validateField($value, $fieldKey) {
		$errors = array();
		$requiredFields = array( 'name', 'email', 'date' );

		if ( in_array( $fieldKey, $requiredFields ) && empty( $value ) ) {
			$errors[] = esc_html__( 'Fill in the required field.', 'adventure-tours' );
		}

		if ( ! $errors ) {
			switch ( $fieldKey ) {
				case 'email':
					if ( ! filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
						$errors[] = esc_html__( 'Email invalid.', 'adventure-tours' );
					}
				break;

				case 'date':
					$converted_date = $this->convert_date( $this->get_date_format(), $value );
					// if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $value ) ) {
					if ( ! $converted_date ) {
						$errors[] = esc_html__( 'Date invalid.', 'adventure-tours' );
					}
				break;
			}
		}

		return $errors;
	}

	/**
	 * Returns date format for the booking form.
	 *
	 * @param  string $for target element for that date should be returned.
	 * @return string
	 */
	public function get_date_format( $for = 'php' ) {
		$date_format = $this->date_format;
		if ( 'datepicker' == $for) {
			$replacement = array(
				'm' => 'mm',
				'd' => 'dd',
				'Y' => 'yy',
			);
			return str_replace( array_keys( $replacement ), $replacement, $date_format );
		}
		return $date_format;
	}

	/**
	 * Converts date from $in_format to $out_format.
	 *
	 * @param  string $in_format  format used for a date value.
	 * @param  string $date
	 * @param  string $out_format output format.
	 * @return string
	 */
	public function convert_date($in_format, $date, $out_format = 'Y-m-d') {
		$possible_delimiters = array(' ', '/', '-');

		$fixed_format = str_replace( $possible_delimiters, '|', $in_format );
		$vars_list = explode( '|', $fixed_format );

		$dayIndex = array_search('d', $vars_list);
		$monthIndex = array_search('m', $vars_list);
		$yearIndex = array_search('Y', $vars_list);

		$time = null;
		if ( false !== $dayIndex || false !== $monthIndex || false !== $yearIndex ) {
			$fixed_delimiters = str_replace( $possible_delimiters, '|', $date );
			$parts = explode('|', $fixed_delimiters);

			$day = isset($parts[$dayIndex]) ? $parts[$dayIndex] : null;
			$month = isset($parts[$monthIndex]) ? $parts[$monthIndex] : null;
			$year = isset($parts[$yearIndex]) ? $parts[$yearIndex] : null;

			if ( $day && $month && $year ) {
				$time = strtotime("{$year}-{$month}-{$day}");
			}
		}

		if ( $time ) {
			return date( $out_format, $time );
		}
		return null;
	}
}
