/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

'use strict';

define(['ko'], function (ko) {
return {
    ids: ko.observableArray([]),
    captchaList: ko.observableArray([]),
    tokenFields: ko.observableArray([])
};
});
