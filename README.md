# Revenue Recover

Recover failed WooCommerce payments automatically.

Revenue Recover detects failed WooCommerce payments, sends customers a secure retry payment link, and helps merchants recover lost revenue before customers disappear.

---

# What Revenue Recover Does

Revenue Recover focuses specifically on failed payment recovery.

When a payment fails during WooCommerce checkout:

1. Revenue Recover detects the failed order
2. The customer receives a retry payment email
3. The customer returns through a secure payment link
4. The order gets recovered
5. Recovered revenue is tracked in the dashboard

---

# This Plugin Is NOT

Revenue Recover is NOT:

- an abandoned cart plugin
- a frontend checkout validation plugin
- a JavaScript error monitor
- an uptime monitoring tool
- a gateway diagnostics suite
- a WooCommerce analytics platform

The plugin specifically focuses on payment failures during payment processing.

---

# MVP Features

## Failed Payment Detection

Detect WooCommerce failed orders automatically.

## Retry Payment Email

Automatically send customers a secure retry payment link.

## Secure Retry Payment Link

Use WooCommerce native order payment flow.

## Recovered Revenue Tracking

Track:
- failed payments
- recovered orders
- recovered revenue
- recovery rate

## Simple Revenue Dashboard

Minimal dashboard focused on revenue recovery.

---

# Dashboard Metrics

Revenue Recover currently tracks:

- Failed Payments
- Recovered Revenue
- Recovery Rate
- Recent Failed Orders

---

# Requirements

- WordPress
- WooCommerce
- PHP 8+
- A supported WooCommerce payment gateway

---

# Supported Gateways

Revenue Recover is designed to work generically with WooCommerce payment gateways.

The MVP has been tested primarily with:
- WooCommerce Stripe Gateway

Additional gateway testing is planned later.

---

# Stripe Compatibility Notes

Revenue Recover MVP is validated with the standard WooCommerce Stripe card payment flow.

Some Stripe features may interfere with failed-payment retry behavior.

For best results during testing:

Recommended:
- Standard card payments
- Saved payment methods

Potentially problematic:
- Adaptive Pricing
- certain express checkout flows
- some Stripe-hosted checkout/session flows

See `TESTING.md` for additional notes.

---

# Installation

1. Upload the plugin ZIP
2. Activate the plugin
3. Ensure WooCommerce is active
4. Complete a test failed payment
5. Verify retry email delivery

---

# Recommended Testing Flow

1. Create a WooCommerce order
2. Use a declined Stripe test card
3. Confirm order becomes Failed
4. Confirm retry email is sent
5. Open retry payment link
6. Complete payment successfully
7. Verify recovered revenue updates

---

# Stripe Test Cards

Declined card example:

```text
4000 0000 0000 9987
```

Successful payment card:

```text
4242 4242 4242 4242
```

Use any future expiry and any CVC.

---

# Current MVP Limitations

This is an intentionally small MVP release.

Currently excluded:

- abandoned cart recovery
- retry sequences
- SMS recovery
- WhatsApp recovery
- Slack notifications
- analytics charts
- logs viewer
- webhook diagnostics
- uptime monitoring
- subscription recovery
- coupon incentives

---

# Plugin Philosophy

Revenue Recover focuses on:

- simplicity
- fast setup
- revenue visibility
- minimal configuration
- merchant-friendly UX

The plugin intentionally avoids unnecessary complexity.

---

# Development Status

MVP / pre-release.

Breaking changes may happen before stable release.

---

# Roadmap Ideas

Possible future features:

- multi-email retry sequences
- SMS recovery
- WhatsApp recovery
- Slack notifications
- subscription renewal recovery
- recovery analytics
- gateway compatibility assistant
- alternative payment suggestions

---

# License

GPLv2 or later

---

# Contributing

Early feedback and real-world WooCommerce gateway testing are highly valuable during MVP validation.

---

# Author

Revenue Recover
