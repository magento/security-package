<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUser\Test\Integration\Observer;

use Magento\Backend\Model\Auth;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Exception\LocalizedException;
use Magento\ReCaptcha\Model\CaptchaValidator;
use Magento\ReCaptchaApi\Api\CaptchaValidatorInterface;
use Magento\TestFramework\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\TestFramework\TestCase\AbstractController;

/**
 * Test for \Magento\ReCaptchaUser\Observer\LoginObserver class.
 *
 * @magentoAppArea adminhtml
 * @magentoAppIsolation enabled
 * @magentoDbIsolation enabled
 */
class LoginObserverTest extends AbstractController
{
    /**
     * @var Auth
     */
    private $auth;

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
        $this->auth = $this->_objectManager->get(Auth::class);
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->response = $this->_objectManager->get(ResponseInterface::class);
        $this->captchaValidatorMock = $this->createMock(CaptchaValidatorInterface::class);
        $this->_objectManager->addSharedInstance($this->captchaValidatorMock, CaptchaValidator::class);
    }

    /**
     * @magentoAdminConfigFixture recaptcha/backend/enabled_for_user_login 1
     */
    public function testReCaptchaNotConfigured()
    {
        $this->sendLoginRequest(true);
    }

    /**
     * @magentoAdminConfigFixture recaptcha/backend/enabled_for_user_login 0
     * @magentoAdminConfigFixture recaptcha/backend/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha/backend/private_key test_private_key
     */
    public function testReCaptchaDisabled()
    {
        $this->sendLoginRequest(true);
    }

    /**
     * @magentoAdminConfigFixture recaptcha/backend/enabled_for_user_login 1
     * @magentoAdminConfigFixture recaptcha/backend/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha/backend/private_key test_private_key
     */
    public function testCorrectRecaptcha()
    {
        $this->captchaValidatorMock->expects($this->once())->method('validate')->willReturn(true);
        $this->sendLoginRequest(true);
    }

    /**
     * @magentoAdminConfigFixture recaptcha/backend/enabled_for_user_login 1
     * @magentoAdminConfigFixture recaptcha/backend/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha/backend/private_key test_private_key
     */
    public function testIncorrectRecaptcha()
    {
        $this->captchaValidatorMock->expects($this->once())->method('validate')->willReturn(false);
        $this->sendLoginRequest(false);
    }

    /**
     * @magentoAdminConfigFixture recaptcha/backend/enabled_for_user_login 1
     * @magentoAdminConfigFixture recaptcha/backend/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha/backend/private_key test_private_key
     */
    public function testErrorValidatingRecaptcha()
    {
        $exception = new LocalizedException(__('error_message'));
        $this->captchaValidatorMock->expects($this->once())->method('validate')->willThrowException($exception);
        $this->sendLoginRequest(false);
    }

    /**
     * @param bool $result
     * @throws LocalizedException
     */
    private function sendLoginRequest(bool $result)
    {
        $this->getRequest()->setPostValue(
            [
                'login' => [
                    'username' => Bootstrap::ADMIN_NAME,
                    'password' => Bootstrap::ADMIN_PASSWORD,
                ],
                'form_key' => $this->formKey->getFormKey(),
                'g-recaptcha-response' => 'test_response'
            ]
        );

        $this->dispatch('backend/admin/index/index');

        $code = $this->response->getHttpResponseCode();
        $this->assertEquals(
            302,
            $code,
            'Incorrect response code'
        );
        $this->assertEquals(
            $result,
            $this->auth->isLoggedIn()
        );
    }
}
