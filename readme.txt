=== Revenue Recover ===
Contributors: azamat88
Tags: woocommerce, failed payments, payment recovery, revenue recovery, stripe
Requires at least: 6.7
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Recover lost WooCommerce revenue from failed payments with automatic retry emails and secure payment retry links.

== Description ==

Revenue Recover helps WooCommerce stores recover revenue that would otherwise be lost because of failed payments.

When a payment fails during checkout, Revenue Recover detects the failed order, sends the customer a retry email, and gives them a secure payment retry link so they can complete the purchase later.

Perfect for:
- Stripe payment failures
- Temporary card issues
- Authentication interruptions
- Payment gateway hiccups
- Customers who abandon checkout after a failed payment

Instead of silently losing revenue, Revenue Recover helps bring those customers back.

= Free Features =

- Detect failed WooCommerce payments
- Automatically send 1 retry email
- Secure retry payment links
- Track recovered revenue
- Recovery dashboard inside WooCommerce
- Recovery rate tracking
- Recent failed orders overview
- WooCommerce HPOS compatible

= Planned Pro Features =

- Automated retry sequences
- SMS and WhatsApp recovery
- Coupon-based recovery
- Advanced recovery analytics
- Subscription renewal recovery
- Slack and Telegram alerts
- Alternative payment suggestions

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/revenue-recover` directory, or install the plugin through the WordPress plugins screen.
2. Activate the plugin through the "Plugins" screen in WordPress.
3. Make sure WooCommerce is installed and active.
4. Complete a failed payment test to see Revenue Recover in action.

== Frequently Asked Questions ==

= Which payment gateways are supported? =

Revenue Recover works with WooCommerce failed payment orders and is gateway-agnostic. Stripe has been tested during MVP development.

= Does this plugin send abandoned cart emails? =

No.

Revenue Recover focuses specifically on failed payments during checkout, not abandoned carts.

= Does the plugin recover payments automatically? =

The free version sends a retry email with a secure payment retry link. Customers complete the payment themselves.

= Is WooCommerce required? =

Yes. WooCommerce must be installed and active.

== Screenshots ==

1. Revenue recovery dashboard
2. Failed payment recovery tracking
3. Retry payment email
4. Planned Revenue Recover Pro features

== Changelog ==

= 1.0.0 =
* Initial public release
* Failed payment detection
* Retry payment emails
* Secure retry links
* Revenue recovery dashboard
* Recovery tracking

== Upgrade Notice ==

= 1.0.0 =
Initial release of Revenue Recover.
