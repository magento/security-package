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

        var reCaptchaIds = new WeakMap(),
            uuid = 0;

        return Component.extend({
            defaults: {
                template: 'Magento_ReCaptchaCheckout/reCaptcha',
                skipPayments: [] // List of payment methods that do not require this reCaptcha
            },

            /**
             * Render reCAPTCHA for payment method
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
             * Get reCAPTCHA ID for payment method
             *
             * @param {Object} method
             * @returns {String}
             */
            getReCaptchaIdFor: function (method) {
                if (!reCaptchaIds.has(method)) {
                    reCaptchaIds.set(method, this.getReCaptchaId() + '-' + uuid++);
                }
                return reCaptchaIds.get(method);
            },

            /**
             * Check whether checkout reCAPTCHA is required for payment method
             *
             * @param {Object} method
             * @returns {Boolean}
             */
            isCheckoutReCaptchaRequiredFor: function (method) {
                return !this.skipPayments || !this.skipPayments.hasOwnProperty(method.getCode());
            },

            /**
             * @inheritdoc
             */
            initCaptcha: function () {
                var $wrapper,
                    $recaptchaResponseInput;

                this._super();
                // Since there will be multiple reCaptcha in the payment form,
                // they may override each other if the form data is serialized and submitted.
                // Instead, the reCaptcha response will be collected in the callback: reCaptchaCallback()
                // and sent in the request header X-ReCaptcha
                $wrapper = $('#' + this.getReCaptchaId() + '-wrapper');
                $recaptchaResponseInput = $wrapper.find('[name=g-recaptcha-response]');
                if ($recaptchaResponseInput.length) {
                    $recaptchaResponseInput.prop('disabled', true);
                }
            }
        });
    }
);
