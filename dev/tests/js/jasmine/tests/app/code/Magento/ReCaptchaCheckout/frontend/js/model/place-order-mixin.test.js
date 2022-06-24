/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define(['squire'
], function (Squire) {
    'use strict';

    var injector = new Squire(),

        defaultContext = require.s.contexts._,
        mixin,
        registry;

    beforeEach(function (done) {
        window.checkoutConfig = {
            defaultSuccessPageUrl: ''
        };

        injector.require([
            'Magento_ReCaptchaCheckout/js/model/place-order-mixin',
            'Magento_ReCaptchaWebapiUi/js/webapiReCaptchaRegistry'
        ], function (Mixin, Registry) {
            mixin = Mixin;
            registry = Registry;
            done();
        });
    });

    afterEach(function () {
        try {
            injector.clean();
            injector.remove();
        } catch (e) {}
    });

    describe('Magento_ReCaptchaCheckout/js/model/place-order-mixin', function () {
        it('mixin is applied to Magento_Checkout/js/model/place-order', function () {
            var placeOrderMixins = defaultContext.config.config.mixins['Magento_Checkout/js/model/place-order'];

            expect(placeOrderMixins['Magento_ReCaptchaCheckout/js/model/place-order-mixin']).toBe(true);
        });

        it('Magento_Checkout/js/action/redirect-on-success is called', function () {
            let recaptchaId = 'recaptcha-checkout-place-order',
                messageContainer = jasmine.createSpy('messageContainer'),
                payload = {},
                serviceUrl = 'test',

                /**
                 * Order place action mock
                 *
                 * @returns {{fail: fail, done: (function(Function): *)}}
                 */
                action =  function () {
                    return {
                        /**
                         * Success result for request
                         *
                         * @param {Function} handler
                         * @returns {*}
                         */
                        done: function (handler) {
                            handler();
                            return this;
                        },

                        /**
                         * Fail result for request
                         */
                        fail: function () {}
                    };
                };

            /**
             * Triggers declared listener
             *
             * @returns {*}
             */
            registry.triggers[recaptchaId] = function () {
                if (registry._listeners[recaptchaId] !== undefined) {
                    return registry._listeners[recaptchaId]('token');
                }
            };

            /**
             * Registers a listener
             *
             * @param id
             * @param func
             */
            registry.addListener = function (id, func) {
                registry._listeners[id] = func;
            };
            registry.removeListener = jasmine.createSpy();
            mixin()(action, serviceUrl, payload, messageContainer);
            expect(registry.removeListener).toHaveBeenCalledWith(recaptchaId);
        });
    });
});
