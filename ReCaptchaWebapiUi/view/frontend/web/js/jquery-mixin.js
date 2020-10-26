/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'mage/utils/wrapper'
], function (wrapper) {
    'use strict';

    return function (jQuery) {
        jQuery.ajax = wrapper.wrapSuper(jQuery.ajax, function () {
            var settings,
                payload;

            if (arguments.length !== 0) {
                settings = arguments.length === 1 ? arguments[0] : arguments[1];
                console.log(settings);
                if (settings.hasOwnProperty("data")) {
                    try {
                        payload = JSON.parse(settings.data);
                        if (payload && payload.hasOwnProperty("xReCaptchaValue")) {
                            if (!settings.hasOwnProperty("headers")) {
                                settings.headers = {};
                            }
                            settings.headers['X-ReCaptcha'] = payload.xReCaptchaValue;
                            delete payload["xReCaptchaValue"];
                            settings.data = JSON.stringify(payload);
                        }
                    } catch (e) {
                        //Not JSON
                    }
                }
            }

            return this._super.apply(this, arguments);
        });

        return jQuery;
    };
});
