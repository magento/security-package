/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_TwoFactorAuth/js/error',
    'mage/translate'
], function ($, ko, Component, error) {
    'use strict';

    return Component.extend({
        selectedMethod: ko.observable(''),
        waitingText: ko.observable(''),
        success: ko.observable(false),
        tokenCode: ko.observable(''),

        defaults: {
            template: 'Magento_TwoFactorAuth/authy/auth'
        },

        waitForOneTouchApprovalTimeout: 0,

        postUrl: '',
        tokenRequestUrl: '',
        oneTouchUrl: '',
        verifyOneTouchUrl: '',

        /**
         * Get auth post URL
         * @returns {String}
         */
        getPostUrl: function () {
            return this.postUrl;
        },

        /**
         * Get token request URL
         * @returns {String}
         */
        getTokenRequestUrl: function () {
            return this.tokenRequestUrl;
        },

        /**
         * Get one touch request URL
         * @returns {String}
         */
        getOneTouchUrl: function () {
            return this.oneTouchUrl;
        },

        /**
         * Get one touch verification URL
         * @returns {String}
         */
        getVerifyOneTouchUrl: function () {
            return this.verifyOneTouchUrl;
        },

        /**
         * Get success URL
         * @returns {String}
         */
        getSuccessUrl: function () {
            return this.successUrl;
        },

        /**
         * Go to login page
         */
        login: function () {
            this.success(true);
            self.location.href = this.getSuccessUrl();
        },

        /**
         * Stop onetouch approval background approval
         */
        stopWaitingOnetouchApproval: function () {
            if (this.waitForOneTouchApprovalTimeout) {
                window.clearTimeout(this.waitForOneTouchApprovalTimeout);
                this.waitForOneTouchApprovalTimeout = 0;
            }
        },

        /**
         * Switch to authy code validation
         * @param {String} via
         */
        runSendCode: function (via) {
            var me = this;

            this.selectedMethod('code');

            if (via !== 'token') {
                $.getJSON(
                    this.getTokenRequestUrl() + '?via=' +
                    via
                )
                    .fail(function () {
                        error.display('There was an error trying to contact Authy services');
                        me.switchAnotherMethod();
                    });
            }
        },

        /**
         * Switch to authy token code validation
         */
        runSendCodeToken: function () {
            this.runSendCode('token');
        },

        /**
         * Switch to authy sms code validation
         */
        runSendCodeSms: function () {
            this.runSendCode('sms');
        },

        /**
         * Switch to authy call code validation
         */
        runSendCodeCall: function () {
            this.runSendCode('call');
        },

        /**
         * Switch to one touch validation
         */
        runOneTouch: function () {
            var me = this;

            this.selectedMethod('onetouch');
            this.waitingText('Sending push notification...');
            this.success(false);

            this.stopWaitingOnetouchApproval();

            $.getJSON(this.getOneTouchUrl())
                .done(function () {
                    me.waitForOneTouchApproval();
                })
                .fail(function () {
                    error.display('There was an error trying to contact Authy services');
                    me.switchAnotherMethod();
                });
        },

        /**
         * Start background one touch approval check
         */
        waitForOneTouchApproval: function () {
            var me = this;

            this.waitingText('Waiting for approval...');

            $.getJSON(this.getVerifyOneTouchUrl())
                .done(function (res) {
                    if (res.status === 'retry') {
                        me.waitForOneTouchApprovalTimeout = window.setTimeout(function () {
                            me.waitForOneTouchApproval();
                        }, 1000);
                    } else if (res.status === 'expired') {
                        error.display($.mage.__('Your request has been expired'));
                        me.switchAnotherMethod();
                    } else if (res.status === 'denied') {
                        error.display($.mage.__('Your request has been rejected'));
                        me.switchAnotherMethod();
                    } else if (res.status === 'approved') {
                        me.login();
                    }
                })
                .fail(function () {
                    error.display('There was an error trying to contact Authy services');
                    this.switchAnotherMethod();
                });
        },

        /**
         * Switch back to method selection
         */
        switchAnotherMethod: function () {
            this.selectedMethod('');
            this.waitingText('');
            this.success(false);
        },

        /**
         * Verify authy code
         */
        verifyCode: function () {
            var me = this;

            this.waitingText('Please wait...');

            $.post(this.getPostUrl(), {
                'tfa_code': this.tokenCode
            })
                .done(function (res) {
                    if (res.success) {
                        me.login();
                    } else {
                        error.display(res.message);
                        me.waitingText('');
                        me.tokenCode('');
                    }
                })
                .fail(function () {
                    error.display('There was an internal error trying to verify your code');
                });
        }
    });
});
