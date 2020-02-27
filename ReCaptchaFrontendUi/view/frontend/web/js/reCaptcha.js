/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* eslint-disable no-undef */
// jscs:disable jsDoc
define(
    [
        'uiComponent',
        'jquery',
        'ko',
        'Magento_ReCaptchaFrontendUi/js/registry'
    ],
    function (Component, $, ko, registry, undefined) {
        'use strict';

        return Component.extend({

            defaults: {
                template: 'Magento_ReCaptchaFrontendUi/reCaptcha',
                reCaptchaId: 'recaptcha'
            },
            _isApiRegistered: undefined,

            initialize: function () {
                this._super();
                this._loadApi();
            },

            /**
             * Loads recaptchaapi API and triggers event, when loaded
             * @private
             */
            _loadApi: function () {
                var element, scriptTag;

                if (this._isApiRegistered !== undefined) {
                    if (this._isApiRegistered === true) {
                        $(window).trigger('recaptchaapiready');
                    }

                    return;
                }
                this._isApiRegistered = false;

                // global function
                window.globalOnRecaptchaOnLoadCallback = function () {
                    this._isApiRegistered = true;
                    $(window).trigger('recaptchaapiready');
                }.bind(this);

                element = document.createElement('script');
                scriptTag = document.getElementsByTagName('script')[0];

                element.async = true;
                element.src = 'https://www.google.com/recaptcha/api.js' +
                    '?onload=globalOnRecaptchaOnLoadCallback&render=explicit' +
                    (this.settings.lang ? '&hl=' + this.settings.lang : '');

                scriptTag.parentNode.insertBefore(element, scriptTag);

            },

            /**
             * Checking that reCAPTCHA is invisible type
             * @returns {Boolean}
             */
            getIsInvisibleRecaptcha: function () {
                return this.settings.invisible;
            },

            /**
             * reCAPTCHA callback
             * @param {String} token
             */
            reCaptchaCallback: function (token) {
                if (this.getIsInvisibleRecaptcha()) {
                    this.tokenField.value = token;
                    this.$parentForm.submit();
                }
            },

            /**
             * Initialize reCAPTCHA after first rendering
             */
            initCaptcha: function () {
                var me = this,
                    $parentForm,
                    $wrapper,
                    $reCaptcha,
                    widgetId,
                    listeners;

                if (this.captchaInitialized) {
                    return;
                }

                this.captchaInitialized = true;

                /*
                 * Workaround for data-bind issue:
                 * We cannot use data-bind to link a dynamic id to our component
                 * See:
                 * https://stackoverflow.com/questions/46657573/recaptcha-the-bind-parameter-must-be-an-element-or-id
                 *
                 * We create a wrapper element with a wrapping id and we inject the real ID with jQuery.
                 * In this way we have no data-bind attribute at all in our reCAPTCHA div
                 */
                $wrapper = $('#' + this.getReCaptchaId() + '-wrapper');
                $reCaptcha = $wrapper.find('.g-recaptcha');
                $reCaptcha.attr('id', this.getReCaptchaId());

                $parentForm = $wrapper.parents('form');
                me = this;

                let parameters = _.extend(
                    {
                        'callback': function (token) { // jscs:ignore jsDoc
                            me.reCaptchaCallback(token);
                            me.validateReCaptcha(true);
                        },
                        'expired-callback': function () {
                            me.validateReCaptcha(false);
                        },
                        'size': 'invisible'
                    },
                    this.settings.rendering
                );

                // eslint-disable-next-line no-undef
                widgetId = grecaptcha.render(this.getReCaptchaId(), parameters);

                if (this.getIsInvisibleRecaptcha() && $parentForm.length > 0) {
                    $parentForm.submit(function (event) {
                        if (!me.tokenField.value) {
                            // eslint-disable-next-line no-undef
                            grecaptcha.execute(widgetId);
                            event.preventDefault(event);
                            event.stopImmediatePropagation();
                        }
                    });

                    // Move our (last) handler topmost. We need this to avoid submit bindings with ko.
                    listeners = $._data($parentForm[0], 'events').submit;
                    listeners.unshift(listeners.pop());

                    // Create a virtual token field
                    this.tokenField = $('<input type="text" name="token" style="display: none" />')[0];
                    this.$parentForm = $parentForm;
                    $parentForm.append(this.tokenField);
                } else {
                    this.tokenField = null;
                }

                registry.ids.push(this.getReCaptchaId());
                registry.captchaList.push(widgetId);
                registry.tokenFields.push(this.tokenField);

            },

            validateReCaptcha: function (state) {
                if (!this.getIsInvisibleRecaptcha()) {
                    return $(document).find('input[type=checkbox].required-captcha').prop('checked', state);
                }
            },

            /**
             * Render reCAPTCHA
             */
            renderReCaptcha: function () {
                var me = this;

                if (window.grecaptcha && window.grecaptcha.render) { // Check if reCAPTCHA is already loaded
                    me.initCaptcha();
                } else { // Wait for reCAPTCHA to be loaded
                    $(window).on('recaptchaapiready', function () {
                        me.initCaptcha();
                    });
                }
            },

            /**
             * Get reCAPTCHA ID
             * @returns {String}
             */
            getReCaptchaId: function () {
                return this.reCaptchaId;
            }
        });
    }
);
