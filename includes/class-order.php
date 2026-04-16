<?php
/**
 * Order processing and report generation
 *
 * @package Astrology_WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Astro_Woo_Order {

	/**
	 * Generate reports when order is completed
	 */
	public function generate_reports( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return;
		}

		$api = new Astro_Woo_API();
		$reports = array();

		foreach ( $order->get_items() as $item_id => $item ) {
			$form_type = $item->get_meta( '_astro_form_type' );
			$form_data = $item->get_meta( '_astro_form_data' );

			if ( empty( $form_type ) || empty( $form_data ) ) {
				continue;
			}

			// Get API endpoint
			$endpoint = $api->get_endpoint_for_type( $form_type );

			if ( empty( $endpoint ) ) {
				continue;
			}

			// Build payload
			$payload = $api->build_payload( $form_type, $form_data );

			// Call API
			$result = $api->call_api( $endpoint, $payload );

			// Store result
			$report_data = array(
				'item_id'    => $item_id,
				'form_type'  => $form_type,
				'form_data'  => $form_data,
				'endpoint'   => $endpoint,
				'payload'    => $payload,
				'result'     => $result,
				'generated'  => current_time( 'mysql' ),
				'status'     => is_wp_error( $result ) ? 'error' : 'success',
			);

			$reports[] = $report_data;

			// Save to order item meta
			wc_update_order_item_meta( $item_id, '_astro_report', $report_data );
		}

		// Save all reports to order meta
		$order->update_meta_data( '_astro_reports', $reports );
		$order->update_meta_data( '_astro_reports_generated', current_time( 'mysql' ) );
		$order->save();

		// Add order note
		$success_count = count( array_filter( $reports, function( $r ) {
			return $r['status'] === 'success';
		} ) );

		$order->add_order_note(
			sprintf(
				__( 'Astrology reports generated: %d successful, %d failed', ASTRO_WOO_TEXT_DOMAIN ),
				$success_count,
				count( $reports ) - $success_count
			)
		);
	}

	/**
	 * Display reports on order details page
	 */
	public function display_reports( $order ) {
		// Handle both order object and order ID for HPOS compatibility
		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}
		
		if ( ! $order ) {
			return;
		}
		
		$reports = $order->get_meta( '_astro_reports' );

		if ( empty( $reports ) ) {
			return;
		}

		// Make $this available in template
		$order_handler = $this;
		
		include ASTRO_WOO_DIR . 'templates/order-reports.php';
	}

	/**
	 * Display reports on admin order page (HPOS compatible)
	 */
	public function display_reports_admin( $order ) {
		// Ensure we have a proper order object for HPOS
		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}
		
		if ( ! $order ) {
			return;
		}
		
		$reports = $order->get_meta( '_astro_reports' );

		if ( empty( $reports ) ) {
			return;
		}

		// Make $this available in template
		$order_handler = $this;
		
		// Add admin-specific wrapper
		echo '<div class="order_data_column" style="clear:both; width:100%;">';
		include ASTRO_WOO_DIR . 'templates/order-reports.php';
		echo '</div>';
	}

	/**
	 * Add reports to order completion email
	 */
	public function add_reports_to_email( $order, $sent_to_admin, $plain_text, $email ) {
		// Only add to customer emails
		if ( $sent_to_admin ) {
			return;
		}

		// Only for completed order emails
		if ( ! in_array( $email->id, array( 'customer_completed_order', 'customer_invoice' ) ) ) {
			return;
		}

		// Check if emails are enabled
		if ( get_option( 'astro_woo_enable_emails', '1' ) !== '1' ) {
			return;
		}

		$reports = $order->get_meta( '_astro_reports' );

		if ( empty( $reports ) ) {
			return;
		}

		if ( $plain_text ) {
			$this->display_reports_plain_text( $reports, $order );
		} else {
			$this->display_reports_html( $reports, $order );
		}
	}

	/**
	 * Display reports in HTML email
	 */
	private function display_reports_html( $reports, $order ) {
		?>
		<h2><?php esc_html_e( 'Your Astrology Reports', ASTRO_WOO_TEXT_DOMAIN ); ?></h2>
		<p><?php esc_html_e( 'Your personalized astrology reports are ready!', ASTRO_WOO_TEXT_DOMAIN ); ?></p>

		<?php foreach ( $reports as $report ) : ?>
			<?php if ( $report['status'] === 'success' && ! is_wp_error( $report['result'] ) ) : ?>
				<div style="background: #f7f7f7; padding: 20px; margin: 20px 0; border-radius: 5px;">
					<h3><?php echo esc_html( $this->get_form_type_title( $report['form_type'] ) ); ?></h3>
					
					<div style="background: white; padding: 15px; margin-top: 10px;">
						<?php echo wp_kses_post( $this->format_report_output( $report['result'], $report['form_type'] ) ); ?>
					</div>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>

		<p>
			<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" style="color: #0073aa;">
				<?php esc_html_e( 'View full reports in your account', ASTRO_WOO_TEXT_DOMAIN ); ?>
			</a>
		</p>
		<?php
	}

	/**
	 * Display reports in plain text email
	 */
	private function display_reports_plain_text( $reports, $order ) {
		echo "\n\n";
		echo "========================================\n";
		echo esc_html__( 'YOUR ASTROLOGY REPORTS', ASTRO_WOO_TEXT_DOMAIN ) . "\n";
		echo "========================================\n\n";

		foreach ( $reports as $report ) {
			if ( $report['status'] === 'success' && ! is_wp_error( $report['result'] ) ) {
				echo esc_html( $this->get_form_type_title( $report['form_type'] ) ) . "\n";
				echo "----------------------------------------\n";
				echo wp_kses_post( $this->format_report_output( $report['result'], $report['form_type'], true ) );
				echo "\n\n";
			}
		}

		echo esc_html__( 'View full reports:', ASTRO_WOO_TEXT_DOMAIN ) . "\n";
		echo esc_url( $order->get_view_order_url() ) . "\n\n";
	}

	/**
	 * Add regenerate action to order actions (HPOS compatible)
	 */
	public function add_regenerate_action( $actions ) {
		global $theorder;
		
		// HPOS compatible: try $theorder first, then fall back to global $post
		if ( ! $theorder ) {
			global $post;
			if ( $post ) {
				$theorder = wc_get_order( $post->ID );
			}
		}
		
		if ( ! $theorder ) {
			return $actions;
		}

		// Check if order has astrology products
		$has_astro_products = false;
		foreach ( $theorder->get_items() as $item ) {
			if ( $item->get_meta( '_astro_form_type' ) ) {
				$has_astro_products = true;
				break;
			}
		}

		if ( $has_astro_products ) {
			$actions['astro_regenerate_reports'] = __( 'Regenerate Astrology Reports', ASTRO_WOO_TEXT_DOMAIN );
		}

		return $actions;
	}

	/**
	 * Handle regenerate reports action
	 */
	public function regenerate_reports_action( $order ) {
		// Remove old reports
		$order->delete_meta_data( '_astro_reports' );
		$order->delete_meta_data( '_astro_reports_generated' );
		$order->save();

		// Generate new reports
		$this->generate_reports( $order->get_id() );

		// Add admin notice
		$order->add_order_note( __( 'Astrology reports regenerated by admin', ASTRO_WOO_TEXT_DOMAIN ) );
	}

	/**
	 * Get form type title
	 */
	public function get_form_type_title( $form_type ) {
		$titles = array(
			'single_person' => __( 'Birth Chart Report', ASTRO_WOO_TEXT_DOMAIN ),
			'two_person'    => __( 'Love Compatibility Report', ASTRO_WOO_TEXT_DOMAIN ),
			'numerology'    => __( 'Numerology Report', ASTRO_WOO_TEXT_DOMAIN ),
			'tarot'         => __( 'Tarot Reading', ASTRO_WOO_TEXT_DOMAIN ),
			'zodiac'        => __( 'Zodiac Horoscope', ASTRO_WOO_TEXT_DOMAIN ),
		);

		return isset( $titles[ $form_type ] ) ? $titles[ $form_type ] : ucfirst( $form_type );
	}

	/**
	 * Format report output for display
	 */
	private function format_report_output( $result, $form_type, $plain_text = false ) {
		if ( is_wp_error( $result ) ) {
			return $plain_text 
				? esc_html__( 'Error generating report', ASTRO_WOO_TEXT_DOMAIN )
				: '<p class="error">' . esc_html__( 'Error generating report', ASTRO_WOO_TEXT_DOMAIN ) . '</p>';
		}

		// Convert array/object to formatted output
		if ( is_array( $result ) || is_object( $result ) ) {
			if ( $plain_text ) {
				return print_r( $result, true );
			} else {
				$output = '<div class="astro-report-data">';
				$output .= $this->format_array_recursive( $result );
				$output .= '</div>';
				return $output;
			}
		}

		return $plain_text ? wp_strip_all_tags( $result ) : wp_kses_post( $result );
	}

	/**
	 * Format array recursively for HTML display
	 */
	public function format_array_recursive( $data, $level = 0 ) {
		$output = '';

		if ( is_array( $data ) ) {
			foreach ( $data as $key => $value ) {
				if ( is_array( $value ) || is_object( $value ) ) {
					$output .= '<div class="report-section" style="margin-left: ' . ( $level * 20 ) . 'px;">';
					$output .= '<strong>' . esc_html( ucwords( str_replace( '_', ' ', $key ) ) ) . ':</strong>';
					$output .= $this->format_array_recursive( $value, $level + 1 );
					$output .= '</div>';
				} else {
					$output .= '<div style="margin-left: ' . ( $level * 20 ) . 'px;">';
					$output .= '<strong>' . esc_html( ucwords( str_replace( '_', ' ', $key ) ) ) . ':</strong> ';
					$output .= esc_html( $value );
					$output .= '</div>';
				}
			}
		}

		return $output;
	}

	/**
	 * Format input summary for display
	 */
	public function format_input_summary( $form_type, $form_data ) {
		$output = '<div class="input-summary">';

		switch ( $form_type ) {
			case 'single_person':
				$output .= '<p><strong>' . __( 'Name:', ASTRO_WOO_TEXT_DOMAIN ) . '</strong> ' . esc_html( $form_data['name'] ?? '' ) . '</p>';
				$output .= '<p><strong>' . __( 'Birth Date:', ASTRO_WOO_TEXT_DOMAIN ) . '</strong> ' . esc_html( $form_data['birth_date'] ?? '' ) . '</p>';
				$output .= '<p><strong>' . __( 'Birth Time:', ASTRO_WOO_TEXT_DOMAIN ) . '</strong> ' . esc_html( $form_data['birth_time'] ?? '' ) . '</p>';
				$output .= '<p><strong>' . __( 'Birth Place:', ASTRO_WOO_TEXT_DOMAIN ) . '</strong> ' . esc_html( $form_data['birth_place'] ?? '' ) . '</p>';
				break;

			case 'two_person':
				$output .= '<p><strong>' . __( 'Person 1:', ASTRO_WOO_TEXT_DOMAIN ) . '</strong> ' . esc_html( $form_data['person1_name'] ?? '' ) . ' - ' . esc_html( $form_data['person1_birth_date'] ?? '' ) . '</p>';
				$output .= '<p><strong>' . __( 'Person 2:', ASTRO_WOO_TEXT_DOMAIN ) . '</strong> ' . esc_html( $form_data['person2_name'] ?? '' ) . ' - ' . esc_html( $form_data['person2_birth_date'] ?? '' ) . '</p>';
				break;

			case 'numerology':
				$output .= '<p><strong>' . __( 'Name:', ASTRO_WOO_TEXT_DOMAIN ) . '</strong> ' . esc_html( $form_data['full_name'] ?? '' ) . '</p>';
				if ( ! empty( $form_data['birth_date'] ) ) {
					$output .= '<p><strong>' . __( 'Birth Date:', ASTRO_WOO_TEXT_DOMAIN ) . '</strong> ' . esc_html( $form_data['birth_date'] ) . '</p>';
				}
				break;

			case 'tarot':
				$output .= '<p><strong>' . __( 'Question:', ASTRO_WOO_TEXT_DOMAIN ) . '</strong> ' . esc_html( $form_data['question'] ?? '' ) . '</p>';
				$output .= '<p><strong>' . __( 'Spread:', ASTRO_WOO_TEXT_DOMAIN ) . '</strong> ' . esc_html( $form_data['card_spread'] ?? '' ) . '</p>';
				break;

			case 'zodiac':
				$output .= '<p><strong>' . __( 'Sign:', ASTRO_WOO_TEXT_DOMAIN ) . '</strong> ' . esc_html( ucfirst( $form_data['zodiac_sign'] ?? '' ) ) . '</p>';
				$output .= '<p><strong>' . __( 'Type:', ASTRO_WOO_TEXT_DOMAIN ) . '</strong> ' . esc_html( ucfirst( $form_data['report_type'] ?? 'daily' ) ) . '</p>';
				break;
		}

		$output .= '</div>';
		return $output;
	}

	/**
	 * Format report for display
	 */
	public function format_report_display( $result, $form_type ) {
		if ( is_wp_error( $result ) ) {
			return '<p class="error">' . esc_html( $result->get_error_message() ) . '</p>';
		}

		return $this->format_array_recursive( $result );
	}

	/**
	 * AJAX handler to send individual report via email
	 */
	public function ajax_send_report_email() {
		// Verify nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'astro_email_report' ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed', ASTRO_WOO_TEXT_DOMAIN ) ) );
		}

		// Get order ID and report index
		$order_id = isset( $_POST['order_id'] ) ? intval( $_POST['order_id'] ) : 0;
		$report_index = isset( $_POST['report_index'] ) ? intval( $_POST['report_index'] ) : 0;

		if ( ! $order_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid order ID', ASTRO_WOO_TEXT_DOMAIN ) ) );
		}

		// Get order
		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			wp_send_json_error( array( 'message' => __( 'Order not found', ASTRO_WOO_TEXT_DOMAIN ) ) );
		}

		// Check if current user can access this order
		if ( ! current_user_can( 'manage_woocommerce' ) && get_current_user_id() != $order->get_customer_id() ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission to access this order', ASTRO_WOO_TEXT_DOMAIN ) ) );
		}

		// Get reports
		$reports = $order->get_meta( '_astro_reports' );
		if ( empty( $reports ) || ! isset( $reports[ $report_index ] ) ) {
			wp_send_json_error( array( 'message' => __( 'Report not found', ASTRO_WOO_TEXT_DOMAIN ) ) );
		}

		$report = $reports[ $report_index ];

		// Send email
		$sent = $this->send_single_report_email( $order, $report );

		if ( $sent ) {
			// Add order note
			$order->add_order_note( 
				sprintf(
					__( 'Astrology report (%s) emailed to customer', ASTRO_WOO_TEXT_DOMAIN ),
					$this->get_form_type_title( $report['form_type'] )
				)
			);

			wp_send_json_success( array( 
				'message' => __( 'Report sent to your email successfully!', ASTRO_WOO_TEXT_DOMAIN ) 
			) );
		} else {
			wp_send_json_error( array( 
				'message' => __( 'Failed to send email. Please try again.', ASTRO_WOO_TEXT_DOMAIN ) 
			) );
		}
	}

	/**
	 * Send single report via email
	 */
	private function send_single_report_email( $order, $report ) {
		$to = $order->get_billing_email();
		$subject = sprintf(
			__( 'Your %s - Order #%s', ASTRO_WOO_TEXT_DOMAIN ),
			$this->get_form_type_title( $report['form_type'] ),
			$order->get_order_number()
		);

		// Build email body
		ob_start();
		?>
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset="UTF-8">
			<style>
				body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
				.container { max-width: 600px; margin: 0 auto; padding: 20px; }
				.header { background: #0073aa; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
				.content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
				.report-card { background: white; padding: 20px; margin: 15px 0; border-radius: 5px; border: 2px solid #4caf50; }
				.report-title { color: #333; font-size: 20px; margin-bottom: 15px; }
				.input-summary { background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 15px 0; }
				.report-result { padding: 15px; background: #fff; border: 1px solid #e0e0e0; border-radius: 5px; margin: 15px 0; }
				.footer { background: #f5f5f5; padding: 15px; text-align: center; font-size: 12px; color: #666; border-radius: 0 0 5px 5px; }
				strong { color: #555; }
			</style>
		</head>
		<body>
			<div class="container">
				<div class="header">
					<h1><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h1>
					<p><?php esc_html_e( 'Your Astrology Report', ASTRO_WOO_TEXT_DOMAIN ); ?></p>
				</div>
				
				<div class="content">
					<p><?php printf( __( 'Hello %s,', ASTRO_WOO_TEXT_DOMAIN ), esc_html( $order->get_billing_first_name() ) ); ?></p>
					<p><?php esc_html_e( 'Your personalized astrology report is ready!', ASTRO_WOO_TEXT_DOMAIN ); ?></p>
					
					<div class="report-card">
						<h2 class="report-title"><?php echo esc_html( $this->get_form_type_title( $report['form_type'] ) ); ?></h2>
						
						<?php if ( $report['status'] === 'success' && ! is_wp_error( $report['result'] ) ) : ?>
							<div class="input-summary">
								<h4><?php esc_html_e( 'Your Details:', ASTRO_WOO_TEXT_DOMAIN ); ?></h4>
								<?php echo wp_kses_post( $this->format_input_summary( $report['form_type'], $report['form_data'] ) ); ?>
							</div>
							
							<div class="report-result">
								<h4><?php esc_html_e( 'Report:', ASTRO_WOO_TEXT_DOMAIN ); ?></h4>
								<?php echo wp_kses_post( $this->format_report_display( $report['result'], $report['form_type'] ) ); ?>
							</div>
						<?php endif; ?>
					</div>
					
					<p>
						<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" 
						   style="display: inline-block; padding: 12px 24px; background: #0073aa; color: white; text-decoration: none; border-radius: 5px;">
							<?php esc_html_e( 'View in Your Account', ASTRO_WOO_TEXT_DOMAIN ); ?>
						</a>
					</p>
				</div>
				
				<div class="footer">
					<p><?php printf( __( 'Order #%s', ASTRO_WOO_TEXT_DOMAIN ), $order->get_order_number() ); ?></p>
					<p>&copy; <?php echo date( 'Y' ) . ' ' . esc_html( get_bloginfo( 'name' ) ); ?></p>
				</div>
			</div>
		</body>
		</html>
		<?php
		$message = ob_get_clean();

		// Set headers for HTML email
		$headers = array(
			'Content-Type: text/html; charset=UTF-8',
			'From: ' . get_bloginfo( 'name' ) . ' <' . get_option( 'admin_email' ) . '>'
		);

		return wp_mail( $to, $subject, $message, $headers );
	}
}
