<?php
/**
 * Dashboard template.
 *
 * @package Revenue_Recover
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap revenue-recover-page revenue-recover-dashboard">
	<div class="revenue-recover-header">
		<div>
			<h1><?php esc_html_e( '💸 Turn failed payments into revenue.', 'revenue-recover' ); ?></h1>
			<p><?php esc_html_e( 'Here is how your failed payment recovery is performing.', 'revenue-recover' ); ?></p>
		</div>

		<a class="revenue-recover-header-action" href="<?php echo esc_url( admin_url( 'admin.php?page=revenue-recover-pro' ) ); ?>">
			<?php esc_html_e( 'Explore Pro Features', 'revenue-recover' ); ?>
		</a>
	</div>

	<div class="revenue-recover-metrics">
		<div class="revenue-recover-metric-card">
			<div class="revenue-recover-metric-icon revenue-recover-metric-icon-danger">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 5h2l2.2 9.2a2 2 0 0 0 2 1.5h7.8a2 2 0 0 0 2-1.6L21 8H7"></path>
                    <circle cx="10" cy="20" r="1"></circle>
                    <circle cx="18" cy="20" r="1"></circle>
                </svg>
            </div>
			<div>
				<h2><?php esc_html_e( 'Failed Payments', 'revenue-recover' ); ?></h2>
				<span class="revenue-recover-metric-label"><?php esc_html_e( 'Total', 'revenue-recover' ); ?></span>
				<strong><?php echo esc_html( number_format_i18n( $failed_count ) ); ?></strong>
			</div>
		</div>

		<div class="revenue-recover-metric-card">
			<div class="revenue-recover-metric-icon revenue-recover-metric-icon-success">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M15 9.5c0-1.1-1.3-2-3-2s-3 .9-3 2 1.3 2 3 2 3 .9 3 2-1.3 2-3 2-3-.9-3-2"></path>
                    <path d="M12 7v10"></path>
                </svg>
            </div>
			<div>
				<h2><?php esc_html_e( 'Recovered Revenue', 'revenue-recover' ); ?></h2>
				<span class="revenue-recover-metric-label"><?php esc_html_e( 'Total', 'revenue-recover' ); ?></span>
				<strong><?php echo wp_kses_post( wc_price( $recovered_revenue ) ); ?></strong>
			</div>
		</div>

		<div class="revenue-recover-metric-card">
			<div class="revenue-recover-metric-icon revenue-recover-metric-icon-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 14a8 8 0 0 1 16 0"></path>
                    <path d="M12 14l4-4"></path>
                    <path d="M8 18h8"></path>
                </svg>
            </div>
			<div>
				<h2><?php esc_html_e( 'Recovery Rate', 'revenue-recover' ); ?></h2>
				<span class="revenue-recover-metric-label"><?php esc_html_e( 'Recovered / Failed', 'revenue-recover' ); ?></span>
				<strong>
					<?php
					printf(
						/* translators: %s: Recovery rate percentage value. */
						esc_html__( '%s%%', 'revenue-recover' ),
						esc_html( number_format_i18n( $recovery_rate, 2 ) )
					);
					?>
				</strong>
			</div>
		</div>

		<div class="revenue-recover-metric-card">
			<div class="revenue-recover-metric-icon revenue-recover-metric-icon-info">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 6L9 17l-5-5"></path>
                </svg>
            </div>
			<div>
				<h2><?php esc_html_e( 'Recovered Orders', 'revenue-recover' ); ?></h2>
				<span class="revenue-recover-metric-label"><?php esc_html_e( 'Recent tracked orders', 'revenue-recover' ); ?></span>
				<strong><?php echo esc_html( number_format_i18n( $recovered_count ) ); ?></strong>
			</div>
		</div>
	</div>

	<div class="revenue-recover-panel">
		<div class="revenue-recover-panel-header">
			<div>
				<h2><?php esc_html_e( 'Recent Failed Orders', 'revenue-recover' ); ?></h2>
				<p><?php esc_html_e( 'Orders with failed payments that Revenue Recover is helping you recover.', 'revenue-recover' ); ?></p>
			</div>
		</div>

		<div class="revenue-recover-table-wrap">
			<table class="revenue-recover-table">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Order', 'revenue-recover' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Customer', 'revenue-recover' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Amount', 'revenue-recover' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Recovery Status', 'revenue-recover' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Date', 'revenue-recover' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $recent_orders ) ) : ?>
						<tr>
							<td colspan="5">
								<div class="revenue-recover-empty">
									<strong><?php esc_html_e( 'No failed payments yet.', 'revenue-recover' ); ?></strong>
									<p><?php esc_html_e( 'When a payment fails, it will appear here with its recovery status.', 'revenue-recover' ); ?></p>
								</div>
							</td>
						</tr>
					<?php else : ?>
						<?php foreach ( $recent_orders as $order ) : ?>
							<?php
							$status       = sanitize_key( $order->get_meta( Revenue_Recover_Failed_Payment_Detector::META_RECOVERY_STATUS, true ) );
							$revenue_recover_is_recovered = Revenue_Recover_Failed_Payment_Detector::RECOVERY_STATUS_RECOVERED === $status;

							?>
							<tr>
								<td><?php $this->render_order_link( $order ); ?></td>
								<td><?php echo esc_html( $this->get_customer_name( $order ) ); ?></td>
								<td><?php echo wp_kses_post( $this->get_order_amount( $order ) ); ?></td>
								<td>
									<span class="revenue-recover-status <?php echo esc_attr( $revenue_recover_is_recovered ? 'is-recovered' : 'is-pending' ); ?>">
										<?php if ( $revenue_recover_is_recovered ) : ?>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                <path d="M20 6L9 17l-5-5"></path>
                                            </svg>

                                            <?php esc_html_e( 'Recovered', 'revenue-recover' ); ?>

                                        <?php else : ?>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                <circle cx="12" cy="12" r="9"></circle>
                                                <path d="M12 7v5"></path>
                                                <path d="M12 16h.01"></path>
                                            </svg>

                                            <?php esc_html_e( 'Pending', 'revenue-recover' ); ?>

                                        <?php endif; ?>
									</span>
								</td>
								<td><?php echo esc_html( $this->get_failed_date( $order ) ); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>

	<div class="revenue-recover-pro-banner">
		<div class="revenue-recover-pro-illustration">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 3l2.8 5.7L21 9.6l-4.5 4.4 1.1 6.2L12 17.3 6.4 20.2 7.5 14 3 9.6l6.2-.9L12 3z"></path>
            </svg>
        </div>

		<div>
			<h2><?php esc_html_e( 'Recover even more revenue with Pro features', 'revenue-recover' ); ?></h2>
			<p><?php esc_html_e( 'Unlock retry sequences, SMS and WhatsApp recovery, coupon incentives, advanced analytics, subscription recovery, and more.', 'revenue-recover' ); ?></p>
		</div>

		<a href="<?php echo esc_url( admin_url( 'admin.php?page=revenue-recover-pro' ) ); ?>">
			<?php esc_html_e( 'Explore Pro Features', 'revenue-recover' ); ?>
		</a>
	</div>
</div>
