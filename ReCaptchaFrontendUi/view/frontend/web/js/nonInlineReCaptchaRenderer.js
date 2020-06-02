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
        add: function (reCaptchaEntity, parameters) {
            if (parameters.size === 'invisible' && parameters.badge !== 'inline') {
                if (!initialized) {
                    this._init();
                    grecaptcha.render(rendererRecaptchaId, parameters);
                    setInterval(this._resolveVisibility, 100);
                    initialized = true;
                }

                reCaptchaEntities.push(reCaptchaEntity);
            }
        },

        _resolveVisibility: function () {
            reCaptchaEntities.some(
                (entity) => {
                    return entity.is(":visible")
                        // 900 is some magic z-index value of modal popups.
                        && (entity.closest("[data-role='modal']").length == 0 || entity.zIndex() > 900)
                }) ? rendererReCaptcha.show() : rendererReCaptcha.hide();
        },

        _init: function () {
            rendererReCaptcha = $('<div/>', {
                'id': rendererRecaptchaId
            });
            $('body').append(rendererReCaptcha);
        }
    };
});
