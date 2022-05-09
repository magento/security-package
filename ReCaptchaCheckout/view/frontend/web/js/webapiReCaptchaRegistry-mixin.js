/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([], function () {
    'use strict';

    return function(originalFunction){
        /**
         * Add a listener to when the ReCaptcha finishes verification
         * @param {String} id - ReCaptchaId
         * @param {Function} func - Will be called back with the token
         */
       originalFunction.addListener = function(id , func) {
            this._listeners[id] = func;
       }

        return originalFunction;
    };

});