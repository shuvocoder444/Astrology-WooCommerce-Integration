<?php
/**
 * Admin settings page with Dynamic Form Type Mapping
 *
 * @package Astrology_WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Astro_Woo_Admin {

	public static function get_field_types() {
		return array(
			'text'     => 'Text Input',
			'email'    => 'Email',
			'number'   => 'Number',
			'date'     => 'Date',
			'time'     => 'Time',
			'textarea' => 'Textarea',
			'select'   => 'Dropdown / Select',
			'radio'    => 'Radio Buttons',
			'checkbox' => 'Checkbox',
		);
	}

	public static function get_custom_mappings() {
		$default = array(
			array(
				'form_type'   => 'single_person',
				'endpoint'    => '/natal_chart',
				'description' => 'Birth Chart - Single person birth details',
				'fields'      => array(
					array( 'key' => 'name',       'label' => 'Full Name',       'type' => 'text',   'required' => 1, 'options' => '' ),
					array( 'key' => 'birth_date', 'label' => 'Date of Birth',   'type' => 'date',   'required' => 1, 'options' => '' ),
					array( 'key' => 'birth_time', 'label' => 'Time of Birth',   'type' => 'time',   'required' => 1, 'options' => '' ),
					array( 'key' => 'latitude',   'label' => 'Latitude',        'type' => 'number', 'required' => 0, 'options' => '' ),
					array( 'key' => 'longitude',  'label' => 'Longitude',       'type' => 'number', 'required' => 0, 'options' => '' ),
					array( 'key' => 'timezone',   'label' => 'Timezone Offset', 'type' => 'number', 'required' => 0, 'options' => '' ),
				),
			),
			array(
				'form_type'   => 'two_person',
				'endpoint'    => '/match_making_report',
				'description' => 'Love Compatibility / Synastry - Two people comparison',
				'fields'      => array(
					array( 'key' => 'person1_name',       'label' => 'Person 1 Name',       'type' => 'text', 'required' => 1, 'options' => '' ),
					array( 'key' => 'person1_birth_date', 'label' => 'Person 1 Birth Date', 'type' => 'date', 'required' => 1, 'options' => '' ),
					array( 'key' => 'person1_birth_time', 'label' => 'Person 1 Birth Time', 'type' => 'time', 'required' => 1, 'options' => '' ),
					array( 'key' => 'person2_name',       'label' => 'Person 2 Name',       'type' => 'text', 'required' => 1, 'options' => '' ),
					array( 'key' => 'person2_birth_date', 'label' => 'Person 2 Birth Date', 'type' => 'date', 'required' => 1, 'options' => '' ),
					array( 'key' => 'person2_birth_time', 'label' => 'Person 2 Birth Time', 'type' => 'time', 'required' => 1, 'options' => '' ),
				),
			),
			array(
				'form_type'   => 'numerology',
				'endpoint'    => '/numerology_report',
				'description' => 'Numerology - Name and date based analysis',
				'fields'      => array(
					array( 'key' => 'full_name',  'label' => 'Full Name',     'type' => 'text', 'required' => 1, 'options' => '' ),
					array( 'key' => 'birth_date', 'label' => 'Date of Birth', 'type' => 'date', 'required' => 1, 'options' => '' ),
				),
			),
			array(
				'form_type'   => 'tarot',
				'endpoint'    => '/tarot_card_prediction',
				'description' => 'Tarot Reading - Question and card spread',
				'fields'      => array(
					array( 'key' => 'question',    'label' => 'Your Question', 'type' => 'textarea', 'required' => 1, 'options' => '' ),
					array( 'key' => 'card_spread', 'label' => 'Card Spread',  'type' => 'select',   'required' => 0, 'options' => "three_card|Three Card\npast_present_future|Past Present Future\nceltic_cross|Celtic Cross" ),
				),
			),
			array(
				'form_type'   => 'zodiac',
				'endpoint'    => '/horoscope_report',
				'description' => 'Zodiac Horoscope - Daily/Weekly/Monthly predictions',
				'fields'      => array(
					array( 'key' => 'zodiac_sign', 'label' => 'Zodiac Sign', 'type' => 'select', 'required' => 1, 'options' => "aries|Aries\ntaurus|Taurus\ngemini|Gemini\ncancer|Cancer\nleo|Leo\nvirgo|Virgo\nlibra|Libra\nscorpio|Scorpio\nsagittarius|Sagittarius\ncapricorn|Capricorn\naquarius|Aquarius\npisces|Pisces" ),
				),
			),
		);
		$saved = get_option( 'astro_woo_custom_mappings', null );
		return ( $saved !== null ) ? $saved : $default;
	}

	public function add_admin_menu() {
		add_submenu_page(
			'woocommerce',
			__( 'Astrology API Settings', ASTRO_WOO_TEXT_DOMAIN ),
			__( 'Astrology API', ASTRO_WOO_TEXT_DOMAIN ),
			'manage_woocommerce',
			'astro-woo-settings',
			array( $this, 'render_settings_page' )
		);
	}

	public function register_settings() {
		register_setting( 'astro_woo_settings', 'astro_woo_api_url' );
		register_setting( 'astro_woo_settings', 'astro_woo_api_user_id' );
		register_setting( 'astro_woo_settings', 'astro_woo_api_key' );
		register_setting( 'astro_woo_settings', 'astro_woo_debug_mode' );
		register_setting( 'astro_woo_settings', 'astro_woo_enable_emails' );

		add_action( 'wp_ajax_astro_test_api_connection', array( $this, 'ajax_test_connection' ) );
		add_action( 'wp_ajax_astro_save_mapping',        array( $this, 'ajax_save_mapping' ) );
		add_action( 'wp_ajax_astro_delete_mapping',      array( $this, 'ajax_delete_mapping' ) );
		add_action( 'wp_ajax_astro_reorder_mappings',    array( $this, 'ajax_reorder_mappings' ) );
	}

	public function ajax_test_connection() {
		check_ajax_referer( 'astro_woo_admin', 'nonce' );
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( array( 'message' => 'Unauthorized' ) );
		}
		$user_id = isset( $_POST['user_id'] ) ? sanitize_text_field( $_POST['user_id'] ) : '';
		$api_key = isset( $_POST['api_key'] ) ? sanitize_text_field( $_POST['api_key'] ) : '';
		$api_url = isset( $_POST['api_url'] ) ? esc_url_raw( $_POST['api_url'] ) : '';
		if ( empty( $user_id ) || empty( $api_key ) || empty( $api_url ) ) {
			wp_send_json_error( array( 'message' => 'Missing credentials' ) );
		}
		$test_url = rtrim( $api_url, '/' ) . '/horoscope_report';
		$response = wp_remote_post( $test_url, array(
			'headers' => array( 'Authorization' => 'Basic ' . base64_encode( $user_id . ':' . $api_key ), 'Content-Type' => 'application/json' ),
			'body'    => wp_json_encode( array( 'day'=>1,'month'=>1,'year'=>1990,'hour'=>10,'min'=>0,'lat'=>23.8103,'lon'=>90.4125,'tzone'=>6.0 ) ),
			'timeout' => 15,
		) );
		if ( is_wp_error( $response ) ) { wp_send_json_error( array( 'message' => $response->get_error_message() ) ); }
		$code = wp_remote_retrieve_response_code( $response );
		if ( $code === 200 || $code === 201 ) { wp_send_json_success( array( 'message' => 'Connection successful! API is working.' ) ); }
		elseif ( $code === 401 || $code === 403 ) { wp_send_json_error( array( 'message' => 'Authentication failed. Check credentials.' ) ); }
		else { wp_send_json_success( array( 'message' => 'API reachable (HTTP ' . $code . ')' ) ); }
	}

	public function ajax_save_mapping() {
		check_ajax_referer( 'astro_woo_admin', 'nonce' );
		if ( ! current_user_can( 'manage_woocommerce' ) ) { wp_send_json_error( array( 'message' => 'Unauthorized' ) ); }

		$index       = isset( $_POST['index'] )       ? (int) $_POST['index']                        : -1;
		$form_type   = isset( $_POST['form_type'] )   ? sanitize_key( $_POST['form_type'] )          : '';
		$endpoint    = isset( $_POST['endpoint'] )    ? sanitize_text_field( $_POST['endpoint'] )    : '';
		$description = isset( $_POST['description'] ) ? sanitize_text_field( $_POST['description'] ) : '';
		$fields_raw  = isset( $_POST['fields'] )      ? $_POST['fields']                             : array();

		if ( empty( $form_type ) || empty( $endpoint ) ) {
			wp_send_json_error( array( 'message' => 'Form Type and Endpoint are required.' ) );
		}

		$fields = array();
		if ( is_array( $fields_raw ) ) {
			foreach ( $fields_raw as $f ) {
				$key = sanitize_key( $f['key'] ?? '' );
				if ( empty( $key ) ) continue;
				$fields[] = array(
					'key'      => $key,
					'label'    => sanitize_text_field( $f['label'] ?? $key ),
					'type'     => sanitize_key( $f['type'] ?? 'text' ),
					'required' => ! empty( $f['required'] ) ? 1 : 0,
					'options'  => sanitize_textarea_field( $f['options'] ?? '' ),
				);
			}
		}

		$mappings = self::get_custom_mappings();
		$entry    = array( 'form_type' => $form_type, 'endpoint' => $endpoint, 'description' => $description, 'fields' => $fields );

		if ( $index >= 0 && isset( $mappings[ $index ] ) ) {
			$mappings[ $index ] = $entry;
		} else {
			foreach ( $mappings as $m ) {
				if ( $m['form_type'] === $form_type ) {
					wp_send_json_error( array( 'message' => 'Form type "' . $form_type . '" already exists.' ) );
				}
			}
			$mappings[] = $entry;
		}

		update_option( 'astro_woo_custom_mappings', $mappings );
		wp_send_json_success( array( 'message' => 'Mapping saved!', 'mappings' => $mappings ) );
	}

	public function ajax_delete_mapping() {
		check_ajax_referer( 'astro_woo_admin', 'nonce' );
		if ( ! current_user_can( 'manage_woocommerce' ) ) { wp_send_json_error( array( 'message' => 'Unauthorized' ) ); }
		$index    = isset( $_POST['index'] ) ? (int) $_POST['index'] : -1;
		$mappings = self::get_custom_mappings();
		if ( $index < 0 || ! isset( $mappings[ $index ] ) ) { wp_send_json_error( array( 'message' => 'Invalid index.' ) ); }
		array_splice( $mappings, $index, 1 );
		update_option( 'astro_woo_custom_mappings', $mappings );
		wp_send_json_success( array( 'message' => 'Deleted.', 'mappings' => $mappings ) );
	}

	public function ajax_reorder_mappings() {
		check_ajax_referer( 'astro_woo_admin', 'nonce' );
		if ( ! current_user_can( 'manage_woocommerce' ) ) { wp_send_json_error( array( 'message' => 'Unauthorized' ) ); }
		$order     = isset( $_POST['order'] ) ? array_map( 'intval', $_POST['order'] ) : array();
		$mappings  = self::get_custom_mappings();
		$reordered = array();
		foreach ( $order as $i ) { if ( isset( $mappings[ $i ] ) ) { $reordered[] = $mappings[ $i ]; } }
		update_option( 'astro_woo_custom_mappings', $reordered );
		wp_send_json_success( array( 'message' => 'Order saved.' ) );
	}

	public function enqueue_admin_assets( $hook ) {
		if ( 'woocommerce_page_astro-woo-settings' !== $hook ) { return; }
		wp_enqueue_style( 'astro-woo-admin', ASTRO_WOO_URL . 'assets/css/admin.css', array(), ASTRO_WOO_VERSION );
		wp_enqueue_script( 'astro-woo-admin', ASTRO_WOO_URL . 'assets/js/admin.js', array( 'jquery', 'jquery-ui-sortable' ), ASTRO_WOO_VERSION, true );
		wp_localize_script( 'astro-woo-admin', 'astroWooAdmin', array(
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
			'nonce'      => wp_create_nonce( 'astro_woo_admin' ),
			'fieldTypes' => self::get_field_types(),
			'mappings'   => self::get_custom_mappings(),
		) );
	}

	public function render_settings_page() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) { return; }

		if ( isset( $_POST['astro_woo_save_settings'] ) && check_admin_referer( 'astro_woo_settings_nonce' ) ) {
			update_option( 'astro_woo_api_url',       sanitize_text_field( $_POST['api_url'] ?? '' ) );
			update_option( 'astro_woo_api_user_id',   sanitize_text_field( $_POST['api_user_id'] ?? '' ) );
			update_option( 'astro_woo_api_key',       sanitize_text_field( $_POST['api_key'] ?? '' ) );
			update_option( 'astro_woo_debug_mode',    isset( $_POST['debug_mode'] ) ? '1' : '0' );
			update_option( 'astro_woo_enable_emails', isset( $_POST['enable_emails'] ) ? '1' : '0' );
			echo '<div class="notice notice-success is-dismissible"><p>Settings saved!</p></div>';
		}

		$api_url       = get_option( 'astro_woo_api_url', 'https://json.astrologyapi.com/v1' );
		$api_user_id   = get_option( 'astro_woo_api_user_id', '' );
		$api_key       = get_option( 'astro_woo_api_key', '' );
		$debug_mode    = get_option( 'astro_woo_debug_mode', '0' );
		$enable_emails = get_option( 'astro_woo_enable_emails', '1' );
		$mappings      = self::get_custom_mappings();
		?>
		<div class="wrap astro-woo-settings">
			<h1>Astrology API Settings</h1>

			<form method="post" action="">
				<?php wp_nonce_field( 'astro_woo_settings_nonce' ); ?>
				<table class="form-table">
					<tr>
						<th><label for="api_url">API Base URL</label></th>
						<td><input type="url" id="api_url" name="api_url" value="<?php echo esc_attr( $api_url ); ?>" class="regular-text" required />
						<p class="description">Example: https://json.astrologyapi.com/v1</p></td>
					</tr>
					<tr>
						<th><label for="api_user_id">API User ID</label></th>
						<td><input type="text" id="api_user_id" name="api_user_id" value="<?php echo esc_attr( $api_user_id ); ?>" class="regular-text" />
						<p class="description">Your API username for authentication</p></td>
					</tr>
					<tr>
						<th><label for="api_key">API Key</label></th>
						<td><input type="password" id="api_key" name="api_key" value="<?php echo esc_attr( $api_key ); ?>" class="regular-text" />
						<p class="description">Your API key/password</p></td>
					</tr>
					<tr>
						<th>Debug Mode</th>
						<td><label><input type="checkbox" name="debug_mode" value="1" <?php checked( $debug_mode, '1' ); ?> /> Enable debug logging</label>
						<p class="description">Logs will be saved in WooCommerce &gt; Status &gt; Logs</p></td>
					</tr>
					<tr>
						<th>Email Reports</th>
						<td><label><input type="checkbox" name="enable_emails" value="1" <?php checked( $enable_emails, '1' ); ?> /> Send reports via email after order completion</label></td>
					</tr>
				</table>
				<p class="submit">
					<button type="submit" name="astro_woo_save_settings" class="button button-primary">Save Settings</button>
					<button type="button" id="astro-test-connection" class="button button-secondary">Test API Connection</button>
				</p>
				<div id="astro-test-result" style="margin-top:10px;"></div>
			</form>

			<hr />

			<div class="astro-mapping-header" style="display:flex;align-items:center;gap:16px;margin-bottom:8px;">
				<h2 style="margin:0;">Form Type Mapping</h2>
				<button type="button" id="astro-add-mapping" class="button button-primary">&#43; Add New Mapping</button>
			</div>
			<p class="description">Define API endpoints and the customer form fields for each service. Drag rows to reorder.</p>

			<div id="astro-mapping-notice" style="margin:10px 0;"></div>

			<table class="widefat striped" id="astro-mapping-table">
				<thead>
					<tr>
						<th style="width:20px;"></th>
						<th>Form Type</th>
						<th>API Endpoint / URL</th>
						<th>Description</th>
						<th>Fields</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody id="astro-mapping-tbody">
				<?php foreach ( $mappings as $idx => $map ) : ?>
					<tr data-index="<?php echo esc_attr( $idx ); ?>">
						<td class="astro-drag-handle" title="Drag to reorder" style="cursor:move;text-align:center;color:#999;">&#8597;</td>
						<td><strong><?php echo esc_html( $map['form_type'] ); ?></strong></td>
						<td><code><?php echo esc_html( $map['endpoint'] ); ?></code></td>
						<td><?php echo esc_html( $map['description'] ); ?></td>
						<td>
						<?php if ( ! empty( $map['fields'] ) ) :
							$parts = array_map( function($f) { return esc_html($f['label']) . ( $f['required'] ? ' <span style="color:red">*</span>' : '' ); }, $map['fields'] );
							echo implode( ', ', $parts );
						else : ?><em>No fields</em><?php endif; ?>
						</td>
						<td>
							<button type="button" class="button button-small astro-edit-mapping" data-index="<?php echo esc_attr( $idx ); ?>">Edit</button>
							<button type="button" class="button button-small astro-delete-mapping" data-index="<?php echo esc_attr( $idx ); ?>" style="color:#a00;border-color:#a00;margin-left:4px;">Delete</button>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>

			<!-- MODAL -->
			<div id="astro-modal-overlay" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:99998;"></div>
			<div id="astro-mapping-modal" style="display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:#fff;border-radius:6px;z-index:99999;width:760px;max-width:95vw;max-height:90vh;overflow:hidden;display:none;flex-direction:column;box-shadow:0 8px 40px rgba(0,0,0,0.3);">
				<div style="padding:20px 24px;border-bottom:1px solid #ddd;display:flex;justify-content:space-between;align-items:center;">
					<h3 id="astro-modal-title" style="margin:0;font-size:16px;">Add / Edit Form Type Mapping</h3>
					<button type="button" id="astro-modal-close" style="background:none;border:none;font-size:22px;cursor:pointer;color:#666;line-height:1;">&times;</button>
				</div>
				<div id="astro-modal-body" style="padding:20px 24px;overflow-y:auto;flex:1;">
					<input type="hidden" id="astro-modal-index" value="-1" />
					<table class="form-table" style="margin:0;">
						<tr>
							<th style="width:160px;"><label for="astro-modal-form-type">Form Type Key *</label></th>
							<td>
								<input type="text" id="astro-modal-form-type" class="regular-text" placeholder="e.g. love_compatibility" />
								<p class="description">Lowercase letters, numbers, underscores only. No spaces.</p>
							</td>
						</tr>
						<tr>
							<th><label for="astro-modal-endpoint">API Endpoint / Full URL *</label></th>
							<td>
								<input type="text" id="astro-modal-endpoint" class="large-text" placeholder="/love_compatibility_report  OR  https://json.astrologyapi.com/v1/love_compatibility_report/tropical" />
								<p class="description">Relative path uses the Base URL above. Full URL overrides it completely.</p>
							</td>
						</tr>
						<tr>
							<th><label for="astro-modal-description">Description</label></th>
							<td><input type="text" id="astro-modal-description" class="large-text" placeholder="Short description of this service" /></td>
						</tr>
					</table>

					<div style="margin:20px 0 8px;display:flex;align-items:center;gap:12px;">
						<h4 style="margin:0;">Customer Input Fields</h4>
						<button type="button" id="astro-add-field" class="button button-secondary button-small">&#43; Add Field</button>
					</div>
					<p class="description" style="margin-bottom:12px;">Define what customers must fill in on the product page. Mark required fields with &#10003;. Drag to reorder.</p>

					<div id="astro-fields-container">
						<!-- rendered by JS -->
					</div>
				</div>
				<div style="padding:16px 24px;border-top:1px solid #ddd;display:flex;align-items:center;gap:10px;">
					<button type="button" id="astro-modal-save" class="button button-primary">Save Mapping</button>
					<button type="button" id="astro-modal-cancel" class="button">Cancel</button>
					<span id="astro-modal-saving" style="display:none;color:#666;">Saving...</span>
				</div>
			</div>

			<hr style="margin-top:30px;" />
			<h2>How to Use</h2>
			<ol>
				<li>Add a mapping above — set the endpoint and define the fields customers need to fill in.</li>
				<li>Create a WooCommerce product for each astrology service.</li>
				<li>In the product edit page, select the appropriate "Form Type".</li>
				<li>The corresponding form (with your defined fields) appears on the product page.</li>
				<li>When the customer completes purchase, the API is called automatically.</li>
				<li>Report is saved to order meta and displayed in My Account.</li>
			</ol>
		</div>
		<?php
	}
}
