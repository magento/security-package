/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

'use strict';

define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'mage/translate'
], function ($, alert) {
    return {
        /**
         * Display an error message
         * @param {String} message
         */
        display: function (message) {
            alert({
                title: $.mage.__('Error'),
                content: message
            });
        }
    };
});
