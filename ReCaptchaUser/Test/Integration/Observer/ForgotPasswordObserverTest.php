<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUser\Test\Integration\Observer;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Exception\LocalizedException;
use Magento\ReCaptcha\Model\CaptchaValidator;
use Magento\ReCaptchaApi\Api\CaptchaValidatorInterface;
use Magento\User\Model\User;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\TestFramework\TestCase\AbstractController;

/**
 * Test for \Magento\ReCaptchaUser\Observer\ForgotPasswordObserver class.
 *
 * @magentoDataFixture ../../../../app/code/Magento/ReCaptchaUser/Test/Integration/_files/dummy_user.php
 * @magentoAppArea adminhtml
 * @magentoAppIsolation enabled
 * @magentoDbIsolation enabled
 */
class ForgotPasswordObserverTest extends AbstractController
{
    /**
     * @var User
     */
    private $user;

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
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->response = $this->_objectManager->get(ResponseInterface::class);
        $this->captchaValidatorMock = $this->createMock(CaptchaValidatorInterface::class);
        $this->user = $this->_objectManager->get(User::class);
        $this->_objectManager->addSharedInstance($this->captchaValidatorMock, CaptchaValidator::class);
    }

    /**
     * @magentoAdminConfigFixture admin/captcha/always_for/backend_forgotpassword 0
     * @magentoAdminConfigFixture recaptcha/backend/enabled_for_user_forgot_password 1
     */
    public function testReCaptchaNotConfigured()
    {
        $this->sendForgotPasswordRequest(false);
    }

    /**
     * @magentoAdminConfigFixture admin/captcha/always_for/backend_forgotpassword 0
     * @magentoAdminConfigFixture recaptcha/backend/enabled_for_user_forgot_password 0
     * @magentoAdminConfigFixture recaptcha/backend/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha/backend/private_key test_private_key
     */
    public function testReCaptchaDisabled()
    {
        $this->sendForgotPasswordRequest(false);
    }

    /**
     * @magentoAdminConfigFixture admin/captcha/always_for/backend_forgotpassword 0
     * @magentoAdminConfigFixture recaptcha/backend/enabled_for_user_forgot_password 1
     * @magentoAdminConfigFixture recaptcha/backend/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha/backend/private_key test_private_key
     */
    public function testCorrectRecaptcha()
    {
        $this->captchaValidatorMock->expects($this->once())->method('validate')->willReturn(true);
        $this->sendForgotPasswordRequest(false);
    }

    /**
     * @magentoAdminConfigFixture admin/captcha/always_for/backend_forgotpassword 0
     * @magentoAdminConfigFixture recaptcha/backend/enabled_for_user_forgot_password 1
     * @magentoAdminConfigFixture recaptcha/backend/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha/backend/private_key test_private_key
     */
    public function testIncorrectRecaptcha()
    {
        $this->captchaValidatorMock->expects($this->once())->method('validate')->willReturn(false);
        $this->sendForgotPasswordRequest(true);
    }

    /**
     * @magentoAdminConfigFixture admin/captcha/always_for/backend_forgotpassword 0
     * @magentoAdminConfigFixture recaptcha/backend/enabled_for_user_forgot_password 1
     * @magentoAdminConfigFixture recaptcha/backend/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha/backend/private_key test_private_key
     */
    public function testErrorValidatingRecaptcha()
    {
        $exception = new LocalizedException(__('error_message'));
        $this->captchaValidatorMock->expects($this->once())->method('validate')->willThrowException($exception);
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage($exception->getMessage());
        $this->sendForgotPasswordRequest(true);
    }

    /**
     * @param bool $result
     * @throws LocalizedException
     */
    private function sendForgotPasswordRequest(bool $result)
    {
        $userName = 'dummy_username';
        $userInitialToken = $this->getUserRpToken($userName);

        $this->getRequest()->setPostValue(
            [
                'email' => 'dummy@dummy.com',
                'form_key' => $this->formKey->getFormKey(),
                'g-recaptcha-response' => 'test_response'
            ]
        );

        $this->dispatch('backend/admin/auth/forgotpassword');

        $code = $this->response->getHttpResponseCode();
        $this->assertEquals(
            302,
            $code,
            'Incorrect response code'
        );

        $newUserToken = $this->getUserRpToken($userName);
        $this->assertEquals(
            $result,
            $newUserToken === $userInitialToken);
    }

    /**
     * @param string $userName
     * @return string|null
     */
    private function getUserRpToken(string $userName): ?string
    {
        $user = $this->user->loadByUsername($userName);
        return $user->getRpToken();
    }
}
