/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'ko'
], function (Component, ko) {
    'use strict';

    return Component.extend({
        showChangeMethod: ko.observable(false),

        providers: [],
        switchIcon: '',

        defaults: {
            template: 'Magento_TwoFactorAuth/change_provider'
        },

        /**
         * Get switch icon URL
         * @returns {String}
         */
        getSwitchIconUrl: function () {
            return this.switchIcon;
        },

        /**
         * Show available alternative 2FA providers
         */
        displayChangeMethod: function () {
            this.showChangeMethod(true);
        },

        /**
         * Return a list of alternative providers
         * @returns {Array}
         */
        getProviders: function () {
            return this.providers;
        }
    });
});
