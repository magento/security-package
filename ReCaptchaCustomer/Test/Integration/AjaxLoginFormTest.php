<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Test\Integration;

use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\Validation\ValidationResult;
use Magento\ReCaptchaUi\Model\CaptchaResponseResolverInterface;
use Magento\ReCaptchaValidation\Model\Validator;
use Magento\Store\Model\ScopeInterface;
use Magento\TestFramework\App\MutableScopeConfig;
use Magento\TestFramework\TestCase\AbstractController;
use PHPUnit\Framework\MockObject\MockObject;
use Zend\Http\Headers;

/**
 * @magentoDataFixture Magento/Customer/_files/customer.php
 * @magentoAppArea frontend
 * @magentoAppIsolation enabled
 */
class AjaxLoginFormTest extends AbstractController
{
    /**
     * @var MutableScopeConfig
     */
    private $mutableScopeConfig;

    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * @var ResponseInterface
     */
    private $response;

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
        $this->formKey = $this->_objectManager->get(FormKey::class);

        $this->response = $this->_objectManager->get(ResponseInterface::class);
        $this->captchaValidationResultMock = $this->createMock(ValidationResult::class);
        $captchaValidationResultMock = $this->createMock(Validator::class);
        $captchaValidationResultMock->expects($this->any())
            ->method('isValid')
            ->willReturn($this->captchaValidationResultMock);
        $this->_objectManager->addSharedInstance($captchaValidationResultMock, Validator::class);
    }

    /**
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     */
    public function testGetRequestIfReCaptchaIsDisabled()
    {
        $this->setConfig(false, 'test_public_key', 'test_private_key');

        $this->checkSuccessfulGetResponse();
    }

    /**
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/customer_login invisible
     *
     * It's  needed for proper work of "ifconfig" in layout during tests running
     * @magentoConfigFixture default_store recaptcha_frontend/type_for/customer_login invisible
     */
    public function testGetRequestIfReCaptchaKeysAreNotConfigured()
    {
        $this->setConfig(true, null, null);

        $this->checkSuccessfulGetResponse();
    }

    /**
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/customer_login invisible
     *
     * It's  needed for proper work of "ifconfig" in layout during tests running
     * @magentoConfigFixture default_store recaptcha_frontend/type_for/customer_login invisible
     */
    public function testGetRequestIfReCaptchaIsEnabled()
    {
        $this->setConfig(true, 'test_public_key', 'test_private_key');

        $this->checkSuccessfulGetResponse(true);
    }

    /**
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     */
    public function testPostRequestIfReCaptchaIsDisabled()
    {
        $this->setConfig(false, 'test_public_key', 'test_private_key');

        $this->checkPostResponse(false, 'Login successful.', [CaptchaResponseResolverInterface::PARAM_RECAPTCHA => 'test']);
    }

    /**
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/customer_login invisible
     */
    public function testPostRequestIfReCaptchaKeysAreNotConfigured()
    {
        $this->setConfig(true, null, null);

        $this->checkPostResponse(false, 'Login successful.', [CaptchaResponseResolverInterface::PARAM_RECAPTCHA => 'test']);
    }

    public function testPostRequestWithSuccessfulReCaptchaValidation()
    {
        $this->setConfig(true, 'test_public_key', 'test_private_key');
        $this->captchaValidationResultMock->expects($this->once())->method('isValid')->willReturn(true);

        $this->checkPostResponse(false, 'Login successful.', [CaptchaResponseResolverInterface::PARAM_RECAPTCHA => 'test']);
    }

    /**
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/customer_login invisible
     */
    public function testPostRequestIfReCaptchaParameterIsMissed()
    {
        $this->setConfig(true, 'test_public_key', 'test_private_key');

        $this->expectException(InputException::class);
        $this->expectExceptionMessage('Can not resolve reCAPTCHA response.');

        $this->checkPostResponse(null, null);
    }

    /**
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/customer_login invisible
     */
    public function testPostRequestWithFailedReCaptchaValidation()
    {
        $this->setConfig(true, 'test_public_key', 'test_private_key');
        $this->captchaValidationResultMock->expects($this->once())->method('isValid')->willReturn(false);

        $this->checkPostResponse(true, 'reCAPTCHA verification failed', [CaptchaResponseResolverInterface::PARAM_RECAPTCHA => 'test']);
    }

    /**
     * @param bool $shouldContainReCaptcha
     */
    private function checkSuccessfulGetResponse($shouldContainReCaptcha = false)
    {
        $this->dispatch('/');
        $content = $this->getResponse()->getBody();

        self::assertNotEmpty($content);

        $shouldContainReCaptcha
            ? $this->assertContains('recaptcha-popup-login', $content)
            : $this->assertNotContains('recaptcha-popup-login', $content);

        self::assertEmpty($this->getSessionMessages(MessageInterface::TYPE_ERROR));
    }

    /**
     * @param bool|null $errors
     * @param string|null $message
     */
    private function checkPostResponse(?bool $errors, ?string $message, array $postValues = [])
    {
        $data = array_replace_recursive(
            [
                'username' => 'customer@example.com',
                'password' => 'password',
                'captcha_form_id' => 'user_login',
                'context' => 'checkout',
            ],
            $postValues
        );
        $this->getRequest()
            ->setMethod(Http::METHOD_POST)
            ->setHeaders(Headers::fromString('X_REQUESTED_WITH: XMLHttpRequest'))
            ->setContent(json_encode($data));

        $this->dispatch('customer/ajax/login');

        $code = $this->response->getHttpResponseCode();
        $this->assertEquals(
            200,
            $code,
            'Incorrect response code'
        );

        $expected = json_encode(['errors' => $errors, 'message' => $message]);

        $this->assertEquals(
            $expected,
            $this->response->getContent()
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
            'recaptcha_frontend/type_for/customer_login',
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
}
