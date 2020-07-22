/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko',
    'uiComponent',
    'Magento_TwoFactorAuth/js/duo/api'
], function (ko, Component, duo) {
    'use strict';

    return Component.extend({
        currentStep: ko.observable('register'),

        defaults: {
            template: 'Magento_TwoFactorAuth/duo/auth'
        },

        signature: '',
        apiHost: '',
        postUrl: '',
        authenticateData: {},

        /**
         * Start waiting loop
         */
        onAfterRender: function () {
            window.setTimeout(function () {
                duo.init();
            }, 100);
        },

        /**
         * Get POST URL
         * @returns {String}
         */
        getPostUrl: function () {
            return this.postUrl;
        },

        /**
         * Get signature
         * @returns {String}
         */
        getSignature: function () {
            return this.signature;
        },

        /**
         * Get API host
         * @returns {String}
         */
        getApiHost: function () {
            return this.apiHost;
        }
    });
});
