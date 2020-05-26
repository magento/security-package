/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
  'Magento_Ui/js/form/element/abstract',
  'Magento_Ui/js/modal/confirm'
], function (Abstract, confirm) {
    'use strict';

    return Abstract.extend({
        /**
         * Get a list of providers with reset option
         * @returns {Array}
         */
        getResetProviders: function () {
            return this.source.data['reset_providers'] ? this.source.data['reset_providers'] : [];
        },

        /**
         * Reset a provider
         * @param {Object} item
         */
        resetProvider: function (item) {
            confirm({
                title: 'Confirm',
                content: 'Are you sure you want to reset ' + item.label + ' provider?',
                actions: {
                    confirm: function () { // jscs:ignore jsDoc
                        self.location.href = item.url;
                    }
                }
            });
        }
    });
});
