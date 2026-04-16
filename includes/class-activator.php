<?php
/**
 * Plugin activation/deactivation handler
 *
 * @package Astrology_WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Astro_Woo_Activator {

	/**
	 * Activation hook
	 */
	public static function activate() {
		// No tables needed - we use order meta
		
		// Register endpoint for My Account page
		add_rewrite_endpoint( 'astrology-reports', EP_ROOT | EP_PAGES );
		
		// Flush rewrite rules
		flush_rewrite_rules();
		
		// Set default options if not exist
		if ( ! get_option( 'astro_woo_api_url' ) ) {
			update_option( 'astro_woo_api_url', 'https://json.astrologyapi.com/v1' );
		}
		
		// Enable email notifications by default
		if ( ! get_option( 'astro_woo_enable_emails' ) ) {
			update_option( 'astro_woo_enable_emails', '1' );
		}
	}

	/**
	 * Deactivation hook
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}
}
