<?php
/**
 * Plugin Name:       Astrology WooCommerce Integration
 * Plugin URI:        https://webkoders.com/
 * Description:       Complete WooCommerce integration for astrology services with dynamic forms, API integration, and automated report generation
 * Version:           3.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Shuvo
 * Author URI:        https://webkoders.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       astrology-woo
 * Domain Path:       /languages
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants
define( 'ASTRO_WOO_VERSION', '3.0.0' );
define( 'ASTRO_WOO_FILE', __FILE__ );
define( 'ASTRO_WOO_DIR', plugin_dir_path( __FILE__ ) );
define( 'ASTRO_WOO_URL', plugin_dir_url( __FILE__ ) );
define( 'ASTRO_WOO_TEXT_DOMAIN', 'astrology-woo' );

/**
 * Check if WooCommerce is active
 */
function astro_woo_check_woocommerce() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'astro_woo_wc_missing_notice' );
		return false;
	}
	return true;
}

/**
 * WooCommerce missing notice
 */
function astro_woo_wc_missing_notice() {
	?>
	<div class="error">
		<p><?php esc_html_e( 'Astrology WooCommerce Integration requires WooCommerce to be installed and active.', ASTRO_WOO_TEXT_DOMAIN ); ?></p>
	</div>
	<?php
}

/**
 * Autoload classes
 */
spl_autoload_register( function ( $class ) {
	$prefix   = 'Astro_Woo_';
	$base_dir = ASTRO_WOO_DIR . 'includes/';

	if ( strncmp( $prefix, $class, strlen( $prefix ) ) !== 0 ) {
		return;
	}

	$relative_class = substr( $class, strlen( $prefix ) );
	$file           = $base_dir . 'class-' . strtolower( str_replace( '_', '-', $relative_class ) ) . '.php';

	if ( file_exists( $file ) ) {
		require $file;
	}
} );

/**
 * Initialize plugin
 */
function astro_woo_init() {
	if ( ! astro_woo_check_woocommerce() ) {
		return;
	}

	// Load text domain
	load_plugin_textdomain( ASTRO_WOO_TEXT_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	// Initialize main plugin class
	Astro_Woo_Plugin::get_instance();
}
add_action( 'plugins_loaded', 'astro_woo_init' );

/**
 * Activation hook
 */
function astro_woo_activate() {
	// Create tables if needed
	Astro_Woo_Activator::activate();
}
register_activation_hook( __FILE__, 'astro_woo_activate' );

/**
 * Deactivation hook
 */
function astro_woo_deactivate() {
	Astro_Woo_Activator::deactivate();
}
register_deactivation_hook( __FILE__, 'astro_woo_deactivate' );

/**
 * Declare WooCommerce HPOS compatibility
 */
add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );
