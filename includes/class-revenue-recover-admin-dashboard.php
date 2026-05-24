<?php
/**
 * Admin dashboard.
 *
 * @package Revenue_Recover
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders the minimal Revenue Recover admin dashboard.
 */
class Revenue_Recover_Admin_Dashboard {

	/**
	 * Revenue tracker.
	 *
	 * @var Revenue_Recover_Revenue_Tracker
	 */
	protected $revenue_tracker;

	/**
	 * Admin page hook suffixes.
	 *
	 * @var string[]
	 */
	protected $page_hooks = array();

	/**
	 * Sets up dependencies.
	 *
	 * @param Revenue_Recover_Revenue_Tracker $revenue_tracker Revenue tracker.
	 */
	public function __construct( Revenue_Recover_Revenue_Tracker $revenue_tracker ) {
		$this->revenue_tracker = $revenue_tracker;
	}

	/**
	 * Registers admin hooks.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'current_screen', array( $this, 'suppress_admin_notices' ) );
		add_action( 'admin_head', array( $this, 'suppress_admin_notices' ), 999 );
	}

	public function enqueue_assets( $hook_suffix ) {
		if ( ! in_array( $hook_suffix, array_filter( $this->page_hooks ), true ) ) {
			return;
		}

		wp_enqueue_style(
			'revenue-recover-admin',
			REVENUE_RECOVER_URL . 'assets/css/admin.css',
			array(),
			REVENUE_RECOVER_VERSION
		);

		$revenue_recover_page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( 'revenue-recover-pro' === $revenue_recover_page ) {
			wp_enqueue_script(
				'revenue-recover-tally',
				'https://tally.so/widgets/embed.js',
				array(),
				REVENUE_RECOVER_VERSION,
				array(
					'in_footer' => true,
					'strategy'  => 'defer',
				)
			);

			wp_add_inline_script(
				'revenue-recover-tally',
				'if ( typeof Tally !== "undefined" ) { Tally.loadEmbeds(); }',
				'after'
			);
		}
	}

	/**
	 * Adds the dashboard menu page.
	 *
	 * @return void
	 */
	public function add_menu_page() {
		$this->page_hooks[] = add_menu_page(
			__( 'Revenue Recover', 'revenue-recover' ),
			__( 'Revenue Recover', 'revenue-recover' ),
			'manage_woocommerce',
			'revenue-recover',
			array( $this, 'render' ),
			'dashicons-money-alt',
			56
		);

		$this->page_hooks[] = add_submenu_page(
			'revenue-recover',
			__( 'Dashboard', 'revenue-recover' ),
			__( 'Dashboard', 'revenue-recover' ),
			'manage_woocommerce',
			'revenue-recover',
			array( $this, 'render' )
		);

		$this->page_hooks[] = add_submenu_page(
			'revenue-recover',
			__( 'Recover More Revenue', 'revenue-recover' ),
			__( 'Recover More Revenue', 'revenue-recover' ),
			'manage_woocommerce',
			'revenue-recover-pro',
			array( $this, 'render_pro_page' )
		);
	}

	/**
	 * Suppresses third-party admin notices on Revenue Recover pages.
	 *
	 * @param WP_Screen|null $screen Current screen.
	 * @return void
	 */
	public function suppress_admin_notices( $screen = null ) {
		if ( ! $this->is_revenue_recover_admin_page( $screen ) ) {
			return;
		}

		foreach ( $this->get_notice_hooks() as $hook_name ) {
			remove_all_actions( $hook_name );
		}
	}

	/**
	 * Checks whether the current admin page belongs to Revenue Recover.
	 *
	 * @param WP_Screen|null $screen Current screen.
	 * @return bool
	 */
	protected function is_revenue_recover_admin_page( $screen = null ) {
		if ( $screen instanceof WP_Screen && in_array( $screen->id, array_filter( $this->page_hooks ), true ) ) {
			return true;
		}

		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		return in_array( $page, array( 'revenue-recover', 'revenue-recover-pro' ), true );
	}

	/**
	 * Gets admin notice hooks to suppress on Revenue Recover pages.
	 *
	 * @return string[]
	 */
	protected function get_notice_hooks() {
		return array(
			'admin_notices',
			'all_admin_notices',
			'network_admin_notices',
			'user_admin_notices',
		);
	}

	/**
	 * Renders the dashboard.
	 *
	 * @return void
	 */
	public function render() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'You do not have permission to view this page.', 'revenue-recover' ) );
		}

		$failed_count      = $this->revenue_tracker->get_total_failed_payments_count();
		$recovered_revenue = $this->revenue_tracker->get_recovered_revenue();
		$recovery_rate     = $this->revenue_tracker->get_recovery_rate();
		$recent_orders     = $this->revenue_tracker->get_recent_failed_orders( 10 );
		$recovered_count   = 0;

		foreach ( $recent_orders as $order ) {
			$status = sanitize_key( $order->get_meta( Revenue_Recover_Failed_Payment_Detector::META_RECOVERY_STATUS, true ) );

			if ( Revenue_Recover_Failed_Payment_Detector::RECOVERY_STATUS_RECOVERED === $status ) {
				$recovered_count++;
			}
		}

		require REVENUE_RECOVER_PATH . 'templates/dashboard.php';
	}

	/**
	 * Renders the Pro demand-validation page.
	 *
	 * @return void
	 */
	public function render_pro_page() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'You do not have permission to view this page.', 'revenue-recover' ) );
		}

		$features = array(
			array(
				'icon'        => '🔁',
				'title'       => __( 'Automated Retry Sequences', 'revenue-recover' ),
				'description' => __( 'Follow up automatically after failed payments with smart retry emails that bring customers back before the sale is gone.', 'revenue-recover' ),
			),
			array(
				'icon'        => '📲',
				'title'       => __( 'SMS & WhatsApp Recovery', 'revenue-recover' ),
				'description' => __( 'Reach customers where they actually respond, with instant retry links delivered through higher-attention channels.', 'revenue-recover' ),
			),
			array(
				'icon'        => '🎁',
				'title'       => __( 'Coupon-Based Recovery', 'revenue-recover' ),
				'description' => __( 'Turn hesitation into action with limited-time incentives that help recover payments faster.', 'revenue-recover' ),
			),
			array(
				'icon'        => '📊',
				'title'       => __( 'Advanced Recovery Analytics', 'revenue-recover' ),
				'description' => __( 'See exactly how much revenue was at risk, what you recovered, and where your store is leaking money.', 'revenue-recover' ),
			),
			array(
				'icon'        => '🔄',
				'title'       => __( 'Subscription Payment Recovery', 'revenue-recover' ),
				'description' => __( 'Protect recurring revenue by recovering failed WooCommerce Subscriptions renewals before customers churn.', 'revenue-recover' ),
			),
			array(
				'icon'        => '🚨',
				'title'       => __( 'Team Notifications', 'revenue-recover' ),
				'description' => __( 'Alert your team instantly when important payments fail, so revenue problems never stay invisible.', 'revenue-recover' ),
			),
			array(
				'icon'        => '💳',
				'title'       => __( 'Alternative Payment Suggestions', 'revenue-recover' ),
				'description' => __( 'Guide customers toward another payment method when their first option fails.', 'revenue-recover' ),
			),
		);

		require REVENUE_RECOVER_PATH . 'templates/pro.php';
	}

	/**
	 * Renders the admin order link.
	 *
	 * @param WC_Order $order Order object.
	 * @return void
	 */
	protected function render_order_link( WC_Order $order ) {
		printf(
			'<a href="%1$s">#%2$s</a>',
			esc_url( $order->get_edit_order_url() ),
			esc_html( $order->get_order_number() )
		);
	}

	/**
	 * Gets a customer label for the order.
	 *
	 * @param WC_Order $order Order object.
	 * @return string
	 */
	protected function get_customer_name( WC_Order $order ) {
		$customer = trim( wp_strip_all_tags( $order->get_formatted_billing_full_name() ) );

		if ( '' !== $customer ) {
			return $customer;
		}

		$email = sanitize_email( $order->get_billing_email() );

		if ( is_email( $email ) ) {
			return $email;
		}

		return __( 'Guest', 'revenue-recover' );
	}

	/**
	 * Gets the failed amount formatted for display.
	 *
	 * @param WC_Order $order Order object.
	 * @return string
	 */
	protected function get_order_amount( WC_Order $order ) {
		$amount = max( 0, (float) $order->get_meta( Revenue_Recover_Failed_Payment_Detector::META_FAILED_AMOUNT, true ) );

		return wc_price(
			$amount,
			array(
				'currency' => $order->get_currency(),
			)
		);
	}

	/**
	 * Gets the recovery status label.
	 *
	 * @param WC_Order $order Order object.
	 * @return string
	 */
	protected function get_recovery_status_label( WC_Order $order ) {
		$status = sanitize_key( $order->get_meta( Revenue_Recover_Failed_Payment_Detector::META_RECOVERY_STATUS, true ) );

		if ( Revenue_Recover_Failed_Payment_Detector::RECOVERY_STATUS_RECOVERED === $status ) {
			return __( 'Recovered', 'revenue-recover' );
		}

		return __( 'Pending', 'revenue-recover' );
	}

	/**
	 * Gets the failed date.
	 *
	 * @param WC_Order $order Order object.
	 * @return string
	 */
	protected function get_failed_date( WC_Order $order ) {
		$timestamp = absint( $order->get_meta( Revenue_Recover_Failed_Payment_Detector::META_FAILED_AT, true ) );

		if ( ! $timestamp && $order->get_date_created() ) {
			$timestamp = $order->get_date_created()->getTimestamp();
		}

		if ( ! $timestamp ) {
			return '';
		}

		return wp_date( get_option( 'date_format' ), $timestamp );
	}
}
