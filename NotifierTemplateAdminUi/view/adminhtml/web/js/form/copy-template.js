/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'ko',
    'Magento_Ui/js/form/element/abstract',
    'mage/translate'
], function (ko, Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            visible: true,
            sysTemplates: [],
            templateMessage: '',
            selectedTemplate: ko.observable(),
            links: {
                templateMessage: ''
            }
        },

        /**
         * Get system templates
         * @returns {array}
         */
        getSysTemplates: function () {
            return this.sysTemplates;
        },

        /**
         * Copy template to destination field
         */
        copyTemplate: function () {
            this.set('templateMessage', this.selectedTemplate().content);
        }
    });
});