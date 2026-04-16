<?php
/**
 * My Account - Astrology Reports Page Template
 *
 * @package Astrology_WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get order handler instance
$order_handler = new Astro_Woo_Order();
?>

<div class="woocommerce-MyAccount-astrology-reports">
	<h2><?php esc_html_e( '🌟 Your Astrology Reports', ASTRO_WOO_TEXT_DOMAIN ); ?></h2>

	<?php if ( empty( $all_reports ) ) : ?>
		<div class="woocommerce-message woocommerce-message--info">
			<p><?php esc_html_e( 'You have no astrology reports yet.', ASTRO_WOO_TEXT_DOMAIN ); ?></p>
			<p>
				<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="button">
					<?php esc_html_e( 'Browse Astrology Services', ASTRO_WOO_TEXT_DOMAIN ); ?>
				</a>
			</p>
		</div>
	<?php else : ?>

		<p class="astro-reports-intro">
			<?php printf( 
				esc_html__( 'You have %d order(s) with astrology reports. View and manage them below.', ASTRO_WOO_TEXT_DOMAIN ),
				count( $all_reports )
			); ?>
		</p>

		<?php foreach ( $all_reports as $order_data ) : 
			$order = $order_data['order'];
			$reports = $order_data['reports'];
			?>

			<div class="astro-order-section">
				<div class="astro-order-header">
					<h3>
						<?php 
						printf( 
							__( 'Order #%s', ASTRO_WOO_TEXT_DOMAIN ), 
							$order->get_order_number() 
						); 
						?>
						<span class="order-date">
							<?php echo esc_html( $order->get_date_created()->date_i18n( get_option( 'date_format' ) ) ); ?>
						</span>
					</h3>
					<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" class="button button-small">
						<?php esc_html_e( 'View Order Details', ASTRO_WOO_TEXT_DOMAIN ); ?>
					</a>
				</div>

				<div class="astro-reports-grid">
					<?php foreach ( $reports as $index => $report ) : ?>
						
						<div class="astro-report-item <?php echo esc_attr( $report['status'] ); ?>">
							<div class="astro-report-icon">
								<?php
								$icons = array(
									'single_person' => '🌟',
									'two_person'    => '💕',
									'numerology'    => '🔢',
									'tarot'         => '🃏',
									'zodiac'        => '♈',
								);
								echo isset( $icons[ $report['form_type'] ] ) ? $icons[ $report['form_type'] ] : '✨';
								?>
							</div>

							<div class="astro-report-content">
								<h4><?php echo esc_html( $order_handler->get_form_type_title( $report['form_type'] ) ); ?></h4>
								
								<?php if ( $report['status'] === 'success' ) : ?>
									<div class="astro-report-summary">
										<?php echo wp_kses_post( $order_handler->format_input_summary( $report['form_type'], $report['form_data'] ) ); ?>
									</div>

									<div class="astro-report-actions-inline">
										<button type="button" 
											class="button button-small astro-view-report" 
											data-order-id="<?php echo esc_attr( $order->get_id() ); ?>"
											data-report-index="<?php echo esc_attr( $index ); ?>">
											👁️ <?php esc_html_e( 'View Report', ASTRO_WOO_TEXT_DOMAIN ); ?>
										</button>
										<button type="button" 
											class="button button-small button-primary astro-email-report" 
											data-order-id="<?php echo esc_attr( $order->get_id() ); ?>" 
											data-report-index="<?php echo esc_attr( $index ); ?>">
											📧 <?php esc_html_e( 'Email Me', ASTRO_WOO_TEXT_DOMAIN ); ?>
										</button>
									</div>
									<div class="astro-email-status" id="email-status-<?php echo esc_attr( $order->get_id() . '-' . $index ); ?>" style="display: none;"></div>

									<!-- Hidden full report content -->
									<div class="astro-report-full-content" id="report-content-<?php echo esc_attr( $order->get_id() . '-' . $index ); ?>" style="display: none;">
										<div class="astro-report-modal-inner">
											<h3><?php echo esc_html( $order_handler->get_form_type_title( $report['form_type'] ) ); ?></h3>
											
											<div class="astro-report-input-summary">
												<h4><?php esc_html_e( 'Your Details:', ASTRO_WOO_TEXT_DOMAIN ); ?></h4>
												<?php echo wp_kses_post( $order_handler->format_input_summary( $report['form_type'], $report['form_data'] ) ); ?>
											</div>

											<div class="astro-report-result">
												<h4><?php esc_html_e( 'Report:', ASTRO_WOO_TEXT_DOMAIN ); ?></h4>
												<?php echo wp_kses_post( $order_handler->format_report_display( $report['result'], $report['form_type'] ) ); ?>
											</div>
										</div>
									</div>

								<?php else : ?>
									<p class="astro-report-error">
										<?php esc_html_e( 'Report generation failed', ASTRO_WOO_TEXT_DOMAIN ); ?>
									</p>
								<?php endif; ?>
							</div>
						</div>

					<?php endforeach; ?>
				</div>
			</div>

		<?php endforeach; ?>

	<?php endif; ?>
</div>

<!-- Modal for viewing full report -->
<div id="astro-report-modal" class="astro-modal" style="display: none;">
	<div class="astro-modal-overlay"></div>
	<div class="astro-modal-content">
		<button class="astro-modal-close">&times;</button>
		<div class="astro-modal-body"></div>
	</div>
</div>

<style>
.woocommerce-MyAccount-astrology-reports {
	margin: 20px 0;
}
.astro-reports-intro {
	background: #f0f8ff;
	padding: 15px;
	border-left: 4px solid #0073aa;
	margin-bottom: 30px;
	border-radius: 4px;
}
.astro-order-section {
	background: white;
	border: 1px solid #ddd;
	border-radius: 8px;
	padding: 20px;
	margin-bottom: 30px;
	box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
.astro-order-header {
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding-bottom: 15px;
	margin-bottom: 20px;
	border-bottom: 2px solid #eee;
}
.astro-order-header h3 {
	margin: 0;
	display: flex;
	align-items: center;
	gap: 15px;
}
.astro-order-header .order-date {
	font-size: 14px;
	color: #666;
	font-weight: normal;
}
.astro-reports-grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
	gap: 20px;
}
.astro-report-item {
	background: #f9f9f9;
	border: 2px solid #ddd;
	border-radius: 8px;
	padding: 20px;
	transition: all 0.3s ease;
}
.astro-report-item.success {
	border-color: #4caf50;
}
.astro-report-item:hover {
	box-shadow: 0 4px 8px rgba(0,0,0,0.1);
	transform: translateY(-2px);
}
.astro-report-icon {
	font-size: 48px;
	text-align: center;
	margin-bottom: 15px;
}
.astro-report-content h4 {
	margin: 0 0 15px 0;
	text-align: center;
	color: #333;
}
.astro-report-summary {
	background: white;
	padding: 12px;
	border-radius: 5px;
	margin-bottom: 15px;
	font-size: 13px;
}
.astro-report-summary p {
	margin: 5px 0;
	line-height: 1.4;
}
.astro-report-actions-inline {
	display: flex;
	gap: 10px;
	flex-wrap: wrap;
}
.astro-report-actions-inline .button {
	flex: 1;
	min-width: 120px;
	text-align: center;
}
.astro-email-status {
	margin-top: 10px;
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
.astro-report-error {
	color: #d32f2f;
	text-align: center;
	font-weight: 500;
}

/* Modal Styles */
.astro-modal {
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	z-index: 999999;
}
.astro-modal-overlay {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background: rgba(0,0,0,0.7);
}
.astro-modal-content {
	position: relative;
	background: white;
	max-width: 800px;
	max-height: 90vh;
	margin: 50px auto;
	border-radius: 8px;
	overflow-y: auto;
	box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}
.astro-modal-close {
	position: sticky;
	top: 0;
	right: 0;
	float: right;
	background: #f44336;
	color: white;
	border: none;
	width: 40px;
	height: 40px;
	font-size: 24px;
	cursor: pointer;
	border-radius: 0 8px 0 8px;
	z-index: 10;
}
.astro-modal-close:hover {
	background: #d32f2f;
}
.astro-modal-body {
	padding: 30px;
	clear: both;
}
.astro-report-modal-inner h3 {
	margin-top: 0;
	color: #333;
	font-size: 24px;
}
.astro-report-input-summary {
	background: #f5f5f5;
	padding: 15px;
	border-radius: 5px;
	margin-bottom: 20px;
}
.astro-report-result {
	padding: 15px;
	background: #fff;
	border: 1px solid #e0e0e0;
	border-radius: 5px;
	max-height: 400px;
	overflow-y: auto;
}
@media (max-width: 768px) {
	.astro-reports-grid {
		grid-template-columns: 1fr;
	}
	.astro-order-header {
		flex-direction: column;
		align-items: flex-start;
		gap: 10px;
	}
	.astro-modal-content {
		margin: 20px;
		max-height: 85vh;
	}
}
</style>

<script>
jQuery(document).ready(function($) {
	// View Report Modal
	$('.astro-view-report').on('click', function() {
		var orderId = $(this).data('order-id');
		var reportIndex = $(this).data('report-index');
		var reportContent = $('#report-content-' + orderId + '-' + reportIndex).html();
		
		$('#astro-report-modal .astro-modal-body').html(reportContent);
		$('#astro-report-modal').fadeIn(300);
		$('body').css('overflow', 'hidden');
	});

	// Close Modal
	$('.astro-modal-close, .astro-modal-overlay').on('click', function() {
		$('#astro-report-modal').fadeOut(300);
		$('body').css('overflow', 'auto');
	});

	// Email Report Handler
	$('.astro-email-report').on('click', function() {
		var $button = $(this);
		var orderId = $button.data('order-id');
		var reportIndex = $button.data('report-index');
		var $status = $('#email-status-' + orderId + '-' + reportIndex);
		
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
						$button.prop('disabled', false).text('📧 Email Me');
						$status.fadeOut();
					}, 3000);
				} else {
					$status.removeClass('loading success').addClass('error')
						.html('❌ ' + response.data.message);
					$button.prop('disabled', false).text('📧 Email Me');
				}
			},
			error: function() {
				$status.removeClass('loading success').addClass('error')
					.html('❌ Failed to send email. Please try again.');
				$button.prop('disabled', false).text('📧 Email Me');
			}
		});
	});
});
</script>
