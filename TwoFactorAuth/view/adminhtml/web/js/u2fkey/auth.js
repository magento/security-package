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
            template: 'Magento_TwoFactorAuth/u2fkey/auth'
        },

        postUrl: '',
        successUrl: '',
        touchImageUrl: '',
        authenticateData: {},

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
            var requestData = this.authenticateData,
                me = this;

            // eslint-disable-next-line no-undef
            u2f.sign(
                requestData,
                function (signResponse) {
                    $.post(me.getPostUrl(), {
                        'request': requestData,
                        'response': signResponse
                    }).done(function (res) {
                        if (res.success) {
                            me.currentStep('login');
                            self.location.href = me.getSuccessUrl();
                        } else {
                            me.waitForTouch();
                        }
                    }).fail(function () {
                        error.display('Invalid device');
                    });
                }, 120
            );
        }
    });
});
