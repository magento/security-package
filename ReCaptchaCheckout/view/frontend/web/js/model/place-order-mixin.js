/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* eslint-disable max-nested-callbacks */

define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_ReCaptchaWebapiUi/js/webapiReCaptchaRegistry',
    'Magento_Checkout/js/action/redirect-on-success'
], function ($, wrapper, recaptchaRegistry, redirectOnSuccessAction) {
    'use strict';

    return function (placeOrder) {
        return wrapper.wrap(placeOrder, function (originalAction, serviceUrl, payload, messageContainer) {
            var recaptchaDeferred;

            if (recaptchaRegistry.triggers.hasOwnProperty('recaptcha-checkout-place-order')) {
                //ReCaptcha is present for checkout
                recaptchaDeferred = $.Deferred();
                recaptchaRegistry.addListener('recaptcha-checkout-place-order', function (token) {
                    //Add reCaptcha value to place-order request and resolve deferred with the API call results
                    payload.xReCaptchaValue = token;
                    originalAction(serviceUrl, payload, messageContainer).done(function () {
                        recaptchaDeferred.resolve.apply(recaptchaDeferred, arguments);
                        redirectOnSuccessAction.execute();
                    }).fail(function () {
                        recaptchaDeferred.reject.apply(recaptchaDeferred, arguments);
                    });
                });
                //Trigger ReCaptcha validation
                recaptchaRegistry.triggers['recaptcha-checkout-place-order']();
                //remove listener so that place order action is only triggered by the 'Place Order' button
                recaptchaRegistry.removeListener('recaptcha-checkout-place-order');
                return recaptchaDeferred;
            }

            //No ReCaptcha, just sending the request
            return originalAction(serviceUrl, payload, messageContainer);
        });
    };
});
