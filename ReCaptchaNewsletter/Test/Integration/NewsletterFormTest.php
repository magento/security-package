<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaNewsletter\Test\Integration;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\Validation\ValidationResult;
use Magento\Newsletter\Model\SubscriberFactory;
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
class NewsletterFormTest extends AbstractController
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
     * @var UrlInterface
     */
    private $url;

    /**
     * @var SubscriberFactory
     */
    private $subscriberFactory;

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
        $this->url = $this->_objectManager->get(UrlInterface::class);
        $this->subscriberFactory = $this->_objectManager->get(SubscriberFactory::class);

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
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/newsletter invisible
     *
     * It's  needed for proper work of "ifconfig" in layout during tests running
     * @magentoConfigFixture default_store recaptcha_frontend/type_for/newsletter invisible
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
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/newsletter invisible
     *
     * It's  needed for proper work of "ifconfig" in layout during tests running
     * @magentoConfigFixture default_store recaptcha_frontend/type_for/newsletter invisible
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

        $this->checkPostResponse(true);
    }

    /**
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/newsletter invisible
     */
    public function testPostRequestIfReCaptchaKeysAreNotConfigured()
    {
        $this->setConfig(true, null, null);

        $this->checkPostResponse(true);
    }

    /**
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/newsletter invisible
     */
    public function testPostRequestWithSuccessfulReCaptchaValidation()
    {
        $this->setConfig(true, 'test_public_key', 'test_private_key');
        $this->captchaValidationResultMock->expects($this->once())->method('isValid')->willReturn(true);

        $this->checkPostResponse(
            true,
            [CaptchaResponseResolverInterface::PARAM_RECAPTCHA => 'test']
        );
    }

    /**
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/newsletter invisible
     */
    public function testPostRequestIfReCaptchaParameterIsMissed()
    {
        $this->setConfig(true, 'test_public_key', 'test_private_key');

        $this->expectException(InputException::class);
        $this->expectExceptionMessage('Can not resolve reCAPTCHA parameter.');

        $this->checkPostResponse(false);
    }

    /**
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/newsletter invisible
     */
    public function testPostRequestWithFailedReCaptchaValidation()
    {
        $this->setConfig(true, 'test_public_key', 'test_private_key');
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
        $this->dispatch($this->url->getRouteUrl());
        $content = $this->getResponse()->getBody();

        self::assertNotEmpty($content);

        $shouldContainReCaptcha
            ? self::assertContains('field-recaptcha', $content)
            : self::assertNotContains('field-recaptcha', $content);

        self::assertEmpty($this->getSessionMessages(MessageInterface::TYPE_ERROR));
    }

    /**
     * @param bool $isSuccessfulRequest
     * @param array $postValues
     */
    private function checkPostResponse(bool $isSuccessfulRequest, array $postValues = [])
    {
        $this->getRequest()
            ->setMethod(Http::METHOD_POST)
            ->setPostValue(array_replace_recursive(
                [
                    'form_key' => $this->formKey->getFormKey(),
                    'email' => 'user@example.com',
                ],
                $postValues
            ));

        $this->dispatch('newsletter/subscriber/new');

        $this->assertRedirect(self::equalTo($this->url->getRouteUrl()));

        if ($isSuccessfulRequest) {
            $this->assertSessionMessages(
                self::contains(
                    'Thank you for your subscription.'
                ),
                MessageInterface::TYPE_SUCCESS
            );
            self::assertEmpty($this->getSessionMessages(MessageInterface::TYPE_ERROR));
        } else {
            $this->assertSessionMessages(
                $this->equalTo(['reCAPTCHA verification failed']),
                MessageInterface::TYPE_ERROR
            );
        }
    }

    /**
     * @param bool $isEnabled
     * @param string|null $public
     * @param string|null $private
     */
    private function setConfig(bool $isEnabled, ?string $public, ?string $private): void
    {
        $this->mutableScopeConfig->setValue(
            'recaptcha_frontend/type_for/newsletter',
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

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->subscriberFactory->create()->loadBySubscriberEmail('user@example.com', 1)->delete();
    }
}
