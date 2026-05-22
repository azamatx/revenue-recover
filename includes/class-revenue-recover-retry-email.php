<?php
/**
 * Retry payment email.
 *
 * @package Revenue_Recover
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sends the single failed-payment retry email.
 */
class Revenue_Recover_Retry_Email {

	const META_RETRY_EMAIL_SENDING = '_revenue_recover_retry_email_sending';

	/**
	 * Sends the retry payment email.
	 *
	 * @param WC_Order $order Order object.
	 * @return bool
	 */
	public function send( WC_Order $order ) {
		if ( $this->is_sent( $order ) || $this->is_sending( $order ) ) {
			return false;
		}

		$to = sanitize_email( $order->get_billing_email() );

		if ( ! is_email( $to ) ) {
			return false;
		}

		$payment_url = $this->get_payment_url( $order );

		if ( empty( $payment_url ) ) {
			return false;
		}

		$order = $this->mark_sending( $order );

		if ( ! $order ) {
			return false;
		}

		$subject = $this->get_subject( $order );
		$heading = $this->get_heading( $order );
		$message = $this->get_message( $order, $payment_url );
		$sent    = $this->send_email( $to, $subject, $heading, $message );

		if ( ! $sent ) {
			$this->clear_sending( $order );
			return false;
		}

		$this->mark_sent( $order );

		/**
		 * Fires after Revenue Recover sends a retry payment email.
		 *
		 * @param int      $order_id Order ID.
		 * @param WC_Order $order    Order object.
		 */
		do_action( 'revenue_recover_retry_email_sent', $order->get_id(), $order );

		return true;
	}

	/**
	 * Checks whether the retry email has already been sent.
	 *
	 * @param WC_Order $order Order object.
	 * @return bool
	 */
	protected function is_sent( WC_Order $order ) {
		$sent    = sanitize_key( $order->get_meta( Revenue_Recover_Failed_Payment_Detector::META_RETRY_EMAIL_SENT, true ) );
		$sent_at = absint( $order->get_meta( Revenue_Recover_Failed_Payment_Detector::META_RETRY_EMAIL_SENT_AT, true ) );

		return 'yes' === $sent || $sent_at > 0;
	}

	/**
	 * Checks whether the retry email is already being sent.
	 *
	 * @param WC_Order $order Order object.
	 * @return bool
	 */
	protected function is_sending( WC_Order $order ) {
		return 'yes' === sanitize_key( $order->get_meta( self::META_RETRY_EMAIL_SENDING, true ) );
	}

	/**
	 * Marks the retry email as being sent.
	 *
	 * @param WC_Order $order Order object.
	 * @return WC_Order|false
	 */
	protected function mark_sending( WC_Order $order ) {
		$order = wc_get_order( $order->get_id() );

		if ( ! $order instanceof WC_Order || $this->is_sent( $order ) || $this->is_sending( $order ) ) {
			return false;
		}

		$order->update_meta_data( self::META_RETRY_EMAIL_SENDING, 'yes' );
		$order->save();

		return $order;
	}

	/**
	 * Gets the native WooCommerce payment retry URL.
	 *
	 * @param WC_Order $order Order object.
	 * @return string
	 */
	protected function get_payment_url( WC_Order $order ) {
		$payment_url = $order->get_checkout_payment_url();

		/**
		 * Filters the retry payment URL.
		 *
		 * @param string   $payment_url Payment URL.
		 * @param WC_Order $order       Order object.
		 */
		return esc_url_raw( apply_filters( 'revenue_recover_retry_payment_url', $payment_url, $order ) );
	}

	/**
	 * Gets the retry email subject.
	 *
	 * @param WC_Order $order Order object.
	 * @return string
	 */
	protected function get_subject( WC_Order $order ) {
		$subject = sprintf(
			/* translators: %s: Site title. */
			__( 'Complete your payment at %s', 'revenue-recover' ),
			wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES )
		);

		/**
		 * Filters the retry email subject.
		 *
		 * @param string   $subject Email subject.
		 * @param WC_Order $order   Order object.
		 */
		return wp_strip_all_tags( apply_filters( 'revenue_recover_retry_email_subject', $subject, $order ) );
	}

	/**
	 * Gets the retry email heading.
	 *
	 * @param WC_Order $order Order object.
	 * @return string
	 */
	protected function get_heading( WC_Order $order ) {
		$heading = __( 'Complete your payment', 'revenue-recover' );

		/**
		 * Filters the retry email heading.
		 *
		 * @param string   $heading Email heading.
		 * @param WC_Order $order   Order object.
		 */
		return wp_strip_all_tags( apply_filters( 'revenue_recover_retry_email_heading', $heading, $order ) );
	}

	/**
	 * Gets the retry email HTML message.
	 *
	 * @param WC_Order $order       Order object.
	 * @param string   $payment_url Payment URL.
	 * @return string
	 */
	protected function get_message( WC_Order $order, $payment_url ) {
		$first_name = $order->get_billing_first_name();
		$greeting   = $first_name
			? sprintf(
				/* translators: %s: Customer first name. */
				__( 'Hi %s,', 'revenue-recover' ),
				$first_name
			)
			: __( 'Hi,', 'revenue-recover' );

		$message  = '<p>' . esc_html( $greeting ) . '</p>';
		$message .= '<p>' . esc_html__( 'Your payment was not completed, but your order is still waiting for you.', 'revenue-recover' ) . '</p>';
		$message .= '<p>' . esc_html__( 'Use the secure link below to complete your payment.', 'revenue-recover' ) . '</p>';
		$message .= '<p><a href="' . esc_url( $payment_url ) . '">' . esc_html__( 'Complete Your Payment', 'revenue-recover' ) . '</a></p>';

		/**
		 * Filters the retry email message.
		 *
		 * @param string   $message     Email message HTML.
		 * @param WC_Order $order       Order object.
		 * @param string   $payment_url Payment URL.
		 */
		return wp_kses_post( apply_filters( 'revenue_recover_retry_email_message', $message, $order, $payment_url ) );
	}

	/**
	 * Sends the email using WooCommerce mail handling when available.
	 *
	 * @param string $to      Recipient email address.
	 * @param string $subject Email subject.
	 * @param string $heading Email heading.
	 * @param string $message Email message HTML.
	 * @return bool
	 */
	protected function send_email( $to, $subject, $heading, $message ) {
		if ( function_exists( 'wc_mail' ) && function_exists( 'WC' ) ) {
			$woocommerce = WC();
			$mailer      = $woocommerce ? $woocommerce->mailer() : null;

			if ( $mailer ) {
				$message = $mailer->wrap_message( $heading, $message );

				return wc_mail( $to, $subject, $message );
			}
		}

		return wp_mail(
			$to,
			$subject,
			$message,
			array( 'Content-Type: text/html; charset=UTF-8' )
		);
	}

	/**
	 * Marks the retry email as sent.
	 *
	 * @param WC_Order $order Order object.
	 * @return void
	 */
	protected function mark_sent( WC_Order $order ) {
		$order->update_meta_data( Revenue_Recover_Failed_Payment_Detector::META_RETRY_EMAIL_SENT, 'yes' );
		$order->update_meta_data( Revenue_Recover_Failed_Payment_Detector::META_RETRY_EMAIL_SENT_AT, absint( current_time( 'timestamp', true ) ) );
		$order->delete_meta_data( self::META_RETRY_EMAIL_SENDING );
		$order->save();
	}

	/**
	 * Clears the retry email sending flag.
	 *
	 * @param WC_Order $order Order object.
	 * @return void
	 */
	protected function clear_sending( WC_Order $order ) {
		$order->delete_meta_data( self::META_RETRY_EMAIL_SENDING );
		$order->save();
	}
}
