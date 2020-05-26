/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define(['Magento_Ui/js/form/element/abstract'], function (Abstract) {
    'use strict';

    return Abstract.extend({
        /**
         * Get a list of forced providers
         * @returns {Array}
         */
        getForcedProviders: function () {
            //jscs:disable requireCamelCaseOrUpperCaseIdentifiers
            return this.forced_providers;
        },

        /**
         * Get a list of enabled providers
         * @returns {Array}
         */
        getEnabledProviders: function () {
            //jscs:disable requireCamelCaseOrUpperCaseIdentifiers
            return this.enabled_providers;
        },

        /**
         * Return true if a provider is selected
         * @param {String} provider
         * @returns {Boolean}
         */
        isSelected: function (provider) {
            var i, providers = this.value();

            for (i = 0; i < providers.length; i++) {
                if (providers[i] === provider) {
                    return true;
                }
            }

            return false;
        }
    });
});
