<?php
/**
 * Failed payment detection.
 *
 * @package Revenue_Recover
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tracks failed WooCommerce orders as Revenue Recover candidates.
 */
class Revenue_Recover_Failed_Payment_Detector {

	const META_RECOVERY_STATUS     = '_revenue_recover_recovery_status';
	const META_FAILED_AMOUNT       = '_revenue_recover_failed_amount';
	const META_FAILED_AT           = '_revenue_recover_failed_at';
	const META_RETRY_EMAIL_SENT    = '_revenue_recover_retry_email_sent';
	const META_RETRY_EMAIL_SENT_AT = '_revenue_recover_retry_email_sent_at';

	const RECOVERY_STATUS_PENDING   = 'pending';
	const RECOVERY_STATUS_RECOVERED = 'recovered';

	/**
	 * Retry email service.
	 *
	 * @var Revenue_Recover_Retry_Email
	 */
	protected $retry_email;

	/**
	 * Sets up dependencies.
	 *
	 * @param Revenue_Recover_Retry_Email $retry_email Retry email service.
	 */
	public function __construct( Revenue_Recover_Retry_Email $retry_email ) {
		$this->retry_email = $retry_email;
	}

	/**
	 * Registers hooks.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'woocommerce_order_status_failed', array( $this, 'record_failed_payment' ), 10, 2 );
	}

	/**
	 * Records a failed order as a recovery candidate.
	 *
	 * @param int           $order_id Order ID.
	 * @param WC_Order|null $order    Order object.
	 * @return void
	 */
	public function record_failed_payment( $order_id, $order = null ) {
		$order_id = absint( $order_id );

		if ( ! $order_id ) {
			return;
		}

		if ( ! $order instanceof WC_Order ) {
			$order = wc_get_order( $order_id );
		}

		if ( ! $order instanceof WC_Order ) {
			return;
		}

		if ( ! $this->is_already_tracked( $order ) ) {
			$order->update_meta_data( self::META_RECOVERY_STATUS, sanitize_key( self::RECOVERY_STATUS_PENDING ) );
			$order->update_meta_data( self::META_FAILED_AMOUNT, wc_format_decimal( max( 0, (float) $order->get_total() ) ) );
			$order->update_meta_data( self::META_FAILED_AT, absint( current_time( 'timestamp', true ) ) );
			$order->update_meta_data( self::META_RETRY_EMAIL_SENT, 'no' );
			$order->save();

			/**
			 * Fires after Revenue Recover records a failed payment candidate.
			 *
			 * @param int      $order_id Order ID.
			 * @param WC_Order $order    Order object.
			 */
			do_action( 'revenue_recover_failed_payment_recorded', $order_id, $order );
		}

		$this->retry_email->send( $order );
	}

	/**
	 * Checks whether this order has already been tracked.
	 *
	 * @param WC_Order $order Order object.
	 * @return bool
	 */
	protected function is_already_tracked( WC_Order $order ) {
		return $order->meta_exists( self::META_RECOVERY_STATUS );
	}
}
