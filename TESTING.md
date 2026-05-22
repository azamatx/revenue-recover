## Known Stripe Compatibility Note

Revenue Recover’s MVP retry-payment flow is validated with the standard WooCommerce Stripe card payment flow.

Stripe Adaptive Pricing can interfere with failed-order recovery testing:

- declined payments may remain in Pending payment instead of moving to Failed
- manually failed orders may not complete successfully through the retry payment link
- Stripe may fail before creating a fresh payment attempt

For MVP testing and early usage, disable Stripe Adaptive Pricing.

Recommended Stripe settings for testing:
- Standard card payments: enabled
- Saved payment methods: allowed
- Adaptive Pricing: disabled
- Express/Link/Apple Pay/Google Pay: optional, but test carefully
