/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'Magento_TwoFactorAuth/js/registry'
], function (Component, registry) {
    'use strict';

    return Component.extend({
        checked: registry.trustThisDevice,

        defaults: {
            template: 'Magento_TwoFactorAuth/trust_device'
        }
    });
});
