/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* global grecaptcha */
define(
    [
        'Magento_ReCaptchaFrontendUi/js/reCaptcha',
        'Magento_ReCaptchaWebapiUi/js/webapiReCaptchaRegistry'
    ],
    function (Component, registry) {
        'use strict';

        return Component.extend({
            defaults: {
                autoTrigger: false
            },

            /**
             * Provide the token to the registry.
             *
             * @param {String} token
             */
            reCaptchaCallback: function (token) {
                //Make the token retrievable in other UI components.
                registry.tokens[this.getReCaptchaId()] = token;

                if (typeof registry._listeners[this.getReCaptchaId()] !== "undefined") {
                    registry._listeners[this.getReCaptchaId()](token);
                }
            },

            /**
             * Register this ReCaptcha.
             *
             * @param {Object} parentForm
             * @param {String} widgetId
             */
            initParentForm: function (parentForm, widgetId) {
                var trigger = function () {
                    grecaptcha.execute(widgetId);
                };

                if (this.autoTrigger) {
                    //Validate ReCaptcha when initiated
                    trigger();
                    registry.triggers[this.getReCaptchaId()] = new Function();
                } else {
                    registry.triggers[this.getReCaptchaId()] = trigger;
                }
                this.tokenField = null;
            }
        });
    }
);
