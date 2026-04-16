<?php
/**
 * Two Person Form Template (Love Compatibility)
 *
 * @package Astrology_WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="astro-woo-form-wrapper" data-form-type="two_person">
	<?php if ( ! empty( $form_description ) ) : ?>
		<div class="astro-form-description">
			<?php echo wp_kses_post( wpautop( $form_description ) ); ?>
		</div>
	<?php endif; ?>

	<div class="astro-form-container">
		<h3 class="astro-form-title"><?php esc_html_e( 'Love Compatibility Details', ASTRO_WOO_TEXT_DOMAIN ); ?></h3>

		<!-- Person 1 -->
		<div class="astro-person-section">
			<h4 class="astro-section-title"><?php esc_html_e( 'Person 1', ASTRO_WOO_TEXT_DOMAIN ); ?></h4>

			<div class="astro-form-row">
				<div class="astro-form-field">
					<label for="astro_person1_name">
						<?php esc_html_e( 'Full Name', ASTRO_WOO_TEXT_DOMAIN ); ?>
						<span class="required">*</span>
					</label>
					<input type="text" 
						   id="astro_person1_name" 
						   name="astro_person1_name" 
						   class="astro-input" 
						   required />
				</div>
			</div>

			<div class="astro-form-row astro-form-row-2col">
				<div class="astro-form-field">
					<label for="astro_person1_birth_date">
						<?php esc_html_e( 'Date of Birth', ASTRO_WOO_TEXT_DOMAIN ); ?>
						<span class="required">*</span>
					</label>
					<input type="date" 
						   id="astro_person1_birth_date" 
						   name="astro_person1_birth_date" 
						   class="astro-input" 
						   required 
						   max="<?php echo esc_attr( gmdate( 'Y-m-d' ) ); ?>" />
				</div>

				<div class="astro-form-field">
					<label for="astro_person1_birth_time">
						<?php esc_html_e( 'Time of Birth', ASTRO_WOO_TEXT_DOMAIN ); ?>
						<span class="required">*</span>
					</label>
					<input type="time" 
						   id="astro_person1_birth_time" 
						   name="astro_person1_birth_time" 
						   class="astro-input" 
						   required />
				</div>
			</div>

			<div class="astro-form-row">
				<div class="astro-form-field">
					<label for="astro_person1_birth_place">
						<?php esc_html_e( 'Place of Birth', ASTRO_WOO_TEXT_DOMAIN ); ?>
						<span class="required">*</span>
					</label>
					<input type="text" 
						   id="astro_person1_birth_place" 
						   name="astro_person1_birth_place" 
						   class="astro-input" 
						   required 
						   placeholder="<?php esc_attr_e( 'City, Country', ASTRO_WOO_TEXT_DOMAIN ); ?>" />
				</div>
			</div>
		</div>

		<!-- Person 2 -->
		<div class="astro-person-section">
			<h4 class="astro-section-title"><?php esc_html_e( 'Person 2', ASTRO_WOO_TEXT_DOMAIN ); ?></h4>

			<div class="astro-form-row">
				<div class="astro-form-field">
					<label for="astro_person2_name">
						<?php esc_html_e( 'Full Name', ASTRO_WOO_TEXT_DOMAIN ); ?>
						<span class="required">*</span>
					</label>
					<input type="text" 
						   id="astro_person2_name" 
						   name="astro_person2_name" 
						   class="astro-input" 
						   required />
				</div>
			</div>

			<div class="astro-form-row astro-form-row-2col">
				<div class="astro-form-field">
					<label for="astro_person2_birth_date">
						<?php esc_html_e( 'Date of Birth', ASTRO_WOO_TEXT_DOMAIN ); ?>
						<span class="required">*</span>
					</label>
					<input type="date" 
						   id="astro_person2_birth_date" 
						   name="astro_person2_birth_date" 
						   class="astro-input" 
						   required 
						   max="<?php echo esc_attr( gmdate( 'Y-m-d' ) ); ?>" />
				</div>

				<div class="astro-form-field">
					<label for="astro_person2_birth_time">
						<?php esc_html_e( 'Time of Birth', ASTRO_WOO_TEXT_DOMAIN ); ?>
						<span class="required">*</span>
					</label>
					<input type="time" 
						   id="astro_person2_birth_time" 
						   name="astro_person2_birth_time" 
						   class="astro-input" 
						   required />
				</div>
			</div>

			<div class="astro-form-row">
				<div class="astro-form-field">
					<label for="astro_person2_birth_place">
						<?php esc_html_e( 'Place of Birth', ASTRO_WOO_TEXT_DOMAIN ); ?>
						<span class="required">*</span>
					</label>
					<input type="text" 
						   id="astro_person2_birth_place" 
						   name="astro_person2_birth_place" 
						   class="astro-input" 
						   required 
						   placeholder="<?php esc_attr_e( 'City, Country', ASTRO_WOO_TEXT_DOMAIN ); ?>" />
				</div>
			</div>
		</div>

		<div class="astro-form-notice">
			<p><?php esc_html_e( '* Please ensure all birth details are accurate for both people.', ASTRO_WOO_TEXT_DOMAIN ); ?></p>
		</div>
	</div>
</div>
