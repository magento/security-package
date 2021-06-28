<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Test\Integration;

use Magento\Framework\App\Request\Http;
use Magento\Framework\DataObject;
use Magento\Framework\Validation\ValidationResult;
use Magento\Paypal\Model\Payflow\Service\Request\SecureToken;
use Magento\ReCaptchaUi\Model\CaptchaResponseResolverInterface;
use Magento\ReCaptchaValidation\Model\Validator;
use Magento\Store\Model\ScopeInterface;
use Magento\TestFramework\App\MutableScopeConfig;
use Magento\TestFramework\TestCase\AbstractController;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @magentoAppArea frontend
 * @magentoAppIsolation enabled
 * @magentoDbIsolation enabled
 */
class CheckoutFormTest extends AbstractController
{
    /**
     * @var MutableScopeConfig
     */
    private $mutableScopeConfig;

    /**
     * @var ValidationResult|MockObject
     */
    private $captchaValidationResultMock;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->mutableScopeConfig = $this->_objectManager->get(MutableScopeConfig::class);

        $this->captchaValidationResultMock = $this->createMock(ValidationResult::class);
        $captchaValidationResultMock = $this->createMock(Validator::class);
        $captchaValidationResultMock->expects($this->any())
            ->method('isValid')
            ->willReturn($this->captchaValidationResultMock);
        $this->_objectManager->addSharedInstance($captchaValidationResultMock, Validator::class);

        $token = new DataObject($this->getPayPalResponse());
        $secureTokenServiceMock = $this->createMock(SecureToken::class);
        $secureTokenServiceMock->expects($this->any())
            ->method('requestToken')
            ->willReturn($token);
        $this->_objectManager->addSharedInstance($secureTokenServiceMock, SecureToken::class);
    }

    /**
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture current_store payment/payflowpro/active 1
     */
    public function testPostRequestIfReCaptchaIsDisabled()
    {
        $this->setConfig(false, 'test_public_key', 'test_private_key');

        $this->checkPostResponse(true, [CaptchaResponseResolverInterface::PARAM_RECAPTCHA => 'test']);
    }

    /**
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/paypal_payflowpro invisible
     * @magentoConfigFixture current_store payment/payflowpro/active 1
     */
    public function testPostRequestIfReCaptchaKeysAreNotConfigured()
    {
        $this->setConfig(true, null, null);

        $this->checkPostResponse(true, [CaptchaResponseResolverInterface::PARAM_RECAPTCHA => 'test']);
    }

    /**
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/paypal_payflowpro invisible
     * @magentoConfigFixture current_store payment/payflowpro/active 1
     */
    public function testPostRequestWithSuccessfulReCaptchaValidation()
    {
        $this->setConfig(true, 'test_public_key', 'test_private_key');
        $this->captchaValidationResultMock->expects($this->once())->method('isValid')->willReturn(true);

        $this->checkPostResponse(
            true,
            [CaptchaResponseResolverInterface::PARAM_RECAPTCHA => 'test']
        );
    }

    /**
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/paypal_payflowpro invisible
     * @magentoConfigFixture current_store payment/payflowpro/active 1
     */
    public function testPostRequestIfReCaptchaParameterIsMissed()
    {
        $this->setConfig(true, 'test_public_key', 'test_private_key');

        $this->checkPostResponse(false);
    }

    /**
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/paypal_payflowpro invisible
     * @magentoConfigFixture current_store payment/payflowpro/active 1
     */
    public function testPostRequestWithFailedReCaptchaValidation()
    {
        $this->setConfig(true, 'test_public_key', 'test_private_key');
        $this->captchaValidationResultMock->expects($this->once())->method('isValid')->willReturn(false);

        $this->checkPostResponse(false, [CaptchaResponseResolverInterface::PARAM_RECAPTCHA => 'test']);
    }

    /**
     * @param bool $isSuccessfulRequest
     * @param array $postValues
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function checkPostResponse(bool $isSuccessfulRequest, array $postValues = [])
    {
        $this->getRequest()
            ->setMethod(Http::METHOD_POST)
            ->setPostValue($postValues);

        $this->dispatch('paypal/transparent/requestSecureToken/');

        $code = $this->getResponse()->getHttpResponseCode();
        $this->assertEquals(
            200,
            $code,
            'Incorrect response code'
        );

        $expected = $isSuccessfulRequest ? $this->getSuccessfulCheckoutResponse() : $this->getFailedCheckoutResponse();

        $this->assertEquals(
            json_encode($expected),
            $this->getResponse()->getContent()
        );
    }

    /**
     * @param bool $isEnabled
     * @param string|null $public
     * @param string|null $private
     */
    private function setConfig(bool $isEnabled, ?string $public, ?string $private): void
    {
        $this->mutableScopeConfig->setValue(
            'recaptcha_frontend/type_for/paypal_payflowpro',
            $isEnabled ? 'invisible' : null,
            ScopeInterface::SCOPE_WEBSITE
        );
        $this->mutableScopeConfig->setValue(
            'recaptcha_frontend/type_invisible/public_key',
            $public,
            ScopeInterface::SCOPE_WEBSITE
        );
        $this->mutableScopeConfig->setValue(
            'recaptcha_frontend/type_invisible/private_key',
            $private,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return array
     */
    private function getPayPalResponse(): array
    {
        return [
            'result' => 'result_value',
            'securetoken' => 'securetoken_value',
            'securetokenid' => 'securetokenid_value',
            'respmsg' => 'respmsg',
            'result_code' => 'result_code_value',
        ];
    }

    /**
     * @return array
     */
    private function getSuccessfulCheckoutResponse(): array
    {
        return [
            'payflowpro' => [
                'fields' => $this->getPayPalResponse(),
            ],
            'success' => true,
            'error' => false,
        ];
    }

    /**
     * @return array
     */
    private function getFailedCheckoutResponse(): array
    {
        return [
            'success' => false,
            'error' => true,
            'error_messages' => 'reCAPTCHA verification failed',
        ];
    }
}
