/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_TwoFactorAuth/js/error',
    'Magento_TwoFactorAuth/js/authy/configure/registry',
    'mage/translate'
], function ($, ko, Component, error, registry) {
    'use strict';

    return Component.extend({
        verifyCode: ko.observable(''),
        messageText: registry.messageText,
        waitText: ko.observable(''),

        verifyPostUrl: '',
        successUrl: '',

        defaults: {
            template: 'Magento_TwoFactorAuth/authy/configure/verify'
        },

        /**
         * Get verification post URL
         * @returns {String}
         */
        getVerifyPostUrl: function () {
            return this.verifyPostUrl;
        },

        /**
         * Go to next step
         */
        nextStep: function () {
            registry.currentStep('login');
            self.location.href = this.successUrl;
        },

        /**
         * Verify auth code
         */
        doVerify: function () {
            var me = this;

            this.waitText('Please wait...');
            $.post(this.getVerifyPostUrl(), {
                'tfa_verify': this.verifyCode()
            })
                .done(function (res) {
                    if (res.success) {
                        me.nextStep();
                    } else {
                        error.display(res.message);
                    }
                    me.waitText('');
                })
                .fail(function () {
                    error.display('There was an internal error trying to verify your code');
                    me.waitText('');
                });
        },

        /**
         * Go to previous step to change phone number
         */
        changePhoneNumber: function () {
            registry.currentStep('register');
        }
    });
});
