/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'Magento_ReCaptchaWebapiUi/js/webapiReCaptcha',
        'jquery'
    ],
    function (Component, $) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Magento_ReCaptchaCheckout/reCaptcha',
                skipPayments: []
            },

            /**
             * Render reCAPTCHA
             *
             * @param {Object} method
             */
            renderReCaptchaFor: function (method) {
                var reCaptcha;

                if (this.isCheckoutReCaptchaRequiredFor(method)) {
                    reCaptcha = $.extend(true, {}, this, {reCaptchaId: this.getReCaptchaIdFor(method)});
                    reCaptcha.renderReCaptcha();
                }
            },

            /**
             * Get reCAPTCHA ID
             *
             * @param {Object} method
             * @returns {String}
             */
            getReCaptchaIdFor: function (method) {
                return this.getReCaptchaId() + '-' + method.getCode();
            },

            /**
             * Check whether checkout reCAPTCHA is required for payment method
             *
             * @param {Object} method
             * @returns {Boolean}
             */
            isCheckoutReCaptchaRequiredFor: function (method) {
                return !this.skipPayments || !this.skipPayments.hasOwnProperty(method.getCode());
            }
        });
    }
);
