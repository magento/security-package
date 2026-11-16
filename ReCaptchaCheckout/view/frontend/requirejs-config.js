/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
 */

// eslint-disable-next-line no-unused-vars
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/model/place-order': {
                'Magento_ReCaptchaCheckout/js/model/place-order-mixin': true
            },
            'Magento_PurchaseOrder/js/model/place-order-purchase-quote': {
                'Magento_ReCaptchaCheckout/js/model/place-order-mixin': true
            },
            'Magento_ReCaptchaWebapiUi/js/webapiReCaptchaRegistry': {
                'Magento_ReCaptchaCheckout/js/webapiReCaptchaRegistry-mixin': true
            }
        }
    }
};

