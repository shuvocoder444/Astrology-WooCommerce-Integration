=== Astrology WooCommerce Integration ===
Contributors: astrologyservices
Tags: woocommerce, astrology, birth chart, horoscope, api
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Complete WooCommerce integration for astrology services with dynamic forms, API integration, and automated report generation.

== Description ==

Transform your WooCommerce store into a complete astrology service platform. This plugin allows you to sell astrology reports, birth charts, compatibility readings, and more with automated API integration.

= Key Features =

* **5 Form Types**: Birth Chart, Love Compatibility, Numerology, Tarot Reading, Zodiac Horoscope
* **Dynamic Product Forms**: Each product gets its own custom form
* **Automatic API Integration**: Connects to astrology API services automatically
* **Order Completion Reports**: Reports generated when order is completed
* **Email Integration**: Send reports via email to customers
* **My Account Display**: Customers can view reports in their account
* **Admin Controls**: Regenerate reports, debug mode, full settings control
* **Professional UI**: Clean, responsive forms with validation

= Supported Services =

1. **Birth Chart** (single_person) - Individual natal chart analysis
2. **Love Compatibility** (two_person) - Relationship compatibility reports
3. **Numerology** (numerology) - Name and date based numerology
4. **Tarot Reading** (tarot) - Card spreads and predictions
5. **Zodiac Horoscope** (zodiac) - Daily/Weekly/Monthly horoscopes

= How It Works =

1. Create WooCommerce products for each astrology service
2. Select the form type in product settings
3. Customer fills form on product page
4. Form data saved to cart and order
5. API called automatically on order completion
6. Report saved and displayed in My Account
7. Optional email delivery to customer

= API Support =

Compatible with astrologyapi.com and similar JSON-based astrology APIs.

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/astrology-woocommerce/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to WooCommerce > Astrology API
4. Enter your API credentials
5. Create products and select form types
6. Start selling!

= Requirements =

* WordPress 5.8 or higher
* WooCommerce 5.0 or higher
* PHP 7.4 or higher
* Valid astrology API credentials

== Frequently Asked Questions ==

= Does this work with any astrology API? =

The plugin is designed for JSON-based REST APIs with Basic Authentication. It's pre-configured for astrologyapi.com but can be adapted for similar APIs.

= Can I customize the forms? =

Yes! Form templates are located in the `/templates/` directory and can be overridden in your theme.

= What happens if the API fails? =

The plugin includes error handling. Failed reports are logged and can be regenerated from the order edit page.

= Can customers download reports? =

Yes, reports are displayed in My Account and can be printed. They're also sent via email if enabled.

= Is the plugin translation ready? =

Yes, the plugin uses WordPress localization and is ready for translation.

== Screenshots ==

1. Admin settings page with API configuration
2. Product edit page with form type selector
3. Frontend birth chart form
4. Love compatibility form with two person fields
5. Order completion with generated reports
6. My Account reports display
7. Email notification with report

== Changelog ==

= 1.0.0 =
* Initial release
* Support for 5 form types
* API integration with automatic report generation
* Email delivery system
* My Account integration
* Admin regenerate function
* Debug mode
* Full documentation

== Upgrade Notice ==

= 1.0.0 =
Initial release of Astrology WooCommerce Integration plugin.

== API Endpoints ==

The plugin maps form types to the following API endpoints:

* single_person → `/natal_chart`
* two_person → `/match_making_report`
* numerology → `/numerology_report`
* tarot → `/tarot_card_prediction`
* zodiac → `/horoscope_report`

== Support ==

For support, feature requests, or bug reports, please contact us or visit our documentation.

== Credits ==

Developed with ❤️ for the astrology community.
