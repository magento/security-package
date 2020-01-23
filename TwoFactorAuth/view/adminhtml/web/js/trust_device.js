/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

'use strict';

define([
    'uiComponent',
    'Magento_TwoFactorAuth/js/registry'
], function (Component, registry) {
    return Component.extend({
        checked: registry.trustThisDevice,

        defaults: {
            template: 'Magento_TwoFactorAuth/trust_device'
        }
    });
});
