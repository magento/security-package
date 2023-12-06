/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_TwoFactorAuth/js/error',
    'Magento_TwoFactorAuth/js/u2fkey/utils',
    'mage/translate'
], function ($, ko, Component, error, utils, $t) {
    'use strict';

    return Component.extend({
        currentStep: ko.observable('register'),

        defaults: {
            template: 'Magento_TwoFactorAuth/u2fkey/configure',
            idle: ko.observable(true),
            loading: ko.observable(false)
        },

        postUrl: '',
        successUrl: '',
        touchImageUrl: '',
        registerData: {},

        /**
         * @inheritdoc
         */
        initConfig: function (config) {
            this._super(config);
            // eslint-disable-next-line no-undef
            this.registerData.publicKey.challenge = new Uint8Array(this.registerData.publicKey.challenge);
            // eslint-disable-next-line no-undef
            this.registerData.publicKey.user.id = new Uint8Array(this.registerData.publicKey.user.id);

            return this;
        },

        /**
         * Start waiting loop
         */
        onAfterRender: function () {
            this.waitForTouch();
        },

        /**
         * Get touch image URL
         *
         * @returns {String}
         */
        getTouchImageUrl: function () {
            return this.touchImageUrl;
        },

        /**
         * Get POST URL
         *
         * @returns {String}
         */
        getPostUrl: function () {
            return this.postUrl;
        },

        /**
         * Get success URL
         * @returns {String}
         */
        getSuccessUrl: function () {
            return this.successUrl;
        },

        /**
         * Wait for key touch
         */
        waitForTouch: function () {
            this.idle(false);
            if (!navigator.credentials) {
                this.currentStep('no-webauthn');
                return;
            }
            navigator.credentials.create({
                publicKey: this.registerData.publicKey
            })
            .then(this._onCredentialSuccess.bind(this))
            .catch(this._onCredentialError.bind(this));
        },

        /**
         * Handle WebAuthn success
         *
         * @param {Object} credentialData
         * @returns void
         * @private
         */
        _onCredentialSuccess: function (credentialData) {
            utils.asyncUint8ArrayToUtf8String(
                // eslint-disable-next-line no-undef
                new Uint8Array(credentialData.response.clientDataJSON),
                function (clientDataJSON) {
                    credentialData.clientData = JSON.parse(clientDataJSON);
                    this._processCredentialData(credentialData);
                }.bind(this)
            );
        },

        /**
         * Validate and submit response from u2f key
         *
         * @param {Object} credentialData
         * @private
         */
        _processCredentialData: function (credentialData) {
            this.loading(true);
            $.post(this.getPostUrl(), {
                publicKeyCredential: {
                    id: utils.arrayBufferToBase64(credentialData.rawId),
                    response: {
                        clientData: credentialData.clientData,
                        attestationObject: utils.arrayBufferToBase64(
                            credentialData.response.attestationObject
                        )
                    }
                }
            })
            .done(function (res) {
                this.loading(false);

                if (res.success) {
                    this.currentStep('login');
                    self.location.href = this.getSuccessUrl();
                } else {
                    error.display($t('There was an error processing your key.'));
                    this.idle(true);
                }
            }.bind(this))
            .fail(function () {
                error.display($t('Unable to register your device'));
                this.loading(false);
                this.idle(true);
            }.bind(this));
        },

        /**
         * Handle WebAuthn failure
         *
         * @param {Object} u2fError
         * @return void
         * @private
         */
        _onCredentialError: function (u2fError) {
            this.idle(true);

            if (['AbortError', 'NS_ERROR_ABORT'].indexOf(u2fError.name) === -1) {
                error.display($t(u2fError.message));
            }
        }
    });
});
