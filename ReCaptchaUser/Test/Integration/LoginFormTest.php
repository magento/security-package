<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUser\Test\Integration;

use Magento\Backend\Model\Auth;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Message\MessageInterface;
use Magento\ReCaptcha\Model\CaptchaValidator;
use Magento\ReCaptchaApi\Api\CaptchaValidatorInterface;
use Magento\ReCaptchaUi\Model\CaptchaResponseResolverInterface;
use Magento\TestFramework\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Should be used "admin/security/use_form_key 0" since \Magento\Backend\Model\UrlInterface is initialized
 * several times (each new instance generates different secret keys)
 *
 * @magentoAppArea adminhtml
 * @magentoAppIsolation enabled
 */
class LoginFormTest extends AbstractController
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
        $this->auth = $this->_objectManager->get(Auth::class);
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->backendUrl = $this->_objectManager->get(UrlInterface::class);

        $this->captchaValidatorMock = $this->createMock(CaptchaValidatorInterface::class);
        $this->_objectManager->addSharedInstance($this->captchaValidatorMock, CaptchaValidator::class);
    }

    /**
     * @magentoAdminConfigFixture admin/security/use_form_key 0
     * @magentoAdminConfigFixture admin/captcha/enable 0
     * @magentoAdminConfigFixture recaptcha/backend/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha/backend/private_key test_private_key
     * @magentoAdminConfigFixture recaptcha/backend/type invisible
     * @magentoAdminConfigFixture recaptcha/backend/enabled_for_user_login 0
     */
    public function testGetRequestIfReCaptchaIsDisabled()
    {
        $this->checkSuccessfulGetResponse();
    }

    /**
     * @magentoAdminConfigFixture admin/security/use_form_key 0
     * @magentoAdminConfigFixture admin/captcha/enable 0
     * @magentoAdminConfigFixture recaptcha/backend/type invisible
     * @magentoAdminConfigFixture recaptcha/backend/enabled_for_user_login 1
     */
    public function testGetRequestIfReCaptchaKeysAreNotConfigured()
    {
        $this->checkSuccessfulGetResponse();
    }

    /**
     * @magentoAdminConfigFixture admin/security/use_form_key 0
     * @magentoAdminConfigFixture admin/captcha/enable 0
     * @magentoAdminConfigFixture recaptcha/backend/type invisible
     * @magentoAdminConfigFixture recaptcha/backend/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha/backend/private_key test_private_key
     * @magentoAdminConfigFixture recaptcha/backend/enabled_for_user_login 1
     */
    public function testGetRequestIfReCaptchaIsEnabled()
    {
        $this->checkSuccessfulGetResponse(true);
    }

    /**
     * @magentoAdminConfigFixture admin/security/use_form_key 0
     * @magentoAdminConfigFixture admin/captcha/enable 0
     * @magentoAdminConfigFixture recaptcha/backend/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha/backend/private_key test_private_key
     * @magentoAdminConfigFixture recaptcha/backend/type invisible
     * @magentoAdminConfigFixture recaptcha/backend/enabled_for_user_login 0
     */
    public function testPostRequestIfReCaptchaIsDisabled()
    {
        $this->checkSuccessfulPostResponse();
    }

    /**
     * @magentoAdminConfigFixture admin/security/use_form_key 0
     * @magentoAdminConfigFixture admin/captcha/enable 0
     * @magentoAdminConfigFixture recaptcha/backend/type invisible
     * @magentoAdminConfigFixture recaptcha/backend/enabled_for_user_login 1
     */
    public function testPostRequestIfReCaptchaKeysAreNotConfigured()
    {
        $this->checkSuccessfulPostResponse();
    }

    /**
     * @magentoAdminConfigFixture admin/security/use_form_key 0
     * @magentoAdminConfigFixture admin/captcha/enable 0
     * @magentoAdminConfigFixture recaptcha/backend/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha/backend/private_key test_private_key
     * @magentoAdminConfigFixture recaptcha/backend/type invisible
     * @magentoAdminConfigFixture recaptcha/backend/enabled_for_user_login 1
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
     * @magentoAdminConfigFixture admin/security/use_form_key 0
     * @magentoAdminConfigFixture admin/captcha/enable 0
     * @magentoAdminConfigFixture recaptcha/backend/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha/backend/private_key test_private_key
     * @magentoAdminConfigFixture recaptcha/backend/type invisible
     * @magentoAdminConfigFixture recaptcha/backend/enabled_for_user_login 1
     */
    public function testPostRequestIfReCaptchaParameterIsMissed()
    {
        $this->getRequest()->setPostValue(
            [
                'form_key' => $this->formKey->getFormKey(),
                'login' => [
                    'username' => Bootstrap::ADMIN_NAME,
                    'password' => Bootstrap::ADMIN_PASSWORD,
                ],
            ]
        );
        $this->dispatch('backend/admin/index/index');

        // Location header is different than in the successful case
        $this->assertRedirect(self::equalTo($this->backendUrl->getUrl('admin')));
        $this->assertSessionMessages(
            self::equalTo(['Can not resolve reCAPTCHA parameter.']),
            MessageInterface::TYPE_ERROR
        );
        self::assertFalse($this->auth->isLoggedIn());
    }

    /**
     * @magentoAdminConfigFixture admin/security/use_form_key 0
     * @magentoAdminConfigFixture admin/captcha/enable 0
     * @magentoAdminConfigFixture recaptcha/backend/public_key test_public_key
     * @magentoAdminConfigFixture recaptcha/backend/private_key test_private_key
     * @magentoAdminConfigFixture recaptcha/backend/type invisible
     * @magentoAdminConfigFixture recaptcha/backend/enabled_for_user_login 1
     */
    public function testPostRequestWithFailedReCaptchaValidation()
    {
        $this->captchaValidatorMock->expects($this->once())->method('isValid')->willReturn(false);

        $this->getRequest()->setPostValue(
            [
                'form_key' => $this->formKey->getFormKey(),
                'login' => [
                    'username' => Bootstrap::ADMIN_NAME,
                    'password' => Bootstrap::ADMIN_PASSWORD,
                ],
                CaptchaResponseResolverInterface::PARAM_RECAPTCHA => 'test',
            ]
        );
        $this->dispatch('backend/admin/index/index');

        // Location header is different than in the successful case
        $this->assertRedirect(self::equalTo($this->backendUrl->getUrl('admin')));
        $this->assertSessionMessages(
            self::equalTo(['Incorrect reCAPTCHA validation.']),
            MessageInterface::TYPE_ERROR
        );
        self::assertFalse($this->auth->isLoggedIn());
    }

    /**
     * @param bool $shouldContainReCaptcha
     */
    private function checkSuccessfulGetResponse($shouldContainReCaptcha = false)
    {
        $this->getRequest()->setUri($this->backendUrl->getUrl('admin'));

        $this->dispatch('backend/admin/auth/login');
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
                'login' => [
                    'username' => Bootstrap::ADMIN_NAME,
                    'password' => Bootstrap::ADMIN_PASSWORD,
                ],
            ],
            $postValues
        ));
        $this->dispatch('backend/admin/index/index');

        $this->assertRedirect(self::equalTo('backend/admin/index/index'));
        self::assertEmpty($this->getSessionMessages(MessageInterface::TYPE_ERROR));
        self::assertTrue($this->auth->isLoggedIn());
    }
}
