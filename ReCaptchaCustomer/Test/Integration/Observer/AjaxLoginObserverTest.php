<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Test\Integration\Observer;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Exception\LocalizedException;
use Magento\ReCaptcha\Model\CaptchaValidator;
use Magento\ReCaptchaApi\Api\CaptchaValidatorInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\TestFramework\App\MutableScopeConfig;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\TestFramework\TestCase\AbstractController;
use Zend\Http\Headers;

/**
 * Test for \Magento\ReCaptchaCustomer\Observer\AjaxLoginObserver class.
 *
 * @magentoDataFixture Magento/Customer/_files/customer.php
 * @magentoAppArea frontend
 * @magentoAppIsolation enabled
 * @magentoDbIsolation enabled
 */
class AjaxLoginObserverTest extends AbstractController
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
     * @var CaptchaValidatorInterface|MockObject
     */
    private $captchaValidatorMock;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->mutableScopeConfig = $this->_objectManager->get(MutableScopeConfig::class);
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->response = $this->_objectManager->get(ResponseInterface::class);
        $this->captchaValidatorMock = $this->createMock(CaptchaValidatorInterface::class);
        $this->_objectManager->addSharedInstance($this->captchaValidatorMock, CaptchaValidator::class);
    }

    public function testReCaptchaNotConfigured()
    {
        $this->initConfig(1, null, null);
        $this->sendAjaxLoginRequest(false, 'Login successful.');
    }

    public function testReCaptchaDisabled()
    {
        $this->initConfig(0, 'test_public_key', 'test_private_key');
        $this->sendAjaxLoginRequest(false, 'Login successful.');
    }

    public function testCorrectRecaptcha()
    {
        $this->initConfig(1, 'test_public_key', 'test_private_key');
        $this->captchaValidatorMock->expects($this->once())->method('validate')->willReturn(true);
        $this->sendAjaxLoginRequest(false, 'Login successful.');
    }

    public function testIncorrectRecaptcha()
    {
        $this->initConfig(1, 'test_public_key', 'test_private_key');
        $this->captchaValidatorMock->expects($this->once())->method('validate')->willReturn(false);
        $this->sendAjaxLoginRequest(true, 'You cannot proceed with such operation, your reCaptcha reputation is too low.');
    }

    public function testErrorValidatingRecaptcha()
    {
        $this->initConfig(1, 'test_public_key', 'test_private_key');
        $exception = new LocalizedException(__('error_message'));
        $this->captchaValidatorMock->expects($this->once())->method('validate')->willThrowException($exception);
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('error_message');
        $this->sendAjaxLoginRequest(null, null);
    }

    /**
     * @param bool|null $errors
     * @param string|null $message
     */
    private function sendAjaxLoginRequest(?bool $errors, ?string $message)
    {
        $data = [
            'username' => 'customer@example.com',
            'password' => 'password',
            'captcha_form_id' => 'user_login',
            'context' => 'checkout',
        ];
        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setHeaders(Headers::fromString('X_REQUESTED_WITH: XMLHttpRequest'));
        $this->getRequest()->setContent(json_encode($data));

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
     * @param int|null $enabled
     * @param string|null $public
     * @param string|null $private
     */
    private function initConfig(?int $enabled, ?string $public, ?string $private): void
    {
        $this->mutableScopeConfig->setValue('recaptcha/frontend/enabled_for_customer_login', $enabled, ScopeInterface::SCOPE_WEBSITE);
        $this->mutableScopeConfig->setValue('recaptcha/frontend/public_key', $public, ScopeInterface::SCOPE_WEBSITE);
        $this->mutableScopeConfig->setValue('recaptcha/frontend/private_key', $private, ScopeInterface::SCOPE_WEBSITE);
    }
}
