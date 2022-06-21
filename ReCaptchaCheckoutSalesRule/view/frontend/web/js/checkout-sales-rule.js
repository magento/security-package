/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/* global grecaptcha */
define(
    [
        'Magento_ReCaptchaWebapiUi/js/webapiReCaptcha',
        'Magento_ReCaptchaWebapiUi/js/webapiReCaptchaRegistry',
        'jquery',
        'Magento_SalesRule/js/action/set-coupon-code',
        'Magento_SalesRule/js/action/cancel-coupon',
        'Magento_Checkout/js/model/quote',
        'ko'
    ], function (Component, recaptchaRegistry, $, setCouponCodeAction, cancelCouponAction, quote, ko) {
        'use strict';

        var totals = quote.getTotals(),
            couponCode = ko.observable(null),
            isApplied;

        if (totals()) {
            couponCode(totals()['coupon_code']);
        }
        //Captcha can only be required for adding a coupon so we need to know if one was added already.
        isApplied = ko.observable(couponCode() != null);

        return Component.extend({

            /**
             * Initialize parent form.
             *
             * @param {Object} parentForm
             * @param {String} widgetId
             */
            initParentForm: function (parentForm, widgetId) {
                var self = this,
                    xRecaptchaValue,
                    captchaId = this.getReCaptchaId();

                this._super();

                if (couponCode() != null) {
                    if (isApplied) {
                        self.validateReCaptcha(true);
                        $('#' + captchaId).hide();
                    }
                }

                if (recaptchaRegistry.triggers.hasOwnProperty('recaptcha-checkout-coupon-apply')) {
                    recaptchaRegistry.addListener('recaptcha-checkout-coupon-apply', function (token) {
                        //Add reCaptcha value to coupon request
                        xRecaptchaValue = token;
                    });
                }

                setCouponCodeAction.registerDataModifier(function (headers) {
                    headers['X-ReCaptcha'] = xRecaptchaValue;
                });

                if (self.getIsInvisibleRecaptcha()) {
                    grecaptcha.execute(widgetId);
                    self.validateReCaptcha(true);
                }
                //Refresh reCaptcha after failed request.
                setCouponCodeAction.registerFailCallback(function () {
                    if (self.getIsInvisibleRecaptcha()) {
                        grecaptcha.execute(widgetId);
                        self.validateReCaptcha(true);
                    } else {
                        self.validateReCaptcha(false);
                        grecaptcha.reset(widgetId);
                        $('#' + captchaId).show();
                    }
                });
                //Hide captcha when a coupon has been applied.
                setCouponCodeAction.registerSuccessCallback(function () {
                    self.validateReCaptcha(true);
                    $('#' + captchaId).hide();
                });
                //Show captcha again if it was canceled.
                cancelCouponAction.registerSuccessCallback(function () {
                    self.validateReCaptcha(false);
                    grecaptcha.reset(widgetId);
                    $('#' + captchaId).show();
                });
            }
        });
    });
