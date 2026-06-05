<?php
/**
 * Pro features template.
 *
 * @package Revenue_Recover
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap revenue-recover-page revenue-recover-pro-page">
	<div class="revenue-recover-pro-hero">
		<div class="revenue-recover-pro-kicker">
			<?php esc_html_e( '✨ Planned Pro Features', 'revenue-recover' ); ?>
		</div>

		<h1><?php esc_html_e( 'Build your failed-payment recovery machine.', 'revenue-recover' ); ?></h1>

		<p>
			<?php esc_html_e( 'Revenue Recover already helps you bring customers back with retry payment links. Pro is being shaped for store owners who want stronger automation, faster recovery channels, and clearer revenue intelligence.', 'revenue-recover' ); ?>
		</p>
	</div>

	<div class="revenue-recover-pro-grid">
		<?php foreach ( $features as $revenue_recover_feature ) : ?>
			<div class="revenue-recover-pro-card">
				<div class="revenue-recover-pro-card-icon">
					<?php echo esc_html( $revenue_recover_feature['icon'] ); ?>
				</div>

				<h2><?php echo esc_html( $revenue_recover_feature['title'] ); ?></h2>
				<p><?php echo esc_html( $revenue_recover_feature['description'] ); ?></p>
			</div>
		<?php endforeach; ?>
	</div>

	<div class="revenue-recover-pro-cta">
		<div>
			<div class="revenue-recover-pro-kicker">
				<?php esc_html_e( '🚀 Early Access', 'revenue-recover' ); ?>
			</div>

			<h2><?php esc_html_e( 'Want to recover more payments automatically?', 'revenue-recover' ); ?></h2>

			<p>
				<?php esc_html_e( 'Join the early access list and help shape Revenue Recover Pro. Early subscribers will receive a special launch discount when Pro becomes available.', 'revenue-recover' ); ?>
			</p>
		</div>

		<div class="revenue-recover-tally-placeholder">
			<h3><?php esc_html_e( 'Help shape the Pro version', 'revenue-recover' ); ?></h3>

			<p>
				<?php esc_html_e( 'Tell us which recovery features would help your store save more failed payments. Early waitlist members get a special launch discount.', 'revenue-recover' ); ?>
			</p>

			<a
				class="revenue-recover-tally-button"
				href="<?php echo esc_url( 'https://tally.so/r/EkNdyN' ); ?>"
				target="_blank"
				rel="noopener noreferrer"
			>
				<?php esc_html_e( 'Join the Pro Waitlist', 'revenue-recover' ); ?>

				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
					<path d="M7 17L17 7"></path>
					<path d="M9 7h8v8"></path>
				</svg>
			</a>
		</div>
	</div>
</div>
