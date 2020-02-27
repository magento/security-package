/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_TwoFactorAuth/js/error',
    'Magento_TwoFactorAuth/js/u2fkey/api'
], function ($, ko, Component, error) {
    'use strict';

    return Component.extend({
        currentStep: ko.observable('register'),

        defaults: {
            template: 'Magento_TwoFactorAuth/u2fkey/configure'
        },

        postUrl: '',
        successUrl: '',
        touchImageUrl: '',
        registerData: {},

        /**
         * Start waiting loop
         */
        onAfterRender: function () {
            this.waitForTouch();
        },

        /**
         * Get touch image URL
         * @returns {String}
         */
        getTouchImageUrl: function () {
            return this.touchImageUrl;
        },

        /**
         * Get POST URL
         * @returns {String}
         */
        getPostUrl: function () {
            return this.postUrl;
        },

        /**
         * Get success URL
         * @returns {String}
         */
        getSuccessUrl: function () {
            return this.successUrl;
        },

        /**
         * Wait for key touch
         */
        waitForTouch: function () {
            var requestData = this.registerData[0],
                signs = this.registerData[1],
                me = this;

            // eslint-disable-next-line no-undef
            u2f.register(
                [requestData],
                signs,
                function (registerResponse) {
                    $.post(me.getPostUrl(), {
                        'request': requestData,
                        'response': registerResponse
                    }).done(function (res) {
                        if (res.success) {
                            me.currentStep('login');
                            self.location.href = me.getSuccessUrl();
                        } else {
                            me.waitForTouch();
                        }
                    }).fail(function () {
                        error.display('Unable to register your device');
                    });
                }, 120
            );
        }
    });
});
