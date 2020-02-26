<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Test\Integration;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Message\MessageInterface;
use Magento\ReCaptcha\Model\CaptchaValidator;
use Magento\ReCaptchaApi\Api\CaptchaValidatorInterface;
use Magento\ReCaptchaUi\Model\CaptchaResponseResolverInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\TestFramework\App\MutableScopeConfig;
use Magento\TestFramework\TestCase\AbstractController;
use PHPUnit\Framework\MockObject\MockObject;
use Zend\Http\Headers;

/**
 * Test for \Magento\ReCaptchaCustomer\Observer\AjaxLoginObserver class.
 *
 * @magentoDataFixture Magento/Customer/_files/customer.php
 * @magentoAppArea frontend
 * @magentoAppIsolation enabled
 * @magentoDbIsolation enabled
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

    public function testGetRequestIfReCaptchaIsDisabled()
    {
        $this->initConfig(0, 'test_public_key', 'test_private_key');

        $this->checkSuccessfulGetResponse();
    }

    public function testGetRequestIfReCaptchaKeysAreNotConfigured()
    {
        $this->initConfig(1, null, null);

        $this->checkSuccessfulGetResponse();
    }

    public function testGetRequestIfReCaptchaIsEnabled()
    {
        $this->initConfig(1, 'test_public_key', 'test_private_key');

        $this->checkSuccessfulGetResponse(true);
    }

    public function testPostRequestIfReCaptchaKeysAreNotConfigured()
    {
        $this->initConfig(1, null, null);

        $this->checkSuccessfulPostResponse(false, 'Login successful.', [CaptchaResponseResolverInterface::PARAM_RECAPTCHA => 'test_response']);
    }

    public function testPostRequestIfReCaptchaIsDisabled()
    {
        $this->initConfig(0, 'test_public_key', 'test_private_key');

        $this->checkSuccessfulPostResponse(false, 'Login successful.', [CaptchaResponseResolverInterface::PARAM_RECAPTCHA => 'test_response']);
    }

    public function testPostRequestWithSuccessfulReCaptchaValidation()
    {
        $this->initConfig(1, 'test_public_key', 'test_private_key');
        $this->captchaValidatorMock->expects($this->once())->method('isValid')->willReturn(true);

        $this->checkSuccessfulPostResponse(false, 'Login successful.', [CaptchaResponseResolverInterface::PARAM_RECAPTCHA => 'test_response']);
    }

    public function testPostRequestWithFailedReCaptchaValidation()
    {
        $this->initConfig(1, 'test_public_key', 'test_private_key');
        $this->captchaValidatorMock->expects($this->once())->method('isValid')->willReturn(false);

        $this->checkSuccessfulPostResponse(true, 'You cannot proceed with such operation, your reCAPTCHA reputation is too low.', [CaptchaResponseResolverInterface::PARAM_RECAPTCHA => 'test_response']);
    }

    public function testPostRequestIfReCaptchaParameterIsMissed()
    {
        $this->initConfig(1, 'test_public_key', 'test_private_key');
        $this->captchaValidatorMock->expects($this->never())->method('isValid');
        $this->expectException(InputException::class);
        $this->expectExceptionMessage('Can not resolve reCAPTCHA response.');

        $this->checkSuccessfulPostResponse(null, null);
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
    private function checkSuccessfulPostResponse(?bool $errors, ?string $message, array $postValues = [])
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
        $this->mutableScopeConfig->setValue('recaptcha/frontend/type', 'invisible', ScopeInterface::SCOPE_WEBSITE);
        $this->mutableScopeConfig->setValue('recaptcha/frontend/enabled_for_customer_login', $enabled, ScopeInterface::SCOPE_WEBSITE);
        $this->mutableScopeConfig->setValue('recaptcha/frontend/public_key', $public, ScopeInterface::SCOPE_WEBSITE);
        $this->mutableScopeConfig->setValue('recaptcha/frontend/private_key', $private, ScopeInterface::SCOPE_WEBSITE);
    }
}
