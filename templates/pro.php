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
			<iframe data-tally-src="https://tally.so/embed/EkNdyN?alignLeft=1&hideTitle=1&transparentBackground=1&dynamicHeight=1" loading="lazy" width="100%" height="1053" frameborder="0" marginheight="0" marginwidth="0" title="<?php esc_attr_e( 'Revenue Recovery PRO features waitlist', 'revenue-recover' ); ?>"></iframe>
			<script>var d=document,w="https://tally.so/widgets/embed.js",v=function(){"undefined"!=typeof Tally?Tally.loadEmbeds():d.querySelectorAll("iframe[data-tally-src]:not([src])").forEach((function(e){e.src=e.dataset.tallySrc}))};if("undefined"!=typeof Tally)v();else if(d.querySelector('script[src="'+w+'"]')==null){var s=d.createElement("script");s.src=w,s.onload=v,s.onerror=v,d.body.appendChild(s);}</script>
		</div>
	</div>
</div>
