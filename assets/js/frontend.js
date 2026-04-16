/**
 * Frontend JavaScript for Astrology WooCommerce Plugin
 *
 * @package Astrology_WooCommerce
 */

(function($) {
    'use strict';

    var AstroWooFrontend = {
        
        init: function() {
            this.formValidation();
            this.addToCartValidation();
        },

        /**
         * Form field validation
         */
        formValidation: function() {
            var self = this;

            // Real-time validation
            $('.astro-woo-form-wrapper input, .astro-woo-form-wrapper select, .astro-woo-form-wrapper textarea').on('blur change', function() {
                self.validateField($(this));
            });

            // Clear errors on focus
            $('.astro-woo-form-wrapper input, .astro-woo-form-wrapper select, .astro-woo-form-wrapper textarea').on('focus', function() {
                var fieldId = $(this).attr('id');
                $('#' + fieldId + '_error').text('').hide();
                $(this).removeClass('error');
            });
        },

        /**
         * Validate individual field
         */
        validateField: function($field) {
            var isValid = true;
            var fieldId = $field.attr('id');
            var errorSpan = $('#' + fieldId + '_error');
            var value = $field.val().trim();

            // Clear previous error
            errorSpan.text('').hide();
            $field.removeClass('error');

            // Check if required
            if ($field.prop('required') && !value) {
                errorSpan.text(astroWoo.i18n.required).show();
                $field.addClass('error');
                return false;
            }

            // Date validation
            if ($field.attr('type') === 'date' && value) {
                var selectedDate = new Date(value);
                var today = new Date();
                today.setHours(0, 0, 0, 0);

                if (selectedDate > today) {
                    errorSpan.text('Date cannot be in the future').show();
                    $field.addClass('error');
                    return false;
                }
            }

            // Email validation
            if ($field.attr('type') === 'email' && value) {
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    errorSpan.text('Invalid email format').show();
                    $field.addClass('error');
                    return false;
                }
            }

            return true;
        },

        /**
         * Validate entire form
         */
        validateForm: function($wrapper) {
            var self = this;
            var isValid = true;

            $wrapper.find('input[required], select[required], textarea[required]').each(function() {
                if (!self.validateField($(this))) {
                    isValid = false;
                }
            });

            return isValid;
        },

        /**
         * Add to cart validation
         */
        addToCartValidation: function() {
            var self = this;

            // Intercept add to cart button
            $('form.cart').on('submit', function(e) {
                var $form = $(this);
                var $wrapper = $('.astro-woo-form-wrapper');

                // Check if this product has astrology form
                if ($wrapper.length === 0) {
                    return true; // No form, proceed normally
                }

                // Validate the form
                if (!self.validateForm($wrapper)) {
                    e.preventDefault();
                    
                    // Scroll to first error
                    var $firstError = $wrapper.find('.error').first();
                    if ($firstError.length) {
                        $('html, body').animate({
                            scrollTop: $firstError.offset().top - 100
                        }, 500);
                    }

                    // Show alert
                    self.showNotice('error', astroWoo.i18n.fillForm);
                    return false;
                }

                // If validation passes, form will submit normally
                return true;
            });
        },

        /**
         * Show notice message
         */
        showNotice: function(type, message) {
            var noticeClass = type === 'error' ? 'woocommerce-error' : 'woocommerce-message';
            var icon = type === 'error' ? '✗' : '✓';
            
            var $notice = $('<div class="' + noticeClass + ' astro-notice" role="alert">' + 
                            icon + ' ' + message + 
                            '</div>');

            // Remove existing notices
            $('.astro-notice').remove();

            // Add new notice
            $('.astro-woo-form-wrapper').before($notice);

            // Scroll to notice
            $('html, body').animate({
                scrollTop: $notice.offset().top - 100
            }, 300);

            // Auto-hide after 5 seconds
            setTimeout(function() {
                $notice.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        },

        /**
         * Get form data
         */
        getFormData: function($wrapper) {
            var formType = $wrapper.data('form-type');
            var data = {
                form_type: formType
            };

            $wrapper.find('input, select, textarea').each(function() {
                var $field = $(this);
                var name = $field.attr('name');
                var value = $field.val();

                if (name && value) {
                    data[name] = value;
                }
            });

            return data;
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        AstroWooFrontend.init();
    });

})(jQuery);
