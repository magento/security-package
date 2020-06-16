/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery'
], function ($) {
    'use strict';

    var reCaptchaEntities = [],
        initialized = false,
        rendererRecaptchaId = 'recaptcha-invisible',
        rendererReCaptcha = null;

    return {
        /**
         * Add reCaptcha entity to checklist.
         *
         * @param {jQuery} reCaptchaEntity
         * @param {Object} parameters
         */
        add: function (reCaptchaEntity, parameters) {
            if (!initialized) {
                this.init();
                grecaptcha.render(rendererRecaptchaId, parameters);
                setInterval(this.resolveVisibility, 100);
                initialized = true;
            }

            reCaptchaEntities.push(reCaptchaEntity);
        },

        /**
         * Show additional reCaptcha instance if any other should be visible, otherwise hide it.
         *
         * @private
         */
        resolveVisibility: function () {
            reCaptchaEntities.some(
                /**
                 * Check if reCaptcha instance is visible.
                 *
                 * @param {jQuery} entity
                 * @returns {Boolean}
                 */
                function (entity) {
                    return entity.is(':visible') &&
                        // 900 is some magic z-index value of modal popups.
                        (entity.closest('[data-role=\'modal\']').length === 0 || entity.zIndex() > 900)
                }) ? rendererReCaptcha.show() : rendererReCaptcha.hide();
        },

        /**
         * Initialize additional reCaptcha instance.
         *
         * @private
         */
        init: function () {
            rendererReCaptcha = $('<div/>', {
                'id': rendererRecaptchaId
            });
            $('body').append(rendererReCaptcha);
        }
    };
});
