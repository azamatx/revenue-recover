<?php
/**
 * Main plugin bootstrap class.
 *
 * @package Revenue_Recover
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Boots Revenue Recover.
 */
class Revenue_Recover_Plugin {

	/**
	 * Plugin instance.
	 *
	 * @var Revenue_Recover_Plugin|null
	 */
	protected static $instance = null;

	/**
	 * Retry email service.
	 *
	 * @var Revenue_Recover_Retry_Email|null
	 */
	protected $retry_email = null;

	/**
	 * Revenue tracker.
	 *
	 * @var Revenue_Recover_Revenue_Tracker|null
	 */
	protected $revenue_tracker = null;

	/**
	 * Admin dashboard.
	 *
	 * @var Revenue_Recover_Admin_Dashboard|null
	 */
	protected $admin_dashboard = null;

	/**
	 * Failed payment detector.
	 *
	 * @var Revenue_Recover_Failed_Payment_Detector|null
	 */
	protected $failed_payment_detector = null;

	/**
	 * Returns the plugin instance.
	 *
	 * @return Revenue_Recover_Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Sets up the plugin.
	 */
	protected function __construct() {
		$this->init_hooks();
	}

	/**
	 * Registers plugin hooks.
	 *
	 * @return void
	 */
	protected function init_hooks() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		$this->retry_email             = new Revenue_Recover_Retry_Email();
		$this->revenue_tracker         = new Revenue_Recover_Revenue_Tracker();
		$this->failed_payment_detector = new Revenue_Recover_Failed_Payment_Detector( $this->retry_email );
		$this->failed_payment_detector->init();
		$this->revenue_tracker->init();

		if ( is_admin() ) {
			$this->admin_dashboard = new Revenue_Recover_Admin_Dashboard( $this->revenue_tracker );
			$this->admin_dashboard->init();
		}

		/**
		 * Fires after Revenue Recover has confirmed WooCommerce is active.
		 *
		 * @param Revenue_Recover_Plugin $plugin Main plugin instance.
		 */
		do_action( 'revenue_recover_loaded', $this );
	}

	/**
	 * Gets the revenue tracker.
	 *
	 * @return Revenue_Recover_Revenue_Tracker|null
	 */
	public function get_revenue_tracker() {
		return $this->revenue_tracker;
	}
}
