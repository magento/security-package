<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUser\Test\Integration;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\Validation\ValidationResult;
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
     * @var ValidationResult|MockObject
     */
    private $captchaValidationResultMock;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->backendUrl = $this->_objectManager->get(UrlInterface::class);
        $this->transportMock = $this->_objectManager->get(TransportBuilderMock::class);

        $this->captchaValidationResultMock = $this->createMock(ValidationResult::class);
        $captchaValidatorMock = $this->createMock(Validator::class);
        $captchaValidatorMock->expects($this->any())
            ->method('isValid')
            ->willReturn($this->captchaValidationResultMock);
        $this->_objectManager->addSharedInstance($captchaValidatorMock, Validator::class);
    }

    /**
     * @magentoAdminConfigFixture admin/captcha/enable 0
     * @magentoAdminConfigFixture recaptcha_backend/type_invisible/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha_backend/type_invisible/private_key test_private_key
     */
    public function testGetRequestIfReCaptchaIsDisabled(): void
    {
        $this->checkSuccessfulGetResponse();
    }

    /**
     * @magentoAdminConfigFixture admin/captcha/enable 0
     * @magentoAdminConfigFixture recaptcha_backend/type_for/user_forgot_password invisible
     *
     * It's  needed for proper work of "ifconfig" in layout during tests running
     * @magentoConfigFixture default_store recaptcha_backend/type_for/user_forgot_password invisible
     */
    public function testGetRequestIfReCaptchaKeysAreNotConfigured(): void
    {
        $this->checkSuccessfulGetResponse();
    }

    /**
     * @magentoAdminConfigFixture admin/captcha/enable 0
     * @magentoAdminConfigFixture recaptcha_backend/type_invisible/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha_backend/type_invisible/private_key test_private_key
     * @magentoAdminConfigFixture recaptcha_backend/type_for/user_forgot_password invisible
     *
     * It's  needed for proper work of "ifconfig" in layout during tests running
     * @magentoConfigFixture default_store recaptcha_backend/type_for/user_forgot_password invisible
     */
    public function testGetRequestIfReCaptchaIsEnabled(): void
    {
        $this->checkSuccessfulGetResponse(true);
    }

    /**
     * @magentoAdminConfigFixture admin/captcha/enable 0
     * @magentoAdminConfigFixture recaptcha_backend/type_invisible/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha_backend/type_invisible/private_key test_private_key
     */
    public function testPostRequestIfReCaptchaIsDisabled(): void
    {
        $this->checkSuccessfulPostResponse();
    }

    /**
     * @magentoAdminConfigFixture admin/captcha/enable 0
     * @magentoAdminConfigFixture admin/captcha/always_for/backend_forgotpassword 0
     * @magentoAdminConfigFixture recaptcha_backend/type_for/user_forgot_password invisible
     */
    public function testPostRequestIfReCaptchaKeysAreNotConfigured(): void
    {
        $this->checkSuccessfulPostResponse();
    }

    /**
     * @magentoAdminConfigFixture admin/captcha/enable 0
     * @magentoAdminConfigFixture recaptcha_backend/type_invisible/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha_backend/type_invisible/private_key test_private_key
     * @magentoAdminConfigFixture recaptcha_backend/type_for/user_forgot_password invisible
     */
    public function testPostRequestWithSuccessfulReCaptchaValidation(): void
    {
        $this->captchaValidationResultMock->expects($this->once())->method('isValid')->willReturn(true);

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
     */
    public function testPostRequestIfReCaptchaParameterIsMissed(): void
    {
        $this->checkFailedPostResponse();
    }

    /**
     * @magentoAdminConfigFixture admin/captcha/enable 0
     * @magentoAdminConfigFixture recaptcha_backend/type_invisible/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha_backend/type_invisible/private_key test_private_key
     * @magentoAdminConfigFixture recaptcha_backend/type_for/user_forgot_password invisible
     */
    public function testPostRequestWithFailedReCaptchaValidation(): void
    {
        $this->captchaValidationResultMock->expects($this->once())->method('isValid')->willReturn(false);

        $this->checkFailedPostResponse([CaptchaResponseResolverInterface::PARAM_RECAPTCHA => 'test']);
    }

    /**
     * @param bool $shouldContainReCaptcha
     * @return void
     */
    private function checkSuccessfulGetResponse($shouldContainReCaptcha = false): void
    {
        $this->dispatch('backend/admin/auth/forgotpassword');
        $content = $this->getResponse()->getBody();

        self::assertNotEmpty($content);

        $shouldContainReCaptcha
            ? $this->assertStringContainsString('admin-recaptcha', $content)
            : $this->assertStringNotContainsString('admin-recaptcha', $content);

        self::assertEmpty($this->getSessionMessages(MessageInterface::TYPE_ERROR));
    }

    /**
     * @param array $postValues
     * @return void
     */
    private function checkSuccessfulPostResponse(array $postValues = []): void
    {
        $this->makePostRequest($postValues);

        $this->assertRedirect(self::equalTo($this->backendUrl->getRouteUrl('adminhtml')));
        self::assertEmpty($this->getSessionMessages(MessageInterface::TYPE_ERROR));

        $message = $this->transportMock->getSentMessage();
        self::assertNotEmpty($message);
        self::assertEquals((string)__('Password Reset Confirmation for %1', ['John Doe']), $message->getSubject());
    }

    /**
     * @param array $postValues
     * @return void
     */
    private function checkFailedPostResponse(array $postValues = []): void
    {
        $this->makePostRequest($postValues);

        $this->assertSessionMessages(
            self::equalTo(['Something went wrong with reCAPTCHA. Please contact the store owner.']),
            MessageInterface::TYPE_ERROR
        );
        self::assertEmpty($this->transportMock->getSentMessage());
    }

    /**
     * @param array $postValues
     * @return void
     */
    private function makePostRequest(array $postValues = []): void
    {
        $this->getRequest()
            ->setMethod(Http::METHOD_POST)
            ->setPostValue(array_replace_recursive(
                [
                    'form_key' => $this->formKey->getFormKey(),
                    'email' => 'adminUser@example.com',
                ],
                $postValues
            ));
        $this->dispatch('backend/admin/auth/forgotpassword');
    }
}
