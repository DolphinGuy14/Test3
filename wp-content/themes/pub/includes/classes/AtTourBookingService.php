<?php
/**
 * Class for saving/processing tour booking periods.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

class AtTourBookingService extends TdComponent
{
	/**
	 * Determines what post meta key should be used for the tour booking periods storing in DB.
	 *
	 * @var string
	 */
	public $meta_key = 'tour_booking_periods';

	/**
	 * Limit that prevents too long looping during period expanding into set of days
	 * for that booking is available.
	 *
	 * @see expand_period method.
	 * @var integer
	 */
	public $expandIterationsLimit = 5000;

	/**
	 * List of order statuses booking for that is active (order statuses missed in this list will be considered as declined/inactive).
	 *
	 * @see get_booking_records
	 * @var array
	 */
	public $order_statuses_with_active_tour_booking = array(
		'wc-pending',
		'wc-processing',
		'wc-on-hold',
		'wc-completed',
		// 'wc-cancelled','wc-refunded','wc-failed',
	);

	/**
	 * Returns set of the saved periods related to the specefied period.
	 *
	 * @param  int $post_id
	 * @return array
	 */
	public function get_rows( $post_id ) {
		if ( $post_id > 0 && $this->meta_key ) {
			$rows = get_post_meta( $post_id, $this->meta_key, true );
		} else {
			$rows = null;
		}

		return $rows ? $rows : array();
	}

	/**
	 * Saves new period set to DB.
	 *
	 * @param int     $post_id
	 * @param assoc   $periods
	 * @param boolean $validate
	 * @return assoc  errors hppaned during validation
	 */
	public function set_rows( $post_id, $periods, $validate = true ) {
		$validation_errors = array();
		if ( $post_id < 1 || ! $this->meta_key ) {
			$validation_errors['general'] = esc_html__( 'Parametes errors. Please contact support', 'adventure-tours' );
			return $validation_errors;
		}

		if ( ! $periods ) {
			delete_post_meta( $post_id, $this->meta_key );
		} else {
			if ( $validate ) {
				foreach( $periods as $index => $period_data ) {
					$period_errors = $this->check_period_data( $period_data );
					if ( $period_errors ) {
						$validation_errors[ $index ] = $period_errors;
					}
				}
				if ( $validation_errors ) {
					return $validation_errors;
				}
			}
			update_post_meta( $post_id, $this->meta_key, $periods );
		}

		return $validation_errors;
	}

	/**
	 * Set of days for that booking is avaliable.
	 *
	 * @param  int     $post_id
	 * @param  string  $from_date              allows limit range of days that should be involved.
	 * @param  string  $to_date                allows limit range of days that should be involved.
	 * @param  boolean $exclude_booked_tickets if already booked tickets should be taken in consideration.
	 * @return array
	 */
	public function get_expanded( $post_id, $from_date = null, $to_date = null, $exclude_booked_tickets = true ) {
		$rows = $this->get_periods( $post_id, $from_date, $to_date );
		return $this->expand_periods(
			$rows,
			$exclude_booked_tickets ? $post_id : 0,
			$from_date,
			$to_date
		);
	}

	/**
	 * Expands passed periods into set of dates with available for booking tickets number.
	 *
	 * @param  array   $periods
	 * @param  integer $exclude_for_tour_id tour id booking for that should be deducted from expanded periods.
	 * @param  [type]  $from_date           allows limit range of days that should be involved.
	 * @param  [type]  $to_date             allows limit range of days that should be involved.
	 * @return array
	 */
	public function expand_periods( $periods, $exclude_for_tour_id = 0, $from_date = null, $to_date = null ) {
		$result = array();

		if ( $periods ) {
			foreach ( $periods as $period ) {
				$expandedDays = $this->expand_period( $period );
				if ( $expandedDays ) {
					$result = array_merge( $result, $expandedDays );
				}
			}

			if ( $result && $exclude_for_tour_id > 0 ) {
				$booked_tickets = $this->get_booking_data( $exclude_for_tour_id, $from_date, $to_date );
				if ( $booked_tickets ) {
					foreach ( $booked_tickets as $booking_date => $qnt ) {
						if ( isset( $result[$booking_date] ) ) {
							$result[$booking_date] -= $qnt;
						}
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Returns number of the open tickets for specific tour for specific date.
	 *
	 * @param  int    $tour_id
	 * @param  string $date
	 * @return int
	 */
	public function get_open_tickets($tour_id, $date) {
		$allowed_dates = $tour_id && $date ? $this->get_expanded( $tour_id, $date, $date ) : array();

		if ( $allowed_dates && isset( $allowed_dates[$date] ) ) {
			return $allowed_dates[$date];
		} else {
			return 0;
		}
	}

	/**
	 * Returns set of periods related to the selected tour (and some specific period).
	 *
	 * @param  int    $post_id
	 * @param  string $from_date allows limit range of days that should be involved.
	 * @param  string $to_date allows limit range of days that should be involved.
	 * @return array
	 */
	public function get_periods( $post_id, $from_date = null, $to_date = null ) {
		$rows = $this->get_rows( $post_id );

		if ( $rows && ( $from_date || $to_date ) ) {
			$result = array();
			foreach ( $rows as $period ) {
				$newSet = $this->get_inersected_period( $period, array(
					'from' => $from_date,
					'to' => $to_date,
				) );
				if ( $newSet ) {
					$result[] = $newSet;
				}
			}
			return $result;
		}

		return $rows;
	}

	/**
	 * Converts passed period into a set of days for that booking is available.
	 *
	 * @param  assoc  $period
	 * @return array
	 */
	public function expand_period( $period ) {
		$step = '+1 day';
		$result = array();

		if ( !isset( $period['type'] ) || $period['type'] != '1' ) {
			return $result;
		}
		$iterationsLimit = $this->expandIterationsLimit > 1 ? $this->expandIterationsLimit : 5000;

		$curTime = $this->toTime( $period['from'] );
		$endTime = $this->toTime( $period['to'] );

		$limit = isset( $period['limit'] ) ? $period['limit'] : '1';

		if ( $curTime && $endTime && $curTime <= $endTime ) {
			$allowedDays = ! empty( $period['days'] ) ? $period['days'] : array();
			while ( $curTime <= $endTime && $iterationsLimit ) {
				$nDay = date( 'D', $curTime );
				if ( in_array( $nDay, $allowedDays ) ) {
					$result[ date( 'Y-m-d', $curTime ) ] = $limit;
				}
				$curTime = strtotime( $step, $curTime );
				$iterationsLimit--;
			}
		}

		return $result;
	}

	/**
	 * Makes intersected period based on dates from period and restriction period.
	 *
	 * @param  assoc $p1
	 * @param  assoc $p2
	 * @return assoc
	 */
	public function get_inersected_period($p1, $p2) {
		$s1 = $this->toTime( $p1['from'] );
		$e1 = $this->toTime( $p1['to'] );

		$s2 = $this->toTime( $p2['from'] );
		$e2 = $this->toTime( $p2['to'] );

		if ( $s1 && $e1 && $s2 && $e2 ) {
			if ( $s2 <= $e1 && $e2 >= $s1 ) {
				return array_merge( $p1, array(
					'from' => max( $s1, $s2 ),
					'to' => min( $e1, $e2 ),
				) );
			}
		}
		return null;
	}

	/**
	 * Converts sting date presentation into timestamp.
	 *
	 * @param  string|int $date_string date string or timestamp
	 * @return int
	 */
	public function toTime( $date_string ) {
		if ( ! $date_string ) {
			return null;
		}
		if ( is_int( $date_string ) ) {
			return $date_string;
		}
		return strtotime( $date_string );
	}

	/**
	 * Returns data related the tour booking.
	 *
	 * @param  int    $tour_id
	 * @param  string $from_date optional, allow restict search timeframe.
	 * @param  string $to_date   optional, allow restict search timeframe.
	 * @return assoc
	 */
	public function get_booking_data( $tour_id, $from_date = null, $to_date = null ) {

		$records_set = $this->get_booking_records( $tour_id, $from_date, $to_date );

		$result = array();
		if ( $records_set ) {
			foreach ( $records_set as $order_report ) {
				$cur_tour_id = $order_report['tour_id'];
				$date = $order_report['booking_date'];
				$qty = $order_report['qty'];
				if ( ! isset( $result[$cur_tour_id] ) ) {
					$result[$cur_tour_id] = array();
				}
				if ( ! empty( $result[$cur_tour_id][$date] ) ) {
					$result[$cur_tour_id][$date] += $qty;
				} else {
					$result[$cur_tour_id][$date] = $qty;
				}
			}
		}
		if ( $tour_id ) {
			return isset( $result[$tour_id] ) ? $result[$tour_id] : array();
		}

		return $result;
	}

	/**
	 * Retrives set of records about the booking events from DB.
	 *
	 * @param  int    $tour_id   optional, allow restrict loaded set with 1 specific tour.
	 * @param  string $from_date optional, allow restict search timeframe.
	 * @param  string $to_date   optional, allow restict search timeframe.
	 * @return array
	 */
	public function get_booking_records( $tour_id = null, $from_date = null, $to_date = null ) {
//TODO improve filtering for booking records loading
		global $wpdb;
		$dates_condition = '';
		if ( $from_date || $to_date ) {
			if ( $from_date && $to_date ) {
				if ( $from_date == $to_date ) {
					$dates_condition = " AND im.meta_value = '{$from_date}'";
				} else {
					// make convertation to date?
					//$dates_condition = 'im.date BETWEEN "{$from_date}" AND "{$to_date}"';
				}
			} else if ( $from_date ) {
				// make convertation to date?
			} else if ( $to_date ) {
				// make convertation to date?
			}
		}

		$tour_condition = '';
		if ( $tour_id ) {
			$tour_condition = " AND pidmeta.meta_value = '{$tour_id}'";
		}

		$tour_date_meta_key = 'tour_date';
		$product_id_meta_key = '_product_id';
		$qty_meta_key = '_qty';

		$status_condition = $this->order_statuses_with_active_tour_booking ? " AND o.post_status IN ('" . join( "','", $this->order_statuses_with_active_tour_booking ) . "')" : '';

// $wpdb->prepare( 
		$query = "SELECT i.order_id, i.order_item_id, pidmeta.meta_value as tour_id, qntmeta.meta_value as qty, im.meta_value as booking_date, o.post_status as order_status 
			FROM `{$wpdb->prefix}woocommerce_order_itemmeta` im
			RIGHT JOIN `{$wpdb->prefix}woocommerce_order_items` i on im.order_item_id = i.order_item_id
			RIGHT JOIN `{$wpdb->prefix}posts` o on i.order_id = o.ID
			RIGHT JOIN `{$wpdb->prefix}woocommerce_order_itemmeta` pidmeta on pidmeta.order_item_id = im.order_item_id AND pidmeta.meta_key = '{$product_id_meta_key}'
			RIGHT JOIN `{$wpdb->prefix}woocommerce_order_itemmeta` qntmeta on qntmeta.order_item_id = im.order_item_id AND qntmeta.meta_key = '{$qty_meta_key}'
			WHERE im.meta_key = '{$tour_date_meta_key}'"
				. $tour_condition
				. $status_condition
				. $dates_condition;

		return $wpdb->get_results( $query, ARRAY_A );
	}

	/**
	 * Validation function. Checks single period data for errors and returns assoc with field errors.
	 *
	 * @param  assoc $data period fileds.
	 * @return assoc
	 */
	public function check_period_data( $data ) {
		$from = !empty( $data['from'] ) ? $this->toTime( $data['from'] ) : null;
		$to = !empty( $data['to'] ) ? $this->toTime( $data['to'] ) : null;

		$errors = array();

		$keys = array( 'from', 'to', 'limit', 'days' );
		foreach ( $keys as $field_key ) {
			$field_errors = array();
			$value = isset( $data[ $field_key ] ) ? $data[ $field_key ] : null;

			if ( empty( $value ) ) {
				if ( 'days' == $field_key ) {
					$field_errors[] = esc_html__( 'Please select at least one day.', 'adventure-tours' );
				} else {
					$field_errors[] = esc_html__( 'The field is required.', 'adventure-tours' );
				}
			} else {
				switch( $field_key ) {
				case 'limit':
					if ( $value < 1 ) {
						$field_errors[] = esc_html__( 'Minimum allowed value is 1.', 'adventure-tours' );
					}
					break;

				case 'from':
					if ( ! $from ) {
						$field_errors[] = esc_html__( 'Please check the date format.', 'adventure-tours' );
					}
					break;

				case 'to':
					if ( ! $to ) {
						$field_errors[] = esc_html__( 'Please check the date format.', 'adventure-tours' );
					} elseif ( $from && $to < $from ) {
						$field_errors[] = sprintf( esc_html__( 'The date should be grater than %s.', 'adventure-tours' ), $data['from'] );
					}
					break;
				}
			}
			if ( $field_errors ) {
				$errors[ $field_key ] = $field_errors;
			}
		}

		return $errors;
	}
}
