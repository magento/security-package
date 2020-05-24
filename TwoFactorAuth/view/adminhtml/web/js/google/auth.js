/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_TwoFactorAuth/js/error'
], function ($, ko, Component, error) {
    'use strict';

    return Component.extend({
        currentStep: ko.observable('register'),
        waitText: ko.observable(''),
        verifyCode: ko.observable(''),
        defaults: {
            template: 'Magento_TwoFactorAuth/google/auth'
        },

        qrCodeUrl: '',
        postUrl: '',
        successUrl: '',
        secretCode: '',

        /**
         * Get QR code URL
         * @returns {String}
         */
        getQrCodeUrl: function () {
            return this.qrCodeUrl;
        },

        /**
         * Get POST URL
         * @returns {String}
         */
        getPostUrl: function () {
            return this.postUrl;
        },

        /**
         * Get plain Secret Code
         * @returns {String}
         */
        getSecretCode: function () {
            return this.secretCode;
        },

        /**
         * Go to next step
         */
        nextStep: function () {
            this.currentStep('login');
            self.location.href = this.successUrl;
        },

        /**
         * Verify auth code
         */
        doVerify: function () {
            var me = this;

            this.waitText('Please wait...');
            $.post(this.getPostUrl(), {
                'tfa_code': this.verifyCode()
            })
                .done(function (res) {
                    if (res.success) {
                        me.nextStep();
                    } else {
                        error.display(res.message);
                        me.verifyCode('');
                    }
                    me.waitText('');
                })
                .fail(function () {
                    error.display('There was an internal error trying to verify your code');
                    me.waitText('');
                });
        }
    });
});
