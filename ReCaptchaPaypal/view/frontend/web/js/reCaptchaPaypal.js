/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'Magento_ReCaptchaFrontendUi/js/reCaptcha',
        'jquery'
    ],
    function (Component, $) {
        'use strict';

        return Component.extend({

            /**
             * Recaptcha callback
             * @param {String} token
             */
            reCaptchaCallback: function (token) {
                this.tokenField.value = token;
                this.$parentForm.trigger('captcha:endExecute');
            },

            /**
             * Initialize parent form.
             *
             * @param {Object} parentForm
             * @param {String} widgetId
             */
            initParentForm: function (parentForm, widgetId) {
                var me = this;

                parentForm.on('captcha:startExecute', function (event) {
                    if (!me.tokenField.value && me.getIsInvisibleRecaptcha()) {
                        // eslint-disable-next-line no-undef
                        grecaptcha.execute(widgetId);
                        event.preventDefault(event);
                        event.stopImmediatePropagation();
                    } else {
                        me.$parentForm.trigger('captcha:endExecute');
                    }
                });

                // Create a virtual token field
                this.tokenField = $('<input type="text" name="token" style="display: none" />')[0];
                this.$parentForm = parentForm;
                parentForm.append(this.tokenField);
            }
        });
    }
);
