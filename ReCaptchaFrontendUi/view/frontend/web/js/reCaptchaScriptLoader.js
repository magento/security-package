/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
 */

define([], function () {
    'use strict'; // eslint-disable-line

    var scriptTagAdded = false;

    return {
        /**
         * Add script tag. Script tag should be added once
         */
        addReCaptchaScriptTag: function (languageCode) {
            var element, scriptTag;

            if (!scriptTagAdded) {
                element = document.createElement('script');
                scriptTag = document.getElementsByTagName('script')[0];

                element.async = true;
                element.src = 'https://www.google.com/recaptcha/api.js' +
                    '?onload=globalOnRecaptchaOnLoadCallback&render=explicit';

                if (languageCode) {
                    element.src += '&hl=' + encodeURIComponent(languageCode);
                }

                scriptTag.parentNode.insertBefore(element, scriptTag);
                scriptTagAdded = true;
            }
        }
    };
});
