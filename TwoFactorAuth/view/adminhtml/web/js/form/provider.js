/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/form/provider'
], function (Provider) {
    'use strict';

    return Provider.extend({
        /**
         * @see Magento_Ui/js/form/provider
         * @returns {Element}
         */
        save: function () {
            // Disable independent save (we have a parent form with own validation)
            return this;
        }
    });
});
