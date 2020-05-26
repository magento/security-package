/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko'
], function (ko) {
    'use strict';

    return {
        currentStep: ko.observable('register'),
        messageText: ko.observable(''),
        secondsToExpire: ko.observable(0)
    };
});
