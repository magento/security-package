/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([], function () {
    'use strict';

    return {
        /**
         * Constructor
         *
         * @returns {self}
         * @constructor
         */
        'Magento_TwoFactorAuth/js/u2fkey/utils': function () {
            return this;
        },

        /**
         * Convert an array buffer to base64
         *
         * @param {ArrayBuffer} buffer
         * @returns {String}
         * @private
         */
        arrayBufferToBase64: function (buffer) {
            var binary = '',
                // eslint-disable-next-line no-undef
                bytes = new Uint8Array(buffer),
                len = bytes.byteLength,
                i = 0;

            for (i = 0; i < len; i++) {
                binary += String.fromCharCode(bytes[i]);
            }

            return window.btoa(binary)
                .replace(/\-/g, '+')
                .replace(/_/g, '/');
        },

        /**
         * Convert a Uint8Array to a UTF-8 string using cross-browser safe methods
         *
         * @param {Uint8Array} uint8array
         * @param {Function} callback
         * @private
         */
        asyncUint8ArrayToUtf8String: function (uint8array, callback) {
            var blob = new Blob([uint8array]),
                fileReader = new FileReader();

            /**
             * Handle loaded
             *
             * @param {Event} e
             */
            fileReader.onload = function (e) {
                callback(e.target.result);
            };

            fileReader.readAsText(blob);
        }
    };
});
