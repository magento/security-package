/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([], function () {
    'use strict';

    return {
        /**
         * recaptchaId: token map.
         *
         * Tokens for already verified recaptcha.
         */
        tokens: {},

        /**
         * recaptchaId: triggerFn map.
         *
         * Call a trigger to initiate a recaptcha verification.
         */
        triggers: {},

        /**
         * recaptchaId: callback map
         */
        _listeners: {},

        /**
         * Add a listener to when the ReCaptcha finishes verification
         * @param {String} id - ReCaptchaId
         * @param {Function} func - Will be called back with the token
         */
        addListener: function (id, func) {
            if (this.tokens.hasOwnProperty(id)) {
                func(this.tokens[id]);
            } else {
                this._listeners[id] = func;
            }
        }
    };
});
