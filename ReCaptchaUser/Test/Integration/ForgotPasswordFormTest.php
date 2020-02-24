<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUser\Test\Integration;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Message\MessageInterface;
use Magento\ReCaptcha\Model\CaptchaValidator;
use Magento\ReCaptchaApi\Api\CaptchaValidatorInterface;
use Magento\ReCaptchaUi\Model\CaptchaResponseResolverInterface;
use Magento\TestFramework\Mail\Template\TransportBuilderMock;
use Magento\User\Model\User;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\TestFramework\TestCase\AbstractController;

/**
 * @magentoAppArea adminhtml
 * @magentoAppIsolation enabled
 * @magentoDataFixture Magento/User/_files/user_with_role.php
 */
class ForgotPasswordFormTest extends AbstractController
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
     * @var UrlInterface
     */
    private $backendUrl;

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
        $this->user = $this->_objectManager->get(User::class);
        $this->backendUrl = $this->_objectManager->get(UrlInterface::class);

        $this->captchaValidatorMock = $this->createMock(CaptchaValidatorInterface::class);
        $this->_objectManager->addSharedInstance($this->captchaValidatorMock, CaptchaValidator::class);
    }

    /**
     * @magentoAdminConfigFixture admin/captcha/always_for/backend_forgotpassword 0
     * @magentoAdminConfigFixture recaptcha/backend/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha/backend/private_key test_private_key
     * @magentoAdminConfigFixture recaptcha/backend/type invisible
     * @magentoAdminConfigFixture recaptcha/backend/enabled_for_user_forgot_password 0
     */
    public function testGetRequestIfReCaptchaIsDisabled()
    {
        $this->checkSuccessfulGetResponse();
    }

    /**
     * @magentoAdminConfigFixture admin/captcha/always_for/backend_forgotpassword 0
     * @magentoAdminConfigFixture recaptcha/backend/type invisible
     * @magentoAdminConfigFixture recaptcha/backend/enabled_for_user_forgot_password 1
     */
    public function testGetRequestIfReCaptchaKeysAreNotConfigured()
    {
        $this->checkSuccessfulGetResponse();
    }

    /**
     * @magentoAdminConfigFixture admin/captcha/always_for/backend_forgotpassword 0
     * @magentoAdminConfigFixture recaptcha/backend/type invisible
     * @magentoAdminConfigFixture recaptcha/backend/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha/backend/private_key test_private_key
     * @magentoAdminConfigFixture recaptcha/backend/enabled_for_user_forgot_password 1
     */
    public function testGetRequestIfReCaptchaIsEnabled()
    {
        $this->checkSuccessfulGetResponse(true);
    }

    /**
     * @magentoAdminConfigFixture admin/captcha/always_for/backend_forgotpassword 0
     * @magentoAdminConfigFixture recaptcha/backend/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha/backend/private_key test_private_key
     * @magentoAdminConfigFixture recaptcha/backend/type invisible
     * @magentoAdminConfigFixture recaptcha/backend/enabled_for_user_forgot_password 0
     */
    public function testPostRequestIfReCaptchaIsDisabled()
    {
        $this->checkSuccessfulPostResponse();
    }

    /**
     * @magentoAdminConfigFixture recaptcha/backend/type invisible
     * @magentoAdminConfigFixture admin/captcha/always_for/backend_forgotpassword 0
     * @magentoAdminConfigFixture recaptcha/backend/enabled_for_user_forgot_password 1
     */
    public function testPostRequestIfReCaptchaKeysAreNotConfigured()
    {
        $this->checkSuccessfulPostResponse();
    }

    /**
     * @magentoAdminConfigFixture admin/captcha/always_for/backend_forgotpassword 0
     * @magentoAdminConfigFixture recaptcha/backend/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha/backend/private_key test_private_key
     * @magentoAdminConfigFixture recaptcha/backend/type invisible
     * @magentoAdminConfigFixture recaptcha/backend/enabled_for_user_forgot_password 1
     */
    public function testPostRequestWithSuccessfulReCaptchaValidation()
    {
        $this->captchaValidatorMock->expects($this->once())->method('isValid')->willReturn(true);
        $this->checkSuccessfulPostResponse(
            [
                CaptchaResponseResolverInterface::PARAM_RECAPTCHA => 'test',
            ]
        );
    }

    /**
     * @magentoAdminConfigFixture admin/captcha/always_for/backend_forgotpassword 0
     * @magentoAdminConfigFixture recaptcha/backend/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha/backend/private_key test_private_key
     * @magentoAdminConfigFixture recaptcha/backend/type invisible
     * @magentoAdminConfigFixture recaptcha/backend/enabled_for_user_forgot_password 1
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage Can not resolve reCAPTCHA parameter.
     */
    public function testPostRequestIfReCaptchaParameterIsMissed()
    {
        $this->getRequest()->setPostValue(
            [
                'form_key' => $this->formKey->getFormKey(),
                'email' => 'adminUser@example.com'
            ]
        );
        $this->dispatch('backend/admin/auth/forgotpassword');
    }

    /**
     * @magentoAdminConfigFixture admin/captcha/always_for/backend_forgotpassword 0
     * @magentoAdminConfigFixture recaptcha/backend/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha/backend/private_key test_private_key
     * @magentoAdminConfigFixture recaptcha/backend/type invisible
     * @magentoAdminConfigFixture recaptcha/backend/enabled_for_user_forgot_password 1
     */
    public function testPostRequestWithFailedReCaptchaValidation()
    {
        $this->captchaValidatorMock->expects($this->once())->method('isValid')->willReturn(false);
        $this->getRequest()->setPostValue(
            [
                'form_key' => $this->formKey->getFormKey(),
                'email' => 'adminUser@example.com',
                CaptchaResponseResolverInterface::PARAM_RECAPTCHA => 'test',
            ]
        );

        $this->dispatch('backend/admin/auth/forgotpassword');

        $this->assertSessionMessages(
            self::equalTo(['Incorrect reCAPTCHA validation.']),
            MessageInterface::TYPE_ERROR
        );
    }

    /**
     * @param bool $shouldContainReCaptcha
     */
    private function checkSuccessfulGetResponse($shouldContainReCaptcha = false)
    {
        $this->dispatch('backend/admin/auth/forgotpassword');
        $content = $this->getResponse()->getBody();

        $shouldContainReCaptcha
            ? $this->assertContains('admin-recaptcha', $content)
            : $this->assertNotContains('admin-recaptcha', $content);

        self::assertEmpty($this->getSessionMessages(MessageInterface::TYPE_ERROR));
    }

    /**
     * @param array $postValues
     */
    private function checkSuccessfulPostResponse(array $postValues = [])
    {
        $this->getRequest()->setPostValue(array_replace_recursive(
            [
                'form_key' => $this->formKey->getFormKey(),
                'email' => 'adminUser@example.com',
            ],
            $postValues
        ));
        $this->dispatch('backend/admin/auth/forgotpassword');

        $this->assertRedirect(self::equalTo($this->backendUrl->getRouteUrl('adminhtml')));
        self::assertEmpty($this->getSessionMessages(MessageInterface::TYPE_ERROR));

        /** @var TransportBuilderMock $transportMock */
        $transportMock = $this->_objectManager->get(TransportBuilderMock::class);
        $message = $transportMock->getSentMessage();
        self::assertNotEmpty($message);
        self::assertEquals((string)__('Password Reset Confirmation for %1', ['John Doe']), $message->getSubject());
    }
}
