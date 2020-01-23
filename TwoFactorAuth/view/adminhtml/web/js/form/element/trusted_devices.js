/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

'use strict';

define([
    'Magento_Ui/js/form/element/abstract'
], function (Abstract) {
    return Abstract.extend({
        /**
         * Get a list of trusted devices
         * @returns {Array}
         */
        getTrustedDevices: function () {
            return this.source.data['trusted_devices'] ? this.source.data['trusted_devices'] : [];
        },

        /**
         * Revoke a trusted device
         * @param {Object} item
         */
        revokeDevice: function (item) {
            self.location.href = item['revoke_url'];
        }
    });
});
