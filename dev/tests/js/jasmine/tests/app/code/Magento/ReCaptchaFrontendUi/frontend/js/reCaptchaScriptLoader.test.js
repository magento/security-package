/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */

define([
    'squire'
], function (Squire) {
    'use strict';

    describe('Magento_ReCaptchaFrontendUi/js/reCaptchaScriptLoader', function () {
        var injector,
            loader,
            insertScript,
            apiUrl = 'https://www.google.com/recaptcha/api.js' +
                '?onload=globalOnRecaptchaOnLoadCallback&render=explicit';

        function prepareLoader(done) {
            injector = new Squire();
            injector.require(['Magento_ReCaptchaFrontendUi/js/reCaptchaScriptLoader'], function (scriptLoader) {
                loader = scriptLoader;
                insertScript = spyOn(document.getElementsByTagName('script')[0].parentNode, 'insertBefore');
                done();
            });
        }

        function registerLanguageTest(testCase) {
            it('should ' + testCase.description, function () {
                loader.addReCaptchaScriptTag(testCase.language);

                expect(insertScript).toHaveBeenCalledTimes(1);
                expect(insertScript.calls.mostRecent().args[0].src).toBe(testCase.expectedUrl);
            });
        }

        beforeEach(prepareLoader);

        afterEach(function () {
            injector.clean();
            injector.remove();
        });

        [
            {
                description: 'append the configured language to the API URL',
                language: 'fr-CA',
                expectedUrl: apiUrl + '&hl=fr-CA'
            },
            {
                description: 'encode the language as a single query parameter',
                language: 'fr-CA&render=invalid',
                expectedUrl: apiUrl + '&hl=fr-CA%26render%3Dinvalid'
            },
            {
                description: 'preserve automatic language detection for an empty language',
                language: '',
                expectedUrl: apiUrl
            },
            {
                description: 'preserve automatic language detection when language is omitted',
                language: undefined,
                expectedUrl: apiUrl
            }
        ].forEach(registerLanguageTest);

        it('should insert only one API script across repeated calls', function () {
            loader.addReCaptchaScriptTag('fr-CA');
            loader.addReCaptchaScriptTag('de');
            loader.addReCaptchaScriptTag();

            expect(insertScript).toHaveBeenCalledTimes(1);
            expect(insertScript.calls.mostRecent().args[0].src).toBe(apiUrl + '&hl=fr-CA');
        });
    });
});
