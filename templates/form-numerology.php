<?php
/**
 * Numerology Form Template
 *
 * @package Astrology_WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="astro-woo-form-wrapper" data-form-type="numerology">
	<?php if ( ! empty( $form_description ) ) : ?>
		<div class="astro-form-description">
			<?php echo wp_kses_post( wpautop( $form_description ) ); ?>
		</div>
	<?php endif; ?>

	<div class="astro-form-container">
		<h3 class="astro-form-title"><?php esc_html_e( 'Numerology Details', ASTRO_WOO_TEXT_DOMAIN ); ?></h3>

		<div class="astro-form-row">
			<div class="astro-form-field">
				<label for="astro_full_name">
					<?php esc_html_e( 'Full Name', ASTRO_WOO_TEXT_DOMAIN ); ?>
					<span class="required">*</span>
				</label>
				<input type="text" 
					   id="astro_full_name" 
					   name="astro_full_name" 
					   class="astro-input" 
					   required 
					   placeholder="<?php esc_attr_e( 'Enter your complete name as per birth certificate', ASTRO_WOO_TEXT_DOMAIN ); ?>" />
				<span class="astro-error" id="astro_full_name_error"></span>
			</div>
		</div>

		<div class="astro-form-row">
			<div class="astro-form-field">
				<label for="astro_birth_date">
					<?php esc_html_e( 'Date of Birth', ASTRO_WOO_TEXT_DOMAIN ); ?>
					<small class="optional"><?php esc_html_e( '(Optional)', ASTRO_WOO_TEXT_DOMAIN ); ?></small>
				</label>
				<input type="date" 
					   id="astro_birth_date" 
					   name="astro_birth_date" 
					   class="astro-input" 
					   max="<?php echo esc_attr( gmdate( 'Y-m-d' ) ); ?>" />
				<small><?php esc_html_e( 'Include for more accurate numerology reading', ASTRO_WOO_TEXT_DOMAIN ); ?></small>
			</div>
		</div>

		<div class="astro-form-notice">
			<p><?php esc_html_e( 'Your name is analyzed to reveal your life path, destiny number, and personality traits.', ASTRO_WOO_TEXT_DOMAIN ); ?></p>
		</div>
	</div>
</div>
