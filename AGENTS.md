# AGENTS.md

# Revenue Recover

Revenue Recover is a WooCommerce plugin focused on recovering failed payments.

The plugin is NOT:
- an abandoned cart plugin
- a checkout optimization suite
- a frontend validation tool
- a JavaScript error monitoring tool
- a gateway diagnostics platform
- an uptime monitor

The plugin explicitly focuses on REAL payment failures happening during payment processing.

Examples:
- Stripe API temporary failures
- PayPal payment interruptions
- gateway timeouts
- failed payment statuses
- customer payment interruptions after checkout submission
- authentication/payment processing failures

The plugin helps merchants recover lost revenue by automatically bringing customers back to complete payment.

---

# Product Philosophy

This plugin is intentionally a very small MVP.

The goal is:
- launch extremely fast
- validate demand
- keep architecture simple
- avoid overengineering
- avoid premature abstractions

DO NOT overbuild features.

DO NOT introduce unnecessary complexity.

DO NOT build future SaaS architecture now.

DO NOT build generic framework-style systems.

DO NOT build enterprise-level abstractions.

This plugin should feel:
- lean
- simple
- focused
- easy to maintain

The plugin is pre-release.

Backward compatibility is NOT required.

Breaking changes are allowed.

Database schema changes are allowed.

Refactors are allowed.

Prefer simplicity over compatibility.

---

# MVP Scope

ONLY build these free MVP features:

1. failed payment detection
2. 1 retry email
3. retry payment link
4. recovered revenue counter
5. simple dashboard

Anything outside this scope should be avoided unless absolutely necessary.

---

# Main User Flow

1. Customer attempts payment
2. Payment fails
3. Plugin detects failed payment
4. Plugin sends retry email automatically
5. Customer clicks retry payment link
6. Customer successfully completes payment
7. Plugin tracks recovered revenue

This is the core product.

Everything else is secondary.

---

# Important Product Boundaries

Revenue Recover only focuses on payment failures AFTER checkout submission.

DO NOT build features for:
- abandoned carts
- incomplete checkout forms
- frontend validation errors
- JavaScript UI problems
- coupon optimization
- lead capture
- popup recovery systems

The plugin should only react to:
- failed payment statuses
- failed gateway processing
- failed order payment attempts

This distinction is extremely important.

---

# Supported Payment Failures

The MVP should primarily rely on WooCommerce failed order/payment states.

Prefer native WooCommerce flows and hooks.

Potential hooks:
- woocommerce_order_status_failed
- woocommerce_payment_complete
- woocommerce_checkout_order_processed

Do not build gateway-specific integrations unless necessary for MVP stability.

Keep gateway handling generic initially.

---

# Retry Payment Flow

The retry system should use native WooCommerce payment retry mechanisms.

Use:
- WooCommerce order-pay endpoint
- native payment retry flow

DO NOT create custom checkout systems.

DO NOT duplicate WooCommerce payment handling.

The plugin should remain lightweight.

---

# Retry Email Requirements

MVP supports only:
- 1 retry email

No sequences.

No advanced automation.

No marketing campaigns.

No abandoned cart flows.

The retry email should:
- be simple
- be short
- contain retry payment link
- clearly explain payment was not completed

Primary CTA:
Complete Your Payment

---

# Recovered Revenue Tracking

The plugin should track:
- failed order totals
- successfully recovered orders
- recovered revenue amount

The purpose is to show ROI clearly.

Avoid complex analytics.

Simple counters are enough for MVP.

---

# Dashboard Requirements

Dashboard must remain minimal.

Show only:
- failed payments
- recovered revenue
- recovery rate
- recent failed orders

No charts required initially.

No advanced filtering required.

No logs viewer.

No diagnostics panels.

No gateway monitoring widgets.

The dashboard should communicate:
- money recovered
- revenue impact
- recovery success

Not technical information.

---

# Architecture Guidelines

Prefer:
- procedural simplicity where reasonable
- small focused classes
- minimal service containers
- minimal abstractions
- direct WooCommerce integrations

Avoid:
- enterprise patterns
- unnecessary interfaces
- repositories everywhere
- event buses
- CQRS
- excessive dependency injection
- premature modularization

Simple code is preferred over theoretically scalable architecture.

---

# WordPress Standards

Follow:
- WordPress coding standards
- WooCommerce conventions
- proper escaping/sanitization
- nonce verification
- capability checks

Code should remain:
- readable
- clean
- minimal
- extensible enough for future growth

But extensibility should not slow down MVP development.

---

# UI Philosophy

Admin UI should feel:
- clean
- modern
- revenue-focused
- non-technical

Avoid:
- developer-heavy terminology
- raw logs
- technical jargon

Prefer:
- "Recovered Revenue"
instead of:
- "Successful Retry Transactions"

The plugin should emotionally communicate:
- saved sales
- recovered money
- reduced revenue loss

---

# Future Features (NOT MVP)

These are intentionally excluded from MVP:

- logs viewer
- Slack notifications
- Telegram notifications
- SMS recovery
- WhatsApp recovery
- retry sequences
- coupon incentives
- AI recommendations
- advanced analytics
- gateway diagnostics
- webhook testing
- uptime monitoring
- subscription recovery
- alternative gateway suggestions

Do not implement these unless explicitly requested later.

---

# Decision-Making Rules

When unsure between:
- simpler implementation
- more scalable implementation

Prefer the simpler implementation.

When unsure between:
- faster shipping
- more architecture

Prefer faster shipping.

When unsure between:
- feature completeness
- MVP scope discipline

Prefer MVP scope discipline.

The success metric is:
Can this plugin quickly prove merchants want failed payment recovery?
