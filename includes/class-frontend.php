<?php
/**
 * Frontend form display - renders dynamic forms from mapping definitions
 *
 * @package Astrology_WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Astro_Woo_Frontend {

	public function enqueue_assets() {
		if ( ! is_product() ) return;
		global $post;
		$form_type = get_post_meta( $post->ID, '_astro_form_type', true );
		if ( empty( $form_type ) ) return;

		wp_enqueue_style( 'astro-woo-frontend', ASTRO_WOO_URL . 'assets/css/frontend.css', array(), ASTRO_WOO_VERSION );
		wp_enqueue_script( 'astro-woo-frontend', ASTRO_WOO_URL . 'assets/js/frontend.js', array( 'jquery' ), ASTRO_WOO_VERSION, true );
		wp_localize_script( 'astro-woo-frontend', 'astroWoo', array(
			'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'astro_woo_form' ),
			'formType' => $form_type,
			'i18n'     => array(
				'required'    => __( 'This field is required', ASTRO_WOO_TEXT_DOMAIN ),
				'invalidDate' => __( 'Invalid date format', ASTRO_WOO_TEXT_DOMAIN ),
				'invalidTime' => __( 'Invalid time format', ASTRO_WOO_TEXT_DOMAIN ),
				'fillForm'    => __( 'Please fill in all required fields before adding to cart', ASTRO_WOO_TEXT_DOMAIN ),
			),
		) );
	}

	/**
	 * Display dynamic form on product page
	 */
	public function display_form() {
		global $post;
		$form_type = get_post_meta( $post->ID, '_astro_form_type', true );
		if ( empty( $form_type ) ) return;

		// Look up fields from dynamic mappings
		$mappings   = Astro_Woo_Admin::get_custom_mappings();
		$mapping    = null;
		foreach ( $mappings as $m ) {
			if ( $m['form_type'] === $form_type ) { $mapping = $m; break; }
		}

		if ( ! $mapping ) {
			// Fallback to legacy templates if available
			$template_file = ASTRO_WOO_DIR . 'templates/form-' . $form_type . '.php';
			if ( file_exists( $template_file ) ) {
				include $template_file;
			} else {
				echo '<div class="astro-woo-form-error"><p>' . esc_html__( 'Form configuration not found.', ASTRO_WOO_TEXT_DOMAIN ) . '</p></div>';
			}
			return;
		}

		$fields      = $mapping['fields'] ?? array();
		$description = get_post_meta( $post->ID, '_astro_form_description', true );
		?>
		<div class="astro-woo-form" id="astro-woo-form-<?php echo esc_attr( $form_type ); ?>">
			<h3 class="astro-form-title"><?php echo esc_html( $mapping['description'] ?: __( 'Your Details', ASTRO_WOO_TEXT_DOMAIN ) ); ?></h3>
			<?php if ( $description ) : ?>
				<p class="astro-form-desc"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>

			<div class="astro-form-fields">
				<?php foreach ( $fields as $field ) : ?>
					<?php $this->render_field( $form_type, $field ); ?>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render a single field
	 */
	private function render_field( $form_type, $field ) {
		$key      = sanitize_key( $field['key'] );
		$label    = esc_html( $field['label'] ?? $key );
		$type     = $field['type'] ?? 'text';
		$required = ! empty( $field['required'] );
		$options  = $field['options'] ?? '';
		$input_id = 'astro-field-' . $key;
		$req_attr = $required ? ' required' : '';
		$req_star = $required ? ' <span style="color:red">*</span>' : '';
		?>
		<div class="astro-field-group" data-field="<?php echo esc_attr( $key ); ?>">
			<label for="<?php echo esc_attr( $input_id ); ?>"><?php echo $label . $req_star; ?></label>

			<?php if ( $type === 'textarea' ) : ?>
				<textarea id="<?php echo esc_attr( $input_id ); ?>"
				          name="astro_form_data[<?php echo esc_attr( $key ); ?>]"
				          class="astro-field"
				          rows="4"
				          <?php echo $req_attr; ?>></textarea>

			<?php elseif ( $type === 'select' ) : ?>
				<select id="<?php echo esc_attr( $input_id ); ?>"
				        name="astro_form_data[<?php echo esc_attr( $key ); ?>]"
				        class="astro-field"
				        <?php echo $req_attr; ?>>
					<option value=""><?php echo esc_html__( '-- Select --', ASTRO_WOO_TEXT_DOMAIN ); ?></option>
					<?php foreach ( $this->parse_options( $options ) as $val => $opt_label ) : ?>
						<option value="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $opt_label ); ?></option>
					<?php endforeach; ?>
				</select>

			<?php elseif ( $type === 'radio' ) : ?>
				<div class="astro-radio-group">
					<?php foreach ( $this->parse_options( $options ) as $val => $opt_label ) : ?>
						<label class="astro-radio-label">
							<input type="radio"
							       name="astro_form_data[<?php echo esc_attr( $key ); ?>]"
							       value="<?php echo esc_attr( $val ); ?>"
							       class="astro-field"
							       <?php echo $req_attr; ?> />
							<?php echo esc_html( $opt_label ); ?>
						</label>
					<?php endforeach; ?>
				</div>

			<?php elseif ( $type === 'checkbox' ) : ?>
				<label class="astro-checkbox-label">
					<input type="checkbox"
					       id="<?php echo esc_attr( $input_id ); ?>"
					       name="astro_form_data[<?php echo esc_attr( $key ); ?>]"
					       value="1"
					       class="astro-field" />
					<?php echo $label; ?>
				</label>

			<?php else :
				// text, email, number, date, time, etc
				$html_type = in_array( $type, array( 'text','email','number','date','time','password' ), true ) ? $type : 'text';
				?>
				<input type="<?php echo esc_attr( $html_type ); ?>"
				       id="<?php echo esc_attr( $input_id ); ?>"
				       name="astro_form_data[<?php echo esc_attr( $key ); ?>]"
				       class="astro-field"
				       <?php echo $req_attr; ?> />
			<?php endif; ?>

			<span class="astro-field-error" style="display:none;color:red;font-size:12px;"></span>
		</div>
		<?php
	}

	/**
	 * Parse options string: "value|Label\nvalue2|Label2"
	 */
	private function parse_options( $options_str ) {
		$options = array();
		if ( empty( $options_str ) ) return $options;
		$lines = explode( "\n", $options_str );
		foreach ( $lines as $line ) {
			$line = trim( $line );
			if ( empty( $line ) ) continue;
			if ( strpos( $line, '|' ) !== false ) {
				list( $val, $lbl ) = explode( '|', $line, 2 );
				$options[ trim( $val ) ] = trim( $lbl );
			} else {
				$options[ $line ] = $line;
			}
		}
		return $options;
	}

	/**
	 * AJAX validation - dynamic, uses mapping fields
	 */
	public function ajax_validate_form() {
		check_ajax_referer( 'astro_woo_form', 'nonce' );
		$form_type = isset( $_POST['form_type'] ) ? sanitize_text_field( $_POST['form_type'] ) : '';
		$form_data = isset( $_POST['form_data'] ) ? $_POST['form_data'] : array();
		$errors    = $this->validate_form_data( $form_type, $form_data );
		if ( ! empty( $errors ) ) {
			wp_send_json_error( array( 'errors' => $errors ) );
		}
		wp_send_json_success( array( 'message' => __( 'Validation successful', ASTRO_WOO_TEXT_DOMAIN ) ) );
	}

	private function validate_form_data( $form_type, $form_data ) {
		$errors   = array();
		$mappings = Astro_Woo_Admin::get_custom_mappings();
		$mapping  = null;
		foreach ( $mappings as $m ) {
			if ( $m['form_type'] === $form_type ) { $mapping = $m; break; }
		}
		if ( ! $mapping ) return $errors;

		foreach ( $mapping['fields'] as $field ) {
			if ( empty( $field['required'] ) ) continue;
			$key = $field['key'];
			if ( empty( $form_data[ $key ] ) ) {
				$errors[ $key ] = sprintf( __( '%s is required', ASTRO_WOO_TEXT_DOMAIN ), $field['label'] );
			}
		}
		return $errors;
	}

	public function add_account_endpoint() {
		add_rewrite_endpoint( 'astrology-reports', EP_ROOT | EP_PAGES );
	}

	public function add_account_menu_item( $items ) {
		$new_items = array();
		foreach ( $items as $key => $value ) {
			$new_items[ $key ] = $value;
			if ( $key === 'orders' ) {
				$new_items['astrology-reports'] = __( 'Astrology Reports', ASTRO_WOO_TEXT_DOMAIN );
			}
		}
		return $new_items;
	}

	public function display_account_reports() {
		$current_user    = wp_get_current_user();
		$customer_orders = wc_get_orders( array( 'customer_id' => $current_user->ID, 'limit' => -1, 'orderby' => 'date', 'order' => 'DESC' ) );
		$all_reports     = array();
		foreach ( $customer_orders as $order ) {
			$reports = $order->get_meta( '_astro_reports' );
			if ( ! empty( $reports ) ) {
				$all_reports[] = array( 'order' => $order, 'reports' => $reports );
			}
		}
		include ASTRO_WOO_DIR . 'templates/account-reports.php';
	}
}
