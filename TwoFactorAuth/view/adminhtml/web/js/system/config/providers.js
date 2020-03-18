/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/translate',
    'Magento_Ui/js/modal/confirm',
    'domReady!'
], function ($, $t, confirm) {
    'use strict';

    return function (config, element) {

        var $element = $(element),
            initialValue = $element.val();

        element.on('blur', function () {
            var currentValue = $element.val();

            if (currentValue && currentValue.some(function (item) {
                return initialValue.indexOf(item) !== -1;
            })) {
                return;
            }

            confirm({
                title: config.modalTitleText,
                content: config.modalContentBody,
                buttons: [{
                    text: $t('Cancel'),
                    class: 'action-secondary action-dismiss',

                    /**
                     * Close modal and trigger 'cancel' action on click
                     */
                    click: function (event) {
                        this.closeModal(event);
                    }
                }, {
                    text: $t('Confirm'),
                    class: 'action-primary action-accept',

                    /**
                     * Close modal and trigger 'confirm' action on click
                     */
                    click: function (event) {
                        this.closeModal(event, true);
                    }
                }],
                actions: {

                    /**
                     * Revert back to original Enabled setting
                     */
                    cancel: function () {
                        $element.val(initialValue);
                    }
                }
            });
        });
    };
});
