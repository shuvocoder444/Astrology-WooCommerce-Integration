<?php
/**
 * Cart data management
 *
 * @package Astrology_WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Astro_Woo_Cart {

	/**
	 * Add custom data to cart item
	 */
	public function add_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
		$form_type = get_post_meta( $product_id, '_astro_form_type', true );

		if ( empty( $form_type ) ) {
			return $cart_item_data;
		}

		// Get form data from POST
		$form_data = $this->get_form_data_from_post( $form_type );

		if ( ! empty( $form_data ) ) {
			$cart_item_data['astro_form_type'] = $form_type;
			$cart_item_data['astro_form_data'] = $form_data;
			
			// Make each cart item unique
			$cart_item_data['unique_key'] = md5( microtime() . wp_rand() );
		}

		return $cart_item_data;
	}

	/**
	 * Display cart item data
	 */
	public function display_cart_item_data( $item_data, $cart_item ) {
		if ( ! isset( $cart_item['astro_form_type'] ) || ! isset( $cart_item['astro_form_data'] ) ) {
			return $item_data;
		}

		$form_type = $cart_item['astro_form_type'];
		$form_data = $cart_item['astro_form_data'];

		$item_data[] = array(
			'key'   => __( 'Service Type', ASTRO_WOO_TEXT_DOMAIN ),
			'value' => $this->get_form_type_label( $form_type ),
		);

		// Add relevant fields to display
		$display_data = $this->get_display_fields( $form_type, $form_data );

		foreach ( $display_data as $key => $value ) {
			$item_data[] = array(
				'key'   => $key,
				'value' => $value,
			);
		}

		return $item_data;
	}

	/**
	 * Add data to order item meta
	 */
	public function add_order_item_meta( $item, $cart_item_key, $values, $order ) {
		if ( isset( $values['astro_form_type'] ) ) {
			$item->add_meta_data( '_astro_form_type', $values['astro_form_type'] );
		}

		if ( isset( $values['astro_form_data'] ) ) {
			$item->add_meta_data( '_astro_form_data', $values['astro_form_data'] );
		}
	}

	/**
	 * Get form data from POST
	 */
	private function get_form_data_from_post( $form_type ) {
		$data = array();

		switch ( $form_type ) {
			case 'single_person':
				$data = array(
					'name'        => isset( $_POST['astro_name'] ) ? sanitize_text_field( $_POST['astro_name'] ) : '',
					'birth_date'  => isset( $_POST['astro_birth_date'] ) ? sanitize_text_field( $_POST['astro_birth_date'] ) : '',
					'birth_time'  => isset( $_POST['astro_birth_time'] ) ? sanitize_text_field( $_POST['astro_birth_time'] ) : '',
					'birth_place' => isset( $_POST['astro_birth_place'] ) ? sanitize_text_field( $_POST['astro_birth_place'] ) : '',
					'latitude'    => isset( $_POST['astro_latitude'] ) ? sanitize_text_field( $_POST['astro_latitude'] ) : '',
					'longitude'   => isset( $_POST['astro_longitude'] ) ? sanitize_text_field( $_POST['astro_longitude'] ) : '',
					'timezone'    => isset( $_POST['astro_timezone'] ) ? sanitize_text_field( $_POST['astro_timezone'] ) : '',
				);
				break;

			case 'two_person':
				$data = array(
					'person1_name'        => isset( $_POST['astro_person1_name'] ) ? sanitize_text_field( $_POST['astro_person1_name'] ) : '',
					'person1_birth_date'  => isset( $_POST['astro_person1_birth_date'] ) ? sanitize_text_field( $_POST['astro_person1_birth_date'] ) : '',
					'person1_birth_time'  => isset( $_POST['astro_person1_birth_time'] ) ? sanitize_text_field( $_POST['astro_person1_birth_time'] ) : '',
					'person1_birth_place' => isset( $_POST['astro_person1_birth_place'] ) ? sanitize_text_field( $_POST['astro_person1_birth_place'] ) : '',
					'person2_name'        => isset( $_POST['astro_person2_name'] ) ? sanitize_text_field( $_POST['astro_person2_name'] ) : '',
					'person2_birth_date'  => isset( $_POST['astro_person2_birth_date'] ) ? sanitize_text_field( $_POST['astro_person2_birth_date'] ) : '',
					'person2_birth_time'  => isset( $_POST['astro_person2_birth_time'] ) ? sanitize_text_field( $_POST['astro_person2_birth_time'] ) : '',
					'person2_birth_place' => isset( $_POST['astro_person2_birth_place'] ) ? sanitize_text_field( $_POST['astro_person2_birth_place'] ) : '',
				);
				break;

			case 'numerology':
				$data = array(
					'full_name'  => isset( $_POST['astro_full_name'] ) ? sanitize_text_field( $_POST['astro_full_name'] ) : '',
					'birth_date' => isset( $_POST['astro_birth_date'] ) ? sanitize_text_field( $_POST['astro_birth_date'] ) : '',
				);
				break;

			case 'tarot':
				$data = array(
					'question'     => isset( $_POST['astro_question'] ) ? sanitize_textarea_field( $_POST['astro_question'] ) : '',
					'card_spread'  => isset( $_POST['astro_card_spread'] ) ? sanitize_text_field( $_POST['astro_card_spread'] ) : '',
				);
				break;

			case 'zodiac':
				$data = array(
					'zodiac_sign' => isset( $_POST['astro_zodiac_sign'] ) ? sanitize_text_field( $_POST['astro_zodiac_sign'] ) : '',
					'report_type' => isset( $_POST['astro_report_type'] ) ? sanitize_text_field( $_POST['astro_report_type'] ) : 'daily',
				);
				break;
		}

		return $data;
	}

	/**
	 * Get form type label
	 */
	private function get_form_type_label( $form_type ) {
		$labels = array(
			'single_person' => __( 'Birth Chart', ASTRO_WOO_TEXT_DOMAIN ),
			'two_person'    => __( 'Love Compatibility', ASTRO_WOO_TEXT_DOMAIN ),
			'numerology'    => __( 'Numerology', ASTRO_WOO_TEXT_DOMAIN ),
			'tarot'         => __( 'Tarot Reading', ASTRO_WOO_TEXT_DOMAIN ),
			'zodiac'        => __( 'Zodiac Horoscope', ASTRO_WOO_TEXT_DOMAIN ),
		);

		return isset( $labels[ $form_type ] ) ? $labels[ $form_type ] : $form_type;
	}

	/**
	 * Get fields to display in cart
	 */
	private function get_display_fields( $form_type, $form_data ) {
		$display = array();

		switch ( $form_type ) {
			case 'single_person':
				$display = array(
					__( 'Name', ASTRO_WOO_TEXT_DOMAIN )        => $form_data['name'] ?? '',
					__( 'Birth Date', ASTRO_WOO_TEXT_DOMAIN )  => $form_data['birth_date'] ?? '',
					__( 'Birth Time', ASTRO_WOO_TEXT_DOMAIN )  => $form_data['birth_time'] ?? '',
					__( 'Birth Place', ASTRO_WOO_TEXT_DOMAIN ) => $form_data['birth_place'] ?? '',
				);
				break;

			case 'two_person':
				$display = array(
					__( 'Person 1', ASTRO_WOO_TEXT_DOMAIN ) => ( $form_data['person1_name'] ?? '' ) . ' - ' . ( $form_data['person1_birth_date'] ?? '' ),
					__( 'Person 2', ASTRO_WOO_TEXT_DOMAIN ) => ( $form_data['person2_name'] ?? '' ) . ' - ' . ( $form_data['person2_birth_date'] ?? '' ),
				);
				break;

			case 'numerology':
				$display = array(
					__( 'Full Name', ASTRO_WOO_TEXT_DOMAIN )  => $form_data['full_name'] ?? '',
					__( 'Birth Date', ASTRO_WOO_TEXT_DOMAIN ) => $form_data['birth_date'] ?? '',
				);
				break;

			case 'tarot':
				$display = array(
					__( 'Question', ASTRO_WOO_TEXT_DOMAIN )    => $form_data['question'] ?? '',
					__( 'Card Spread', ASTRO_WOO_TEXT_DOMAIN ) => $form_data['card_spread'] ?? '',
				);
				break;

			case 'zodiac':
				$display = array(
					__( 'Zodiac Sign', ASTRO_WOO_TEXT_DOMAIN ) => $form_data['zodiac_sign'] ?? '',
					__( 'Report Type', ASTRO_WOO_TEXT_DOMAIN ) => $form_data['report_type'] ?? 'daily',
				);
				break;
		}

		return array_filter( $display );
	}
}
