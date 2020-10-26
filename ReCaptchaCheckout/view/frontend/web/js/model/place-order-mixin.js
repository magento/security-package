/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_ReCaptchaWebapiUi/js/webapiReCaptchaRegistry'
], function ($, wrapper, recaptchaRegistry) {
    'use strict';

    return function (placeOrder) {
        return wrapper.wrap(placeOrder, function (originalAction, serviceUrl, payload, messageContainer) {
            var recaptchaDeferred;
            if (recaptchaRegistry.triggers.hasOwnProperty("recaptcha-checkout-place-order")) {
                recaptchaDeferred = $.Deferred();
                recaptchaRegistry.addListener("recaptcha-checkout-place-order", function (token) {
                    payload.xReCaptchaValue = token;
                    originalAction(serviceUrl, payload, messageContainer).done(function() {
                        recaptchaDeferred.resolve.apply(recaptchaDeferred, arguments);
                    }).fail(function() {
                        recaptchaDeferred.reject.apply(recaptchaDeferred, arguments);
                    });
                });
                recaptchaRegistry.triggers["recaptcha-checkout-place-order"]();

                return recaptchaDeferred;
            } else {
                return originalAction(serviceUrl, payload, messageContainer);
            }
        });
    };
});
