<?php
/**
 * API communication handler - supports dynamic form type mappings
 *
 * @package Astrology_WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Astro_Woo_API {

	/**
	 * Get all mappings (delegates to admin class)
	 */
	private function get_mappings() {
		return Astro_Woo_Admin::get_custom_mappings();
	}

	/**
	 * Get endpoint for a form type (now reads from dynamic mappings)
	 */
	public function get_endpoint_for_type( $form_type ) {
		foreach ( $this->get_mappings() as $m ) {
			if ( $m['form_type'] === $form_type ) {
				return $m['endpoint'];
			}
		}
		return '';
	}

	/**
	 * Get field definitions for a form type
	 */
	public function get_fields_for_type( $form_type ) {
		foreach ( $this->get_mappings() as $m ) {
			if ( $m['form_type'] === $form_type ) {
				return $m['fields'] ?? array();
			}
		}
		return array();
	}

	/**
	 * Build API payload dynamically from form fields
	 * Maps form_data keys directly to payload.
	 * For date fields (YYYY-MM-DD) it splits into day/month/year.
	 * For time fields (HH:MM) it splits into hour/min.
	 */
	public function build_payload( $form_type, $form_data ) {
		$fields  = $this->get_fields_for_type( $form_type );
		$payload = array();

		foreach ( $fields as $field ) {
			$key  = $field['key'];
			$type = $field['type'] ?? 'text';
			$val  = $form_data[ $key ] ?? null;

			if ( $val === null || $val === '' ) {
				continue;
			}

			if ( $type === 'date' ) {
				$parts = explode( '-', $val );
				// Auto-generate sub-keys: birth_date -> day, month, year | person1_birth_date -> p1_day etc
				$prefix = $this->date_prefix( $key );
				$payload[ $prefix . 'day' ]   = isset( $parts[2] ) ? (int) $parts[2]  : 1;
				$payload[ $prefix . 'month' ] = isset( $parts[1] ) ? (int) $parts[1]  : 1;
				$payload[ $prefix . 'year' ]  = isset( $parts[0] ) ? (int) $parts[0]  : 1990;
			} elseif ( $type === 'time' ) {
				$parts  = explode( ':', $val );
				$prefix = $this->time_prefix( $key );
				$payload[ $prefix . 'hour' ] = isset( $parts[0] ) ? (int) $parts[0] : 0;
				$payload[ $prefix . 'min' ]  = isset( $parts[1] ) ? (int) $parts[1] : 0;
			} elseif ( $type === 'number' ) {
				$payload[ $key ] = (float) $val;
			} elseif ( $type === 'checkbox' ) {
				$payload[ $key ] = (bool) $val;
			} else {
				$payload[ $key ] = $val;
			}
		}

		return $payload;
	}

	/**
	 * Determine date prefix from field key.
	 * e.g. birth_date -> ''   |  person1_birth_date -> 'p1_'  |  person2_birth_date -> 'p2_'
	 */
	private function date_prefix( $key ) {
		if ( strpos( $key, 'person1' ) !== false ) return 'p1_';
		if ( strpos( $key, 'person2' ) !== false ) return 'p2_';
		if ( strpos( $key, 'm_' ) === 0 )          return 'm_';
		if ( strpos( $key, 'f_' ) === 0 )          return 'f_';
		return '';
	}

	private function time_prefix( $key ) {
		return $this->date_prefix( $key );
	}

	/**
	 * Resolve full URL: if endpoint is a full URL use it directly, else append to base.
	 */
	private function resolve_url( $endpoint ) {
		if ( preg_match( '#^https?://#i', $endpoint ) ) {
			return $endpoint; // Full URL override
		}
		$api_url = get_option( 'astro_woo_api_url', 'https://json.astrologyapi.com/v1' );
		return rtrim( $api_url, '/' ) . '/' . ltrim( $endpoint, '/' );
	}

	/**
	 * Call API endpoint
	 */
	public function call_api( $endpoint, $payload ) {
		$api_user_id = get_option( 'astro_woo_api_user_id', '' );
		$api_key     = get_option( 'astro_woo_api_key', '' );
		$debug_mode  = get_option( 'astro_woo_debug_mode', '0' );

		if ( empty( $api_user_id ) || empty( $api_key ) ) {
			return new WP_Error( 'api_config_missing', __( 'API credentials are not configured.', ASTRO_WOO_TEXT_DOMAIN ) );
		}

		$full_url = $this->resolve_url( $endpoint );

		$headers = array(
			'Authorization' => 'Basic ' . base64_encode( $api_user_id . ':' . $api_key ),
			'Content-Type'  => 'application/json',
			'Accept'        => 'application/json',
		);

		if ( $debug_mode === '1' ) {
			$this->log( 'API Request', array( 'url' => $full_url, 'payload' => $payload ) );
		}

		$response = wp_remote_post( $full_url, array(
			'headers' => $headers,
			'body'    => wp_json_encode( $payload ),
			'timeout' => 30,
		) );

		if ( is_wp_error( $response ) ) {
			if ( $debug_mode === '1' ) $this->log( 'API Error', $response->get_error_message() );
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( $debug_mode === '1' ) $this->log( 'API Response', array( 'code' => $code, 'data' => $data ) );

		if ( $code !== 200 && $code !== 201 ) {
			return new WP_Error( 'api_error', sprintf( __( 'API returned error code %d', ASTRO_WOO_TEXT_DOMAIN ), $code ) );
		}

		return $data;
	}

	private function log( $title, $data ) {
		if ( function_exists( 'wc_get_logger' ) ) {
			$logger = wc_get_logger();
			$logger->info( $title . ': ' . print_r( $data, true ), array( 'source' => 'astrology-woo' ) );
		} else {
			error_log( '[Astrology WooCommerce] ' . $title . ': ' . print_r( $data, true ) );
		}
	}
}
