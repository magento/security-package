/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko',
    'uiComponent',
    'Magento_TwoFactorAuth/js/authy/configure/registry'
], function (ko, Component, registry) {
    'use strict';

    return Component.extend({
        currentStep: registry.currentStep,
        defaults: {
            template: 'Magento_TwoFactorAuth/authy/configure'
        }
    });
});
