<?php
/**
 * Recovered revenue tracking.
 *
 * @package Revenue_Recover
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tracks recovered failed payments and provides simple totals.
 */
class Revenue_Recover_Revenue_Tracker {

	const META_RECOVERED_AT         = '_revenue_recover_recovered_at';
	const META_RECOVERED_AMOUNT     = '_revenue_recover_recovered_amount';
	const META_RECOVERY_PROCESSING  = '_revenue_recover_recovery_processing';

	/**
	 * Registers hooks.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'woocommerce_payment_complete', array( $this, 'maybe_mark_order_recovered' ), 10, 1 );
		add_action( 'woocommerce_order_status_changed', array( $this, 'maybe_mark_order_recovered_from_status_change' ), 10, 4 );
	}

	/**
	 * Marks an order recovered after a paid status transition.
	 *
	 * @param int           $order_id   Order ID.
	 * @param string        $old_status Previous status.
	 * @param string        $new_status New status.
	 * @param WC_Order|null $order      Order object.
	 * @return void
	 */
	public function maybe_mark_order_recovered_from_status_change( $order_id, $old_status, $new_status, $order = null ) {
		$this->maybe_mark_order_recovered( $order instanceof WC_Order ? $order : $order_id );
	}

	/**
	 * Marks a tracked failed order as recovered when it becomes paid.
	 *
	 * @param int|WC_Order $order Order ID or object.
	 * @return void
	 */
	public function maybe_mark_order_recovered( $order ) {
		if ( ! $order instanceof WC_Order ) {
			$order = wc_get_order( absint( $order ) );
		}

		if ( ! $order instanceof WC_Order || ! $this->is_tracked_order( $order ) || $this->is_recovered_order( $order ) || $this->is_recovery_processing( $order ) || ! $order->is_paid() ) {
			return;
		}

		$order = $this->mark_recovery_processing( $order );

		if ( ! $order ) {
			return;
		}

		try {
			$order->update_meta_data( Revenue_Recover_Failed_Payment_Detector::META_RECOVERY_STATUS, sanitize_key( Revenue_Recover_Failed_Payment_Detector::RECOVERY_STATUS_RECOVERED ) );
			$order->update_meta_data( self::META_RECOVERED_AT, absint( current_time( 'timestamp', true ) ) );
			$order->update_meta_data( self::META_RECOVERED_AMOUNT, wc_format_decimal( max( 0, (float) $order->get_total() ) ) );
			$order->delete_meta_data( self::META_RECOVERY_PROCESSING );
			$order->save();
		} catch ( Exception $exception ) {
			$this->clear_recovery_processing( $order );
			return;
		}

		/**
		 * Fires after Revenue Recover marks an order as recovered.
		 *
		 * @param int      $order_id Order ID.
		 * @param WC_Order $order    Order object.
		 */
		do_action( 'revenue_recover_order_recovered', $order->get_id(), $order );
	}

	/**
	 * Gets total tracked failed payments.
	 *
	 * @return int
	 */
	public function get_total_failed_payments_count() {
		return count( $this->get_tracked_orders() );
	}

	/**
	 * Gets total failed amount.
	 *
	 * @return float
	 */
	public function get_total_failed_amount() {
		$total = 0.0;

		foreach ( $this->get_tracked_orders() as $order ) {
			$total += max( 0, (float) $order->get_meta( Revenue_Recover_Failed_Payment_Detector::META_FAILED_AMOUNT, true ) );
		}

		return $total;
	}

	/**
	 * Gets recovered orders count.
	 *
	 * @return int
	 */
	public function get_recovered_orders_count() {
		return count( $this->get_recovered_orders() );
	}

	/**
	 * Gets recovered revenue.
	 *
	 * @return float
	 */
	public function get_recovered_revenue() {
		$total = 0.0;

		foreach ( $this->get_recovered_orders() as $order ) {
			$total += max( 0, (float) $order->get_meta( self::META_RECOVERED_AMOUNT, true ) );
		}

		return $total;
	}

	/**
	 * Gets recovery rate as a percentage.
	 *
	 * @return float
	 */
	public function get_recovery_rate() {
		$total_failed = $this->get_total_failed_payments_count();

		if ( 0 === $total_failed ) {
			return 0.0;
		}

		return round( ( $this->get_recovered_orders_count() / $total_failed ) * 100, 2 );
	}

	/**
	 * Gets recent tracked failed orders.
	 *
	 * @param int $limit Number of orders to return.
	 * @return WC_Order[]
	 */
	public function get_recent_failed_orders( $limit = 10 ) {
		$limit  = max( 1, absint( $limit ) );
		$orders = $this->get_tracked_orders();

		usort(
			$orders,
			function ( $order_a, $order_b ) {
				return $this->get_failed_timestamp( $order_b ) <=> $this->get_failed_timestamp( $order_a );
			}
		);

		return array_slice( $orders, 0, $limit );
	}

	/**
	 * Checks whether the order is tracked by Revenue Recover.
	 *
	 * @param WC_Order $order Order object.
	 * @return bool
	 */
	protected function is_tracked_order( WC_Order $order ) {
		return $order->meta_exists( Revenue_Recover_Failed_Payment_Detector::META_RECOVERY_STATUS );
	}

	/**
	 * Checks whether the order has already been recovered.
	 *
	 * @param WC_Order $order Order object.
	 * @return bool
	 */
	protected function is_recovered_order( WC_Order $order ) {
		$status       = sanitize_key( $order->get_meta( Revenue_Recover_Failed_Payment_Detector::META_RECOVERY_STATUS, true ) );
		$recovered_at = absint( $order->get_meta( self::META_RECOVERED_AT, true ) );

		return Revenue_Recover_Failed_Payment_Detector::RECOVERY_STATUS_RECOVERED === $status || $recovered_at > 0;
	}

	/**
	 * Checks whether recovery is already being processed.
	 *
	 * @param WC_Order $order Order object.
	 * @return bool
	 */
	protected function is_recovery_processing( WC_Order $order ) {
		return '' !== sanitize_text_field( $order->get_meta( self::META_RECOVERY_PROCESSING, true ) );
	}

	/**
	 * Marks recovery as being processed.
	 *
	 * @param WC_Order $order Order object.
	 * @return WC_Order|false
	 */
	protected function mark_recovery_processing( WC_Order $order ) {
		$token = wp_generate_uuid4();
		$order = wc_get_order( $order->get_id() );

		if ( ! $order instanceof WC_Order || ! $this->is_tracked_order( $order ) || $this->is_recovered_order( $order ) || $this->is_recovery_processing( $order ) || ! $order->is_paid() ) {
			return false;
		}

		$order->update_meta_data( self::META_RECOVERY_PROCESSING, $token );
		$order->save();

		$order = wc_get_order( $order->get_id() );

		if ( ! $order instanceof WC_Order || $token !== $order->get_meta( self::META_RECOVERY_PROCESSING, true ) ) {
			return false;
		}

		return $order;
	}

	/**
	 * Clears the recovery processing flag.
	 *
	 * @param WC_Order $order Order object.
	 * @return void
	 */
	protected function clear_recovery_processing( WC_Order $order ) {
		$order->delete_meta_data( self::META_RECOVERY_PROCESSING );
		$order->save();
	}

	/**
	 * Gets the stored failed timestamp.
	 *
	 * @param WC_Order $order Order object.
	 * @return int
	 */
	protected function get_failed_timestamp( WC_Order $order ) {
		return absint( $order->get_meta( Revenue_Recover_Failed_Payment_Detector::META_FAILED_AT, true ) );
	}

	/**
	 * Gets tracked failed payment orders.
	 *
	 * @return WC_Order[]
	 */
	protected function get_tracked_orders() {
		$args = array(
			'limit'      => -1,
			'return'     => 'objects',
			'type'       => 'shop_order',
			'meta_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => Revenue_Recover_Failed_Payment_Detector::META_RECOVERY_STATUS,
					'compare' => 'EXISTS',
				),
			),
		);

		/**
		 * Filters the query args for tracked failed payment orders.
		 *
		 * @param array $args Query args.
		 */
		return wc_get_orders( apply_filters( 'revenue_recover_tracked_orders_query_args', $args ) );
	}

	/**
	 * Gets recovered orders.
	 *
	 * @return WC_Order[]
	 */
	protected function get_recovered_orders() {
		$args = array(
			'limit'      => -1,
			'return'     => 'objects',
			'type'       => 'shop_order',
			'meta_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'   => Revenue_Recover_Failed_Payment_Detector::META_RECOVERY_STATUS,
					'value' => Revenue_Recover_Failed_Payment_Detector::RECOVERY_STATUS_RECOVERED,
				),
			),
		);

		/**
		 * Filters the query args for recovered orders.
		 *
		 * @param array $args Query args.
		 */
		return wc_get_orders( apply_filters( 'revenue_recover_recovered_orders_query_args', $args ) );
	}
}
