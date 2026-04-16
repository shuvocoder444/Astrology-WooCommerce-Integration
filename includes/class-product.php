<?php
/**
 * Product field management
 *
 * @package Astrology_WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Astro_Woo_Product {

	/**
	 * Add custom product type option
	 */
	public function add_product_type_option( $options ) {
		$options['astrology_service'] = array(
			'id'            => '_astrology_service',
			'wrapper_class' => 'show_if_simple',
			'label'         => __( 'Astrology Service', ASTRO_WOO_TEXT_DOMAIN ),
			'description'   => __( 'Enable astrology form for this product', ASTRO_WOO_TEXT_DOMAIN ),
			'default'       => 'no',
		);
		return $options;
	}

	/**
	 * Add form type field to product
	 */
	public function add_form_type_field() {
		global $post;

		echo '<div class="options_group astro-woo-options">';

		woocommerce_wp_select(
			array(
				'id'          => '_astro_form_type',
				'label'       => __( 'Astrology Form Type', ASTRO_WOO_TEXT_DOMAIN ),
				'description' => __( 'Select the type of form to display on product page', ASTRO_WOO_TEXT_DOMAIN ),
				'desc_tip'    => true,
				'options'     => array(
					''              => __( 'None (Regular Product)', ASTRO_WOO_TEXT_DOMAIN ),
					'single_person' => __( 'Birth Chart (Single Person)', ASTRO_WOO_TEXT_DOMAIN ),
					'two_person'    => __( 'Love Compatibility (Two People)', ASTRO_WOO_TEXT_DOMAIN ),
					'numerology'    => __( 'Numerology Report', ASTRO_WOO_TEXT_DOMAIN ),
					'tarot'         => __( 'Tarot Card Reading', ASTRO_WOO_TEXT_DOMAIN ),
					'zodiac'        => __( 'Zodiac Horoscope', ASTRO_WOO_TEXT_DOMAIN ),
				),
			)
		);

		woocommerce_wp_textarea_input(
			array(
				'id'          => '_astro_form_description',
				'label'       => __( 'Form Instructions', ASTRO_WOO_TEXT_DOMAIN ),
				'description' => __( 'Optional instructions to display above the form', ASTRO_WOO_TEXT_DOMAIN ),
				'desc_tip'    => true,
				'placeholder' => __( 'Please fill in your birth details accurately...', ASTRO_WOO_TEXT_DOMAIN ),
			)
		);

		echo '</div>';
	}

	/**
	 * Save form type field
	 */
	public function save_form_type_field( $post_id ) {
		$form_type = isset( $_POST['_astro_form_type'] ) 
			? sanitize_text_field( $_POST['_astro_form_type'] ) 
			: '';
		
		update_post_meta( $post_id, '_astro_form_type', $form_type );

		$form_desc = isset( $_POST['_astro_form_description'] ) 
			? sanitize_textarea_field( $_POST['_astro_form_description'] ) 
			: '';
		
		update_post_meta( $post_id, '_astro_form_description', $form_desc );

		// Also save the checkbox option
		$astrology_service = isset( $_POST['_astrology_service'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_astrology_service', $astrology_service );
	}
}
