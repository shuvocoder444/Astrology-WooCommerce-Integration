<?php
/**
 * Zodiac Form Template
 *
 * @package Astrology_WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="astro-woo-form-wrapper" data-form-type="zodiac">
	<?php if ( ! empty( $form_description ) ) : ?>
		<div class="astro-form-description">
			<?php echo wp_kses_post( wpautop( $form_description ) ); ?>
		</div>
	<?php endif; ?>

	<div class="astro-form-container">
		<h3 class="astro-form-title"><?php esc_html_e( 'Zodiac Horoscope Details', ASTRO_WOO_TEXT_DOMAIN ); ?></h3>

		<div class="astro-form-row">
			<div class="astro-form-field">
				<label for="astro_zodiac_sign">
					<?php esc_html_e( 'Your Zodiac Sign', ASTRO_WOO_TEXT_DOMAIN ); ?>
					<span class="required">*</span>
				</label>
				<select id="astro_zodiac_sign" 
						name="astro_zodiac_sign" 
						class="astro-select" 
						required>
					<option value=""><?php esc_html_e( 'Select your zodiac sign...', ASTRO_WOO_TEXT_DOMAIN ); ?></option>
					<option value="aries">♈ <?php esc_html_e( 'Aries (Mar 21 - Apr 19)', ASTRO_WOO_TEXT_DOMAIN ); ?></option>
					<option value="taurus">♉ <?php esc_html_e( 'Taurus (Apr 20 - May 20)', ASTRO_WOO_TEXT_DOMAIN ); ?></option>
					<option value="gemini">♊ <?php esc_html_e( 'Gemini (May 21 - Jun 20)', ASTRO_WOO_TEXT_DOMAIN ); ?></option>
					<option value="cancer">♋ <?php esc_html_e( 'Cancer (Jun 21 - Jul 22)', ASTRO_WOO_TEXT_DOMAIN ); ?></option>
					<option value="leo">♌ <?php esc_html_e( 'Leo (Jul 23 - Aug 22)', ASTRO_WOO_TEXT_DOMAIN ); ?></option>
					<option value="virgo">♍ <?php esc_html_e( 'Virgo (Aug 23 - Sep 22)', ASTRO_WOO_TEXT_DOMAIN ); ?></option>
					<option value="libra">♎ <?php esc_html_e( 'Libra (Sep 23 - Oct 22)', ASTRO_WOO_TEXT_DOMAIN ); ?></option>
					<option value="scorpio">♏ <?php esc_html_e( 'Scorpio (Oct 23 - Nov 21)', ASTRO_WOO_TEXT_DOMAIN ); ?></option>
					<option value="sagittarius">♐ <?php esc_html_e( 'Sagittarius (Nov 22 - Dec 21)', ASTRO_WOO_TEXT_DOMAIN ); ?></option>
					<option value="capricorn">♑ <?php esc_html_e( 'Capricorn (Dec 22 - Jan 19)', ASTRO_WOO_TEXT_DOMAIN ); ?></option>
					<option value="aquarius">♒ <?php esc_html_e( 'Aquarius (Jan 20 - Feb 18)', ASTRO_WOO_TEXT_DOMAIN ); ?></option>
					<option value="pisces">♓ <?php esc_html_e( 'Pisces (Feb 19 - Mar 20)', ASTRO_WOO_TEXT_DOMAIN ); ?></option>
				</select>
				<span class="astro-error" id="astro_zodiac_sign_error"></span>
			</div>
		</div>

		<div class="astro-form-row">
			<div class="astro-form-field">
				<label for="astro_report_type">
					<?php esc_html_e( 'Report Type', ASTRO_WOO_TEXT_DOMAIN ); ?>
					<span class="required">*</span>
				</label>
				<select id="astro_report_type" 
						name="astro_report_type" 
						class="astro-select" 
						required>
					<option value="daily"><?php esc_html_e( 'Daily Horoscope', ASTRO_WOO_TEXT_DOMAIN ); ?></option>
					<option value="weekly"><?php esc_html_e( 'Weekly Horoscope', ASTRO_WOO_TEXT_DOMAIN ); ?></option>
					<option value="monthly"><?php esc_html_e( 'Monthly Horoscope', ASTRO_WOO_TEXT_DOMAIN ); ?></option>
					<option value="yearly"><?php esc_html_e( 'Yearly Horoscope', ASTRO_WOO_TEXT_DOMAIN ); ?></option>
				</select>
				<span class="astro-error" id="astro_report_type_error"></span>
			</div>
		</div>

		<div class="astro-form-notice">
			<p><?php esc_html_e( '🌟 Get personalized predictions based on your zodiac sign.', ASTRO_WOO_TEXT_DOMAIN ); ?></p>
		</div>
	</div>
</div>
