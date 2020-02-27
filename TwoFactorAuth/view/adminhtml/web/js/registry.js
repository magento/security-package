/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [],
    function () {
    'use strict';

    return {
        /**
         * Trusted devices are not allowed.
         * @returns {Boolean}
         */
        trustThisDevice: function () {
            return false;
        }
    };
});
