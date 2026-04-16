<?php
/**
 * Core plugin class
 *
 * @package Astrology_WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Astro_Woo_Plugin {

	private static $instance = null;
	private $admin;
	private $product;
	private $cart;
	private $order;
	private $frontend;
	private $api;

	/**
	 * Get singleton instance
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->load_dependencies();
		$this->register_hooks();
	}

	/**
	 * Load required classes
	 */
	private function load_dependencies() {
		$this->admin    = new Astro_Woo_Admin();
		$this->product  = new Astro_Woo_Product();
		$this->cart     = new Astro_Woo_Cart();
		$this->order    = new Astro_Woo_Order();
		$this->frontend = new Astro_Woo_Frontend();
		$this->api      = new Astro_Woo_API();
	}

	/**
	 * Register hooks
	 */
	private function register_hooks() {
		// Admin hooks
		add_action( 'admin_menu', array( $this->admin, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this->admin, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this->admin, 'enqueue_admin_assets' ) );

		// Product hooks
		add_filter( 'product_type_options', array( $this->product, 'add_product_type_option' ) );
		add_action( 'woocommerce_product_options_general_product_data', array( $this->product, 'add_form_type_field' ) );
		add_action( 'woocommerce_process_product_meta', array( $this->product, 'save_form_type_field' ) );

		// Frontend hooks
		add_action( 'wp_enqueue_scripts', array( $this->frontend, 'enqueue_assets' ) );
		add_action( 'woocommerce_before_add_to_cart_button', array( $this->frontend, 'display_form' ) );
		
		// Cart hooks
		add_filter( 'woocommerce_add_cart_item_data', array( $this->cart, 'add_cart_item_data' ), 10, 3 );
		add_filter( 'woocommerce_get_item_data', array( $this->cart, 'display_cart_item_data' ), 10, 2 );
		add_action( 'woocommerce_checkout_create_order_line_item', array( $this->cart, 'add_order_item_meta' ), 10, 4 );

		// AJAX hooks
		add_action( 'wp_ajax_astro_validate_form', array( $this->frontend, 'ajax_validate_form' ) );
		add_action( 'wp_ajax_nopriv_astro_validate_form', array( $this->frontend, 'ajax_validate_form' ) );
		add_action( 'wp_ajax_astro_send_report_email', array( $this->order, 'ajax_send_report_email' ) );
		add_action( 'wp_ajax_nopriv_astro_send_report_email', array( $this->order, 'ajax_send_report_email' ) );

		// Order hooks
		add_action( 'woocommerce_order_status_completed', array( $this->order, 'generate_reports' ) );
		
		// Frontend display (My Account → Orders → View Order)
		add_action( 'woocommerce_order_details_after_order_table', array( $this->order, 'display_reports' ) );
		
		// Admin display (WooCommerce → Orders → Edit Order) - HPOS compatible
		add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this->order, 'display_reports_admin' ), 10, 1 );
		
		// Email hooks
		add_action( 'woocommerce_email_after_order_table', array( $this->order, 'add_reports_to_email' ), 10, 4 );

		// Admin order actions
		add_action( 'woocommerce_order_actions', array( $this->order, 'add_regenerate_action' ) );
		add_action( 'woocommerce_order_action_astro_regenerate_reports', array( $this->order, 'regenerate_reports_action' ) );

		// My Account endpoint
		add_action( 'init', array( $this->frontend, 'add_account_endpoint' ) );
		add_filter( 'woocommerce_account_menu_items', array( $this->frontend, 'add_account_menu_item' ) );
		add_action( 'woocommerce_account_astrology-reports_endpoint', array( $this->frontend, 'display_account_reports' ) );
	}
}
