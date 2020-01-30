/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

'use strict';

define([
    'ko'
], function (ko) {
    return {
        currentStep: ko.observable('register'),
        messageText: ko.observable(''),
        secondsToExpire: ko.observable(0)
    };
});
