<?php
/**
 * Tarot Form Template
 *
 * @package Astrology_WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="astro-woo-form-wrapper" data-form-type="tarot">
	<?php if ( ! empty( $form_description ) ) : ?>
		<div class="astro-form-description">
			<?php echo wp_kses_post( wpautop( $form_description ) ); ?>
		</div>
	<?php endif; ?>

	<div class="astro-form-container">
		<h3 class="astro-form-title"><?php esc_html_e( 'Tarot Reading Details', ASTRO_WOO_TEXT_DOMAIN ); ?></h3>

		<div class="astro-form-row">
			<div class="astro-form-field">
				<label for="astro_question">
					<?php esc_html_e( 'Your Question', ASTRO_WOO_TEXT_DOMAIN ); ?>
					<span class="required">*</span>
				</label>
				<textarea id="astro_question" 
						  name="astro_question" 
						  class="astro-textarea" 
						  rows="4" 
						  required 
						  placeholder="<?php esc_attr_e( 'Ask a specific question about your life, relationships, career, etc.', ASTRO_WOO_TEXT_DOMAIN ); ?>"></textarea>
				<span class="astro-error" id="astro_question_error"></span>
				<small><?php esc_html_e( 'Be as specific as possible for the best guidance', ASTRO_WOO_TEXT_DOMAIN ); ?></small>
			</div>
		</div>

		<div class="astro-form-row">
			<div class="astro-form-field">
				<label for="astro_card_spread">
					<?php esc_html_e( 'Card Spread Type', ASTRO_WOO_TEXT_DOMAIN ); ?>
					<span class="required">*</span>
				</label>
				<select id="astro_card_spread" 
						name="astro_card_spread" 
						class="astro-select" 
						required>
					<option value=""><?php esc_html_e( 'Select a spread...', ASTRO_WOO_TEXT_DOMAIN ); ?></option>
					<option value="three_card"><?php esc_html_e( 'Three Card Spread (Past-Present-Future)', ASTRO_WOO_TEXT_DOMAIN ); ?></option>
					<option value="celtic_cross"><?php esc_html_e( 'Celtic Cross (10 cards - detailed)', ASTRO_WOO_TEXT_DOMAIN ); ?></option>
					<option value="single_card"><?php esc_html_e( 'Single Card (Quick answer)', ASTRO_WOO_TEXT_DOMAIN ); ?></option>
					<option value="relationship"><?php esc_html_e( 'Relationship Spread', ASTRO_WOO_TEXT_DOMAIN ); ?></option>
					<option value="career"><?php esc_html_e( 'Career & Success Spread', ASTRO_WOO_TEXT_DOMAIN ); ?></option>
				</select>
				<span class="astro-error" id="astro_card_spread_error"></span>
			</div>
		</div>

		<div class="astro-form-notice">
			<p><?php esc_html_e( '💫 Take a moment to focus on your question before submitting.', ASTRO_WOO_TEXT_DOMAIN ); ?></p>
		</div>
	</div>
</div>
