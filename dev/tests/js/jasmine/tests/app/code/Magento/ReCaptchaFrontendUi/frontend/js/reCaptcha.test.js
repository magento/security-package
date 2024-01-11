/************************************************************************
 *
 * Copyright 2023 Adobe
 * All Rights Reserved.
 *
 * NOTICE: All information contained herein is, and remains
 * the property of Adobe and its suppliers, if any. The intellectual
 * and technical concepts contained herein are proprietary to Adobe
 * and its suppliers and are protected by all applicable intellectual
 * property laws, including trade secret and copyright laws.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Adobe.
 * ************************************************************************
 */

/* eslint-disable max-nested-callbacks */
define([
    'squire'
], function (Squire) {
    'use strict';

    var injector = new Squire(),
        mocks = {
            'Magento_ReCaptchaFrontendUi/js/registry': {
                ds: [],
                captchaList: [],
                tokenFields: []
            },
            'Magento_ReCaptchaFrontendUi/js/reCaptchaScriptLoader': {
                addReCaptchaScriptTag: jasmine.createSpy('reCaptchaScriptLoader.addReCaptchaScriptTag')
            },
            'Magento_ReCaptchaFrontendUi/js/nonInlineReCaptchaRenderer': {
                add: jasmine.createSpy('nonInlineReCaptchaRenderer.add')
            }
        },
        reCaptchaModel,
        formElement,
        submitButtonElement,
        $;

    beforeEach(function (done) {
        injector.mock(mocks);
        injector.require(['jquery', 'Magento_ReCaptchaFrontendUi/js/reCaptcha'], function (jq, reCaptchaUiComponent) {
            reCaptchaModel = new reCaptchaUiComponent();
            $ = jq;
            done();
        });
        formElement = document.createElement('form');
        submitButtonElement = document.createElement('button');
        formElement.appendChild(submitButtonElement);
        window.document.body.appendChild(formElement);
        window.grecaptcha = {
            render: jasmine.createSpy('window.grecaptcha.render'),
            execute: jasmine.createSpy('window.grecaptcha.execute')
        };
    });

    afterEach(function () {
        try {
            injector.clean();
            injector.remove();
        } catch (e) {
        }
        formElement.remove();
        formElement = undefined;
        submitButtonElement = undefined;
    });

    describe('Magento_ReCaptchaFrontendUi/js/reCaptcha', function () {
        describe('Invisible ReCaptcha', function () {
            beforeEach(function () {
                reCaptchaModel.getIsInvisibleRecaptcha = jasmine.createSpy().and.returnValue(true);
            });

            describe('"initParentForm" method', function () {
                it(
                    'should disable submit button, prevent submit handlers from executing and execute recaptcha' +
                    ' on submit',
                    function () {
                        var request = {
                            send: jasmine.createSpy('request.send')
                        };

                        // check that submit button is enabled
                        expect(submitButtonElement.disabled).toBeFalse();
                        $(formElement).on('submit', function (event) {
                            event.preventDefault();
                            request.send();
                        });
                        reCaptchaModel.initParentForm($(formElement), 'test');
                        $(formElement).submit();
                        // check that submit button is disabled
                        expect(submitButtonElement.disabled).toBeTrue();
                        // check that grecaptcha is executed
                        expect(window.grecaptcha.execute).toHaveBeenCalledOnceWith('test');
                        // check that other submit handlers are not executed
                        expect(request.send).not.toHaveBeenCalled();
                    });

                it('should add a token input field to the form', function () {
                    submitButtonElement.disabled = true;
                    reCaptchaModel.initParentForm($(formElement), 'test');
                    expect(submitButtonElement.disabled).toBeFalse();
                    expect(reCaptchaModel.tokenField).not.toBeNull();
                    expect($(reCaptchaModel.tokenField).parents('form').is($(formElement))).toBeTrue();
                });
            });
            describe('"reCaptchaCallback" method', function () {
                it('should enable submit button, set token input value and submit the form', function () {
                    var request = {
                        send: jasmine.createSpy('request.send')
                    };

                    submitButtonElement.disabled = true;
                    reCaptchaModel.$parentForm = $(formElement);
                    reCaptchaModel.tokenField = $('<input type="text" name="token" style="display: none" />')[0];

                    $(formElement).on('submit', function (event) {
                        event.preventDefault();
                        request.send();
                    });
                    reCaptchaModel.reCaptchaCallback('testtoken');
                    // check that submit button is enabled
                    expect(submitButtonElement.disabled).toBeFalse();
                    // check that token input value is set
                    expect(reCaptchaModel.tokenField.value).toEqual('testtoken');
                    // check that form is submitted
                    expect(request.send).toHaveBeenCalled();
                });
            });
        });
    });
});
