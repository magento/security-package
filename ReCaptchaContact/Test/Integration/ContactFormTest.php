<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaContact\Test\Integration;

use Magento\Framework\App\Request\Http as HttpRequest;
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

/**
 * @magentoAppArea frontend
 * @magentoAppIsolation enabled
 */
class ContactFormTest extends AbstractController
{
    /**
     * @var FormKey
     */
    private $formKey;

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
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->mutableScopeConfig = $this->_objectManager->get(MutableScopeConfig::class);

        $this->captchaValidationResultMock = $this->createMock(ValidationResult::class);
        $captchaValidatorMock = $this->createMock(Validator::class);
        $captchaValidatorMock->expects($this->any())
            ->method('isValid')
            ->willReturn($this->captchaValidationResultMock);
        $this->_objectManager->addSharedInstance($captchaValidatorMock, Validator::class);
    }

    /**
     * @magentoConfigFixture base_website customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     */
    public function testGetRequestIfReCaptchaIsDisabled()
    {
        $this->initConfig(0, 'test_public_key', 'test_private_key');

        $this->checkSuccessfulGetResponse();
    }

    /**
     * @magentoConfigFixture base_website customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/contact invisible
     */
    public function testGetRequestIfReCaptchaKeysAreNotConfigured()
    {
        $this->initConfig(1, null, null);

        $this->checkSuccessfulGetResponse();
    }

    /**
     * @magentoConfigFixture base_website customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/contact invisible
     */
    public function testGetRequestIfReCaptchaIsEnabled()
    {
        $this->initConfig(1, 'test_public_key', 'test_private_key');

        $this->checkSuccessfulGetResponse(true);
    }

    /**
     * @magentoConfigFixture base_website customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     */
    public function testPostRequestIfReCaptchaIsDisabled()
    {
        $this->initConfig(0, 'test_public_key', 'test_private_key');

        $this->checkPostResponse(true);
    }

    /**
     * @magentoConfigFixture base_website customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/contact invisible
     */
    public function testPostRequestIfReCaptchaKeysAreNotConfigured()
    {
        $this->initConfig(1, null, null);

        $this->checkPostResponse(true);
    }

    /**
     * @magentoConfigFixture base_website customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/contact invisible
     */
    public function testPostRequestWithSuccessfulReCaptchaValidation()
    {
        $this->initConfig(1, 'test_public_key', 'test_private_key');
        $this->captchaValidationResultMock->expects($this->once())->method('isValid')->willReturn(true);

        $this->checkPostResponse(
            true,
            [
                CaptchaResponseResolverInterface::PARAM_RECAPTCHA => 'test',
            ]
        );
    }

    /**
     * @magentoConfigFixture base_website customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/contact invisible
     */
    public function testPostRequestIfReCaptchaParameterIsMissed()
    {
        $this->initConfig(1, 'test_public_key', 'test_private_key');

        $this->expectException(InputException::class);
        $this->expectExceptionMessage('Can not resolve reCAPTCHA parameter.');

        $this->checkPostResponse(false);
    }

    /**
     * @magentoConfigFixture base_website customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/contact invisible
     */
    public function testPostRequestWithFailedReCaptchaValidation()
    {
        $this->initConfig(1, 'test_public_key', 'test_private_key');
        $this->captchaValidationResultMock->expects($this->once())->method('isValid')->willReturn(false);

        $this->checkPostResponse(
            false,
            [CaptchaResponseResolverInterface::PARAM_RECAPTCHA => 'test']
        );
    }

    /**
     * @param bool $shouldContainReCaptcha
     */
    private function checkSuccessfulGetResponse($shouldContainReCaptcha = false)
    {
        $this->dispatch('contact/index');
        $content = $this->getResponse()->getBody();

        self::assertNotEmpty($content);

        $shouldContainReCaptcha
            ? $this->assertContains('recaptcha', $content)
            : $this->assertNotContains('recaptcha', $content);

        self::assertEmpty($this->getSessionMessages(MessageInterface::TYPE_ERROR));
    }

    /**
     * @param bool $isSuccessfulRequest
     * @param array $postValues
     */
    private function checkPostResponse(bool $isSuccessfulRequest, array $postValues = [])
    {
        $this->getRequest()
            ->setMethod(HttpRequest::METHOD_POST)
            ->setPostValue(array_replace_recursive(
                [
                    'form_key' => $this->formKey->getFormKey(),
                    'name' => 'customer name',
                    'comment' => 'comment',
                    'email' => 'user@example.com',
                ],
                $postValues
            ));

        $this->dispatch('contact/index/post');

        $this->assertRedirect($this->stringContains('contact/index'));

        if ($isSuccessfulRequest) {
            $this->assertSessionMessages(
                $this->contains(
                    "Thanks for contacting us with your comments and questions. We&#039;ll respond to you very soon."
                ),
                MessageInterface::TYPE_SUCCESS
            );
            $this->assertEmpty($this->getSessionMessages(MessageInterface::TYPE_ERROR));
        } else {
            $this->assertSessionMessages(
                $this->equalTo(['reCAPTCHA verification failed']),
                MessageInterface::TYPE_ERROR
            );
        }
    }

    /**
     * @param int|null $enabled
     * @param string|null $public
     * @param string|null $private
     */
    private function initConfig(?int $enabled, ?string $public, ?string $private): void
    {
        $this->mutableScopeConfig->setValue(
            'recaptcha_frontend/type_for/contact',
            $enabled ? 'invisible' : null,
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
