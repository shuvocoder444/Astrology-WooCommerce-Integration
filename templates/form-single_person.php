<?php
/**
 * Single Person Form Template (Birth Chart)
 *
 * @package Astrology_WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="astro-woo-form-wrapper" data-form-type="single_person">
	<?php if ( ! empty( $form_description ) ) : ?>
		<div class="astro-form-description">
			<?php echo wp_kses_post( wpautop( $form_description ) ); ?>
		</div>
	<?php endif; ?>

	<div class="astro-form-container">
		<h3 class="astro-form-title"><?php esc_html_e( 'Birth Chart Details', ASTRO_WOO_TEXT_DOMAIN ); ?></h3>

		<div class="astro-form-row">
			<div class="astro-form-field">
				<label for="astro_name">
					<?php esc_html_e( 'Full Name', ASTRO_WOO_TEXT_DOMAIN ); ?>
					<span class="required">*</span>
				</label>
				<input type="text" 
					   id="astro_name" 
					   name="astro_name" 
					   class="astro-input" 
					   required 
					   placeholder="<?php esc_attr_e( 'Enter your full name', ASTRO_WOO_TEXT_DOMAIN ); ?>" />
				<span class="astro-error" id="astro_name_error"></span>
			</div>
		</div>

		<div class="astro-form-row astro-form-row-2col">
			<div class="astro-form-field">
				<label for="astro_birth_date">
					<?php esc_html_e( 'Date of Birth', ASTRO_WOO_TEXT_DOMAIN ); ?>
					<span class="required">*</span>
				</label>
				<input type="date" 
					   id="astro_birth_date" 
					   name="astro_birth_date" 
					   class="astro-input" 
					   required 
					   max="<?php echo esc_attr( gmdate( 'Y-m-d' ) ); ?>" />
				<span class="astro-error" id="astro_birth_date_error"></span>
			</div>

			<div class="astro-form-field">
				<label for="astro_birth_time">
					<?php esc_html_e( 'Time of Birth', ASTRO_WOO_TEXT_DOMAIN ); ?>
					<span class="required">*</span>
				</label>
				<input type="time" 
					   id="astro_birth_time" 
					   name="astro_birth_time" 
					   class="astro-input" 
					   required />
				<span class="astro-error" id="astro_birth_time_error"></span>
			</div>
		</div>

		<div class="astro-form-row">
			<div class="astro-form-field">
				<label for="astro_birth_place">
					<?php esc_html_e( 'Place of Birth', ASTRO_WOO_TEXT_DOMAIN ); ?>
					<span class="required">*</span>
				</label>
				<input type="text" 
					   id="astro_birth_place" 
					   name="astro_birth_place" 
					   class="astro-input" 
					   required 
					   placeholder="<?php esc_attr_e( 'City, Country', ASTRO_WOO_TEXT_DOMAIN ); ?>" />
				<span class="astro-error" id="astro_birth_place_error"></span>
			</div>
		</div>

		<div class="astro-form-row astro-form-row-3col">
			<div class="astro-form-field">
				<label for="astro_latitude">
					<?php esc_html_e( 'Latitude', ASTRO_WOO_TEXT_DOMAIN ); ?>
				</label>
				<input type="number" 
					   id="astro_latitude" 
					   name="astro_latitude" 
					   class="astro-input" 
					   step="0.0001" 
					   placeholder="23.8103" 
					   value="23.8103" />
				<small><?php esc_html_e( 'Optional - defaults to Dhaka', ASTRO_WOO_TEXT_DOMAIN ); ?></small>
			</div>

			<div class="astro-form-field">
				<label for="astro_longitude">
					<?php esc_html_e( 'Longitude', ASTRO_WOO_TEXT_DOMAIN ); ?>
				</label>
				<input type="number" 
					   id="astro_longitude" 
					   name="astro_longitude" 
					   class="astro-input" 
					   step="0.0001" 
					   placeholder="90.4125" 
					   value="90.4125" />
				<small><?php esc_html_e( 'Optional - defaults to Dhaka', ASTRO_WOO_TEXT_DOMAIN ); ?></small>
			</div>

			<div class="astro-form-field">
				<label for="astro_timezone">
					<?php esc_html_e( 'Timezone', ASTRO_WOO_TEXT_DOMAIN ); ?>
				</label>
				<input type="number" 
					   id="astro_timezone" 
					   name="astro_timezone" 
					   class="astro-input" 
					   step="0.5" 
					   placeholder="6" 
					   value="6" />
				<small><?php esc_html_e( 'UTC offset (e.g., 6 for Bangladesh)', ASTRO_WOO_TEXT_DOMAIN ); ?></small>
			</div>
		</div>

		<div class="astro-form-notice">
			<p><?php esc_html_e( '* Please ensure all birth details are accurate for the best results.', ASTRO_WOO_TEXT_DOMAIN ); ?></p>
		</div>
	</div>
</div>
