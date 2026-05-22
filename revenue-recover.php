<?php
/**
 * Plugin Name: Revenue Recover
 * Plugin URI: https://wordpress.org/plugins/revenue-recover/
 * Description: Recover lost WooCommerce revenue from failed payments with automatic retry emails and secure payment retry links.
 * Version: 1.0.0
 * Requires at least: 6.7
 * Requires PHP: 7.4
 * Author: Azamat Khodjakov
 * Author URI: https://profiles.wordpress.org/azamat88/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: revenue-recover
 * Domain Path: /languages
 * Requires Plugins: woocommerce
 *
 * @package Revenue_Recover
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'REVENUE_RECOVER_VERSION' ) ) {
	define( 'REVENUE_RECOVER_VERSION', '1.0.0' );
}

if ( ! defined( 'REVENUE_RECOVER_FILE' ) ) {
	define( 'REVENUE_RECOVER_FILE', __FILE__ );
}

if ( ! defined( 'REVENUE_RECOVER_PATH' ) ) {
	define( 'REVENUE_RECOVER_PATH', plugin_dir_path( REVENUE_RECOVER_FILE ) );
}

if ( ! defined( 'REVENUE_RECOVER_URL' ) ) {
	define( 'REVENUE_RECOVER_URL', plugin_dir_url( REVENUE_RECOVER_FILE ) );
}

require_once REVENUE_RECOVER_PATH . 'includes/class-revenue-recover-failed-payment-detector.php';
require_once REVENUE_RECOVER_PATH . 'includes/class-revenue-recover-revenue-tracker.php';
require_once REVENUE_RECOVER_PATH . 'includes/class-revenue-recover-retry-email.php';
require_once REVENUE_RECOVER_PATH . 'includes/class-revenue-recover-admin-dashboard.php';
require_once REVENUE_RECOVER_PATH . 'includes/class-revenue-recover-plugin.php';

/**
 * Returns the main Revenue Recover plugin instance.
 *
 * @return Revenue_Recover_Plugin
 */
function revenue_recover() {
	return Revenue_Recover_Plugin::instance();
}

add_action( 'plugins_loaded', 'revenue_recover', 20 );
