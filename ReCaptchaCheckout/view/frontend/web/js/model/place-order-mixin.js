/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* eslint-disable max-nested-callbacks */

define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_ReCaptchaWebapiUi/js/webapiReCaptchaRegistry'
], function ($, wrapper, recaptchaRegistry) {
    'use strict';

    return function (placeOrder) {
        return wrapper.wrap(placeOrder, function (originalAction, serviceUrl, payload, messageContainer) {
            var recaptchaDeferred,
                reCaptchaId,
                $activeReCaptcha;

            $activeReCaptcha = $('.recaptcha-checkout-place-order:visible .g-recaptcha');

            if ($activeReCaptcha.length > 0) {
                reCaptchaId = $activeReCaptcha.last().attr('id');
            }

            if (reCaptchaId !== undefined && recaptchaRegistry.triggers.hasOwnProperty(reCaptchaId)) {
                //ReCaptcha is present for checkout
                recaptchaDeferred = $.Deferred();
                recaptchaRegistry.addListener(reCaptchaId, function (token) {
                    //Add reCaptcha value to place-order request and resolve deferred with the API call results
                    payload.xReCaptchaValue = token;
                    originalAction(serviceUrl, payload, messageContainer).done(function () {
                        recaptchaDeferred.resolve.apply(recaptchaDeferred, arguments);
                    }).fail(function () {
                        recaptchaDeferred.reject.apply(recaptchaDeferred, arguments);
                    });
                });
                //Trigger ReCaptcha validation
                recaptchaRegistry.triggers[reCaptchaId]();

                if (
                    !recaptchaRegistry._isInvisibleType.hasOwnProperty(reCaptchaId) ||
                    recaptchaRegistry._isInvisibleType[reCaptchaId] === false
                ) {
                    //remove listener so that place order action is only triggered by the 'Place Order' button
                    recaptchaRegistry.removeListener(reCaptchaId);
                }

                return recaptchaDeferred;
            }

            //No ReCaptcha, just sending the request
            return originalAction(serviceUrl, payload, messageContainer);
        });
    };
});
