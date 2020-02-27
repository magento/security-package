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
        configurePostUrl: '',
        countries: [],

        country: ko.observable(''),
        phone: ko.observable(''),
        method: ko.observable(''),

        waitText: ko.observable(''),

        defaults: {
            template: 'Magento_TwoFactorAuth/authy/configure/register'
        },

        /**
         * Get configure POST url
         * @returns {String}
         */
        getConfigurePostUrl: function () {
            return this.configurePostUrl;
        },

        /**
         * Get a list of available countries
         * @returns {Array}
         */
        getCountries: function () {
            return this.countries;
        },

        /**
         * Go to next step
         */
        nextStep: function () {
            registry.currentStep('verify');
            window.setTimeout(function () {
                registry.currentStep('register');
            }, registry.secondsToExpire() * 1000);
        },

        /**
         * Start Authy registration procedure
         */
        doRegister: function () {
            var me = this;

            this.waitText('Please wait...');
            $.post(this.getConfigurePostUrl(), {
                'tfa_country': this.country(),
                'tfa_phone': this.phone(),
                'tfa_method': this.method()

            })
                .done(function (res) {
                    if (res.success) {
                        registry.messageText(res.message);
                        registry.secondsToExpire(res['seconds_to_expire']);
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
        }
    });
});
