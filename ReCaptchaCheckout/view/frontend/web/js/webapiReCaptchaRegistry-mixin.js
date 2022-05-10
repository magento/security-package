/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([], function () {
    'use strict';

    return function (originalFunction) {
        /**
         * {@inheritDoc}
         */
       originalFunction.addListener = function (id , func) {
            this._listeners[id] = func;
       };

        return originalFunction;
    };

});
