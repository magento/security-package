/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

'use strict';

define([
    'Magento_Ui/js/form/provider'
], function (Provider) {
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
