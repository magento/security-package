/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// jscs:disable jsDoc

/* global grecaptcha */
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
             */
            renderReCaptchaForPayment: function (method) {
                var reCaptcha;

                if (!this.skipPayments || !this.skipPayments.hasOwnProperty(method.getCode())) {
                    reCaptcha = $.extend({}, this);

                    reCaptcha.reCaptchaId = this.getPaymentReCaptchaId(method);
                    reCaptcha.renderReCaptcha();
                }
            },

            /**
             * Render reCAPTCHA
             */
            getPaymentReCaptchaId: function (method) {
                return this.getReCaptchaId() + '-' + method.getCode();
            }
        });
    }
);
