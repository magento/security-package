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
use Magento\ReCaptchaUi\Model\CaptchaResponseResolverInterface;
use Magento\ReCaptchaValidation\Model\Validator;
use Magento\TestFramework\Mail\Template\TransportBuilderMock;
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
     * @var FormKey
     */
    private $formKey;

    /**
     * @var UrlInterface
     */
    private $backendUrl;

    /**
     * @var TransportBuilderMock
     */
    private $transportMock;

    /**
     * @var Validator|MockObject
     */
    private $captchaValidatorMock;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->backendUrl = $this->_objectManager->get(UrlInterface::class);
        $this->transportMock = $this->_objectManager->get(TransportBuilderMock::class);

        $this->captchaValidatorMock = $this->createMock(Validator::class);
        $this->_objectManager->addSharedInstance($this->captchaValidatorMock, Validator::class);
    }

    /**
     * @magentoAdminConfigFixture admin/captcha/enable 0
     * @magentoAdminConfigFixture recaptcha_backend/type_invisible/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha_backend/type_invisible/private_key test_private_key
     */
    public function testGetRequestIfReCaptchaIsDisabled()
    {
        $this->checkSuccessfulGetResponse();
    }

    /**
     * @magentoAdminConfigFixture admin/captcha/enable 0
     * @magentoAdminConfigFixture recaptcha_backend/type_for/user_forgot_password invisible
     * @magentoConfigFixture default_store recaptcha_backend/type_for/user_forgot_password invisible
     */
    public function testGetRequestIfReCaptchaKeysAreNotConfigured()
    {
        $this->checkSuccessfulGetResponse();
    }

    /**
     * @magentoAdminConfigFixture admin/captcha/enable 0
     * @magentoAdminConfigFixture recaptcha_backend/type_invisible/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha_backend/type_invisible/private_key test_private_key
     * @magentoAdminConfigFixture recaptcha_backend/type_for/user_forgot_password invisible
     * @magentoConfigFixture default_store recaptcha_backend/type_for/user_forgot_password invisible
     */
    public function testGetRequestIfReCaptchaIsEnabled()
    {
        $this->checkSuccessfulGetResponse(true);
    }

    /**
     * @magentoAdminConfigFixture admin/captcha/enable 0
     * @magentoAdminConfigFixture recaptcha_backend/type_invisible/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha_backend/type_invisible/private_key test_private_key
     */
    public function testPostRequestIfReCaptchaIsDisabled()
    {
        $this->checkSuccessfulPostResponse();
    }

    /**
     * @magentoAdminConfigFixture admin/captcha/enable 0
     * @magentoAdminConfigFixture admin/captcha/always_for/backend_forgotpassword 0
     * @magentoAdminConfigFixture recaptcha_backend/type_for/user_forgot_password invisible
     * @magentoConfigFixture default_store recaptcha_backend/type_for/user_forgot_password invisible
     */
    public function testPostRequestIfReCaptchaKeysAreNotConfigured()
    {
        $this->checkSuccessfulPostResponse();
    }

    /**
     * @magentoAdminConfigFixture admin/captcha/enable 0
     * @magentoAdminConfigFixture recaptcha_backend/type_invisible/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha_backend/type_invisible/private_key test_private_key
     * @magentoAdminConfigFixture recaptcha_backend/type_for/user_forgot_password invisible
     * @magentoConfigFixture default_store recaptcha_backend/type_for/user_forgot_password invisible
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
     * @magentoAdminConfigFixture admin/captcha/enable 0
     * @magentoAdminConfigFixture recaptcha_backend/type_invisible/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha_backend/type_invisible/private_key test_private_key
     * @magentoAdminConfigFixture recaptcha_backend/type_for/user_forgot_password invisible
     * @magentoConfigFixture default_store recaptcha_backend/type_for/user_forgot_password invisible
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

        self::assertEmpty($this->transportMock->getSentMessage());
    }

    /**
     * @magentoAdminConfigFixture admin/captcha/enable 0
     * @magentoAdminConfigFixture recaptcha_backend/type_invisible/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha_backend/type_invisible/private_key test_private_key
     * @magentoAdminConfigFixture recaptcha_backend/type_for/user_forgot_password invisible
     * @magentoConfigFixture default_store recaptcha_backend/type_for/user_forgot_password invisible
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
            self::equalTo(['reCAPTCHA verification failed']),
            MessageInterface::TYPE_ERROR
        );
        self::assertEmpty($this->transportMock->getSentMessage());
    }

    /**
     * @param bool $shouldContainReCaptcha
     */
    private function checkSuccessfulGetResponse($shouldContainReCaptcha = false)
    {
        $this->dispatch('backend/admin/auth/forgotpassword');
        $content = $this->getResponse()->getBody();

        self::assertNotEmpty($content);

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

        $message = $this->transportMock->getSentMessage();
        self::assertNotEmpty($message);
        self::assertEquals((string)__('Password Reset Confirmation for %1', ['John Doe']), $message->getSubject());
    }
}
