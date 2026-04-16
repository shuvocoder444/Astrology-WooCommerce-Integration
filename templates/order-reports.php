<?php
/**
 * Order Reports Display Template
 *
 * @package Astrology_WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="astro-woo-reports-section">
	<h2 class="astro-reports-title">
		<?php esc_html_e( 'Your Astrology Reports', ASTRO_WOO_TEXT_DOMAIN ); ?>
	</h2>

	<?php
	$generated_date = $order->get_meta( '_astro_reports_generated' );
	if ( $generated_date ) :
		?>
		<p class="astro-reports-meta">
			<?php
			printf(
				/* translators: %s: date and time */
				esc_html__( 'Generated on: %s', ASTRO_WOO_TEXT_DOMAIN ),
				esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $generated_date ) ) )
			);
			?>
		</p>
	<?php endif; ?>

	<div class="astro-reports-container">
		<?php foreach ( $reports as $index => $report ) : ?>
			<div class="astro-report-card <?php echo esc_attr( $report['status'] ); ?>" data-report-index="<?php echo esc_attr( $index ); ?>">
				
				<div class="astro-report-header">
					<h3 class="astro-report-title">
						<?php
						$form_type_labels = array(
							'single_person' => __( '🌟 Birth Chart Report', ASTRO_WOO_TEXT_DOMAIN ),
							'two_person'    => __( '💕 Love Compatibility Report', ASTRO_WOO_TEXT_DOMAIN ),
							'numerology'    => __( '🔢 Numerology Report', ASTRO_WOO_TEXT_DOMAIN ),
							'tarot'         => __( '🃏 Tarot Reading', ASTRO_WOO_TEXT_DOMAIN ),
							'zodiac'        => __( '♈ Zodiac Horoscope', ASTRO_WOO_TEXT_DOMAIN ),
						);

						echo isset( $form_type_labels[ $report['form_type'] ] ) 
							? esc_html( $form_type_labels[ $report['form_type'] ] )
							: esc_html( ucfirst( $report['form_type'] ) );
						?>
					</h3>
					
					<span class="astro-report-status astro-status-<?php echo esc_attr( $report['status'] ); ?>">
						<?php echo $report['status'] === 'success' ? '✓' : '✗'; ?>
					</span>
				</div>

				<?php if ( $report['status'] === 'success' && ! is_wp_error( $report['result'] ) ) : ?>
					
					<!-- Display Input Data -->
					<div class="astro-report-input-summary">
						<h4><?php esc_html_e( 'Your Details:', ASTRO_WOO_TEXT_DOMAIN ); ?></h4>
						<?php 
						// Use the order_handler variable if available
						if ( isset( $order_handler ) ) {
							echo wp_kses_post( $order_handler->format_input_summary( $report['form_type'], $report['form_data'] ) );
						} else {
							// Fallback display
							echo '<pre>' . esc_html( print_r( $report['form_data'], true ) ) . '</pre>';
						}
						?>
					</div>

					<!-- Display Report Result -->
					<div class="astro-report-result">
						<h4><?php esc_html_e( 'Report:', ASTRO_WOO_TEXT_DOMAIN ); ?></h4>
						<?php 
						if ( isset( $order_handler ) ) {
							echo wp_kses_post( $order_handler->format_report_display( $report['result'], $report['form_type'] ) );
						} else {
							// Fallback display
							echo '<div class="astro-raw-result">';
							echo '<pre>' . esc_html( print_r( $report['result'], true ) ) . '</pre>';
							echo '</div>';
						}
						?>
					</div>

					<!-- Download/Print/Email Options -->
					<div class="astro-report-actions">
						<button type="button" class="button astro-print-report" data-report-index="<?php echo esc_attr( $index ); ?>">
							<?php esc_html_e( '🖨️ Print Report', ASTRO_WOO_TEXT_DOMAIN ); ?>
						</button>
						<button type="button" class="button button-primary astro-email-report" 
							data-order-id="<?php echo esc_attr( $order->get_id() ); ?>" 
							data-report-index="<?php echo esc_attr( $index ); ?>">
							<?php esc_html_e( '📧 Email Report', ASTRO_WOO_TEXT_DOMAIN ); ?>
						</button>
					</div>
					<div class="astro-email-status" id="email-status-<?php echo esc_attr( $index ); ?>" style="margin-top: 10px; display: none;"></div>

				<?php elseif ( is_wp_error( $report['result'] ) ) : ?>
					
					<div class="astro-report-error">
						<p>
							<strong><?php esc_html_e( 'Error:', ASTRO_WOO_TEXT_DOMAIN ); ?></strong>
							<?php echo esc_html( $report['result']->get_error_message() ); ?>
						</p>
						<p>
							<?php esc_html_e( 'Please contact support if this issue persists.', ASTRO_WOO_TEXT_DOMAIN ); ?>
						</p>
					</div>

				<?php else : ?>
					
					<div class="astro-report-error">
						<p><?php esc_html_e( 'Report generation failed. Please contact support.', ASTRO_WOO_TEXT_DOMAIN ); ?></p>
						<?php if ( isset( $report['result'] ) ) : ?>
							<details>
								<summary><?php esc_html_e( 'Technical Details', ASTRO_WOO_TEXT_DOMAIN ); ?></summary>
								<pre><?php echo esc_html( print_r( $report['result'], true ) ); ?></pre>
							</details>
						<?php endif; ?>
					</div>

				<?php endif; ?>

			</div>
		<?php endforeach; ?>
	</div>

	<style>
		.astro-woo-reports-section {
			margin: 30px 0;
			padding: 20px;
			background: #f9f9f9;
			border-radius: 8px;
		}
		.astro-reports-title {
			font-size: 24px;
			margin-bottom: 10px;
			color: #333;
		}
		.astro-reports-meta {
			color: #666;
			font-size: 14px;
			margin-bottom: 20px;
		}
		.astro-reports-container {
			display: flex;
			flex-direction: column;
			gap: 20px;
		}
		.astro-report-card {
			background: white;
			border: 2px solid #ddd;
			border-radius: 8px;
			padding: 20px;
			box-shadow: 0 2px 4px rgba(0,0,0,0.1);
		}
		.astro-report-card.success {
			border-color: #4caf50;
		}
		.astro-report-card.error {
			border-color: #f44336;
		}
		.astro-report-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 15px;
			padding-bottom: 15px;
			border-bottom: 2px solid #eee;
		}
		.astro-report-title {
			margin: 0;
			font-size: 20px;
			color: #333;
		}
		.astro-report-status {
			width: 30px;
			height: 30px;
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			font-weight: bold;
			color: white;
		}
		.astro-status-success {
			background: #4caf50;
		}
		.astro-status-error {
			background: #f44336;
		}
		.astro-report-input-summary {
			background: #f5f5f5;
			padding: 15px;
			border-radius: 5px;
			margin-bottom: 15px;
		}
		.astro-report-input-summary h4 {
			margin-top: 0;
			color: #555;
		}
		.astro-report-input-summary p {
			margin: 5px 0;
		}
		.astro-report-result {
			padding: 15px;
			background: #fff;
			border: 1px solid #e0e0e0;
			border-radius: 5px;
			margin-bottom: 15px;
			max-height: 500px;
			overflow-y: auto;
		}
		.astro-report-result h4 {
			margin-top: 0;
			color: #333;
		}
		.astro-report-error {
			padding: 15px;
			background: #fff3cd;
			border: 1px solid #ffc107;
			border-radius: 5px;
			color: #856404;
		}
		.astro-report-actions {
			display: flex;
			gap: 10px;
			margin-top: 15px;
		}
		.astro-print-report {
			cursor: pointer;
		}
		.astro-email-report {
			cursor: pointer;
			background: #0073aa;
			color: white;
		}
		.astro-email-report:hover {
			background: #005a87;
		}
		.astro-email-status {
			padding: 10px;
			border-radius: 4px;
			font-size: 14px;
		}
		.astro-email-status.success {
			background: #d4edda;
			color: #155724;
			border: 1px solid #c3e6cb;
		}
		.astro-email-status.error {
			background: #f8d7da;
			color: #721c24;
			border: 1px solid #f5c6cb;
		}
		.astro-email-status.loading {
			background: #d1ecf1;
			color: #0c5460;
			border: 1px solid #bee5eb;
		}
		.report-section {
			margin: 10px 0;
		}
		.report-section strong {
			color: #555;
		}
		.astro-raw-result pre {
			background: #f5f5f5;
			padding: 10px;
			border-radius: 4px;
			overflow-x: auto;
			white-space: pre-wrap;
			word-wrap: break-word;
		}
		.input-summary p {
			margin: 8px 0;
			line-height: 1.6;
		}
		details {
			margin-top: 10px;
		}
		details summary {
			cursor: pointer;
			font-weight: bold;
			padding: 5px;
			background: #f0f0f0;
			border-radius: 3px;
		}
		details pre {
			margin-top: 10px;
			background: #f5f5f5;
			padding: 10px;
			border-radius: 4px;
			overflow-x: auto;
		}
	</style>

	<script>
	jQuery(document).ready(function($) {
		// Print Report Handler
		$('.astro-print-report').on('click', function() {
			var reportCard = $(this).closest('.astro-report-card');
			var printWindow = window.open('', '', 'height=600,width=800');
			
			printWindow.document.write('<html><head><title>Astrology Report</title>');
			printWindow.document.write('<style>body{font-family:Arial,sans-serif;padding:20px;} .astro-report-status{display:inline-block;padding:5px 10px;border-radius:3px;} .astro-status-success{background:#4caf50;color:white;} .astro-report-actions{display:none;}</style>');
			printWindow.document.write('</head><body>');
			printWindow.document.write(reportCard.html());
			printWindow.document.write('</body></html>');
			
			printWindow.document.close();
			printWindow.focus();
			
			setTimeout(function() {
				printWindow.print();
				printWindow.close();
			}, 250);
		});

		// Email Report Handler
		$('.astro-email-report').on('click', function() {
			var $button = $(this);
			var orderId = $button.data('order-id');
			var reportIndex = $button.data('report-index');
			var $status = $('#email-status-' + reportIndex);
			
			// Disable button and show loading
			$button.prop('disabled', true).text('📧 Sending...');
			$status.removeClass('success error').addClass('loading')
				.html('⏳ Sending email...').show();
			
			// AJAX call to send email
			$.ajax({
				url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
				type: 'POST',
				data: {
					action: 'astro_send_report_email',
					order_id: orderId,
					report_index: reportIndex,
					nonce: '<?php echo wp_create_nonce( 'astro_email_report' ); ?>'
				},
				success: function(response) {
					if (response.success) {
						$status.removeClass('loading error').addClass('success')
							.html('✅ ' + response.data.message);
						$button.text('📧 Email Sent!');
						setTimeout(function() {
							$button.prop('disabled', false).text('📧 Email Report');
							$status.fadeOut();
						}, 3000);
					} else {
						$status.removeClass('loading success').addClass('error')
							.html('❌ ' + response.data.message);
						$button.prop('disabled', false).text('📧 Email Report');
					}
				},
				error: function() {
					$status.removeClass('loading success').addClass('error')
						.html('❌ Failed to send email. Please try again.');
					$button.prop('disabled', false).text('📧 Email Report');
				}
			});
		});
	});
	</script>
</div>
