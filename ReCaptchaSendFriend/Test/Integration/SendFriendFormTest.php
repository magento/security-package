<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaSendFriend\Test\Integration;

use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\Validation\ValidationResult;
use Magento\ReCaptchaUi\Model\CaptchaResponseResolverInterface;
use Magento\ReCaptchaValidation\Model\Validator;
use Magento\Store\Model\ScopeInterface;
use Magento\TestFramework\App\MutableScopeConfig;
use Magento\TestFramework\Mail\Template\TransportBuilderMock;
use Magento\TestFramework\TestCase\AbstractController;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @magentoAppArea frontend
 * @magentoAppIsolation enabled
 * @magentoDataFixture Magento/Catalog/_files/product_simple.php
 */
class SendFriendFormTest extends AbstractController
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
        $this->mutableScopeConfig = $this->_objectManager->get(MutableScopeConfig::class);
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->transportMock = $this->_objectManager->get(TransportBuilderMock::class);

        $this->captchaValidationResultMock = $this->createMock(ValidationResult::class);
        $captchaValidationResultMock = $this->createMock(Validator::class);
        $captchaValidationResultMock->expects($this->any())
            ->method('isValid')
            ->willReturn($this->captchaValidationResultMock);
        $this->_objectManager->addSharedInstance($captchaValidationResultMock, Validator::class);
    }

    /**
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture default_store sendfriend/email/enabled 1
     * @magentoConfigFixture default_store sendfriend/email/allow_guest 1
     *
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     */
    public function testGetRequestIfReCaptchaIsDisabled(): void
    {
        $this->setConfig(false, 'test_public_key', 'test_private_key');

        $this->checkSuccessfulGetResponse();
    }

    /**
     * @magentoConfigFixture default_store sendfriend/email/enabled 1
     * @magentoConfigFixture default_store sendfriend/email/allow_guest 1
     * @magentoConfigFixture default_store customer/captcha/enable 0
     *
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/sendfriend invisible
     *
     * It's  needed for proper work of "ifconfig" in layout during tests running
     * @magentoConfigFixture default_store recaptcha_frontend/type_for/sendfriend invisible
     */
    public function testGetRequestIfReCaptchaKeysAreNotConfigured(): void
    {
        $this->setConfig(true, null, null);

        $this->checkSuccessfulGetResponse();
    }

    /**
     * @magentoConfigFixture default_store sendfriend/email/enabled 1
     * @magentoConfigFixture default_store sendfriend/email/allow_guest 1
     * @magentoConfigFixture default_store customer/captcha/enable 0
     *
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/sendfriend invisible
     *
     * It's  needed for proper work of "ifconfig" in layout during tests running
     * @magentoConfigFixture default_store recaptcha_frontend/type_for/sendfriend invisible
     */
    public function testGetRequestIfReCaptchaIsEnabled(): void
    {
        $this->setConfig(true, 'test_public_key', 'test_private_key');

        $this->checkSuccessfulGetResponse(true);
    }

    /**
     * @magentoConfigFixture default_store sendfriend/email/enabled 1
     * @magentoConfigFixture default_store sendfriend/email/allow_guest 1
     * @magentoConfigFixture default_store customer/captcha/enable 0
     *
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     */
    public function testPostRequestIfReCaptchaIsDisabled(): void
    {
        $this->setConfig(false, 'test_public_key', 'test_private_key');

        $this->checkSuccessfulPostResponse();
    }

    /**
     * @magentoConfigFixture default_store sendfriend/email/enabled 1
     * @magentoConfigFixture default_store sendfriend/email/allow_guest 1
     * @magentoConfigFixture default_store customer/captcha/enable 0
     *
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/sendfriend invisible
     */
    public function testPostRequestIfReCaptchaKeysAreNotConfigured(): void
    {
        $this->setConfig(true, null, null);

        $this->checkSuccessfulPostResponse();
    }

    /**
     * @magentoConfigFixture default_store sendfriend/email/enabled 1
     * @magentoConfigFixture default_store sendfriend/email/allow_guest 1
     * @magentoConfigFixture default_store customer/captcha/enable 0
     *
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/sendfriend invisible
     */
    public function testPostRequestWithSuccessfulReCaptchaValidation(): void
    {
        $this->setConfig(true, 'test_public_key', 'test_private_key');
        $this->captchaValidationResultMock->expects($this->once())->method('isValid')->willReturn(true);

        $this->checkSuccessfulPostResponse(
            [CaptchaResponseResolverInterface::PARAM_RECAPTCHA => 'test']
        );
    }

    /**
     * @magentoConfigFixture default_store sendfriend/email/enabled 1
     * @magentoConfigFixture default_store sendfriend/email/allow_guest 1
     * @magentoConfigFixture default_store customer/captcha/enable 0
     *
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/sendfriend invisible
     */
    public function testPostRequestIfReCaptchaParameterIsMissed(): void
    {
        $this->setConfig(true, 'test_public_key', 'test_private_key');

        $this->checkFailedPostResponse();
    }

    /**
     * @magentoConfigFixture default_store sendfriend/email/enabled 1
     * @magentoConfigFixture default_store sendfriend/email/allow_guest 1
     * @magentoConfigFixture default_store customer/captcha/enable 0
     *
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/sendfriend invisible
     */
    public function testPostRequestWithFailedReCaptchaValidation(): void
    {
        $this->setConfig(true, 'test_public_key', 'test_private_key');
        $this->captchaValidationResultMock->expects($this->once())->method('isValid')->willReturn(false);

        $this->checkFailedPostResponse(
            [CaptchaResponseResolverInterface::PARAM_RECAPTCHA => 'test']
        );
    }

    /**
     * @param bool $shouldContainReCaptcha
     * @return void
     */
    private function checkSuccessfulGetResponse($shouldContainReCaptcha = false): void
    {
        $this->dispatch('sendfriend/product/send/id/1');
        $content = $this->getResponse()->getBody();

        self::assertNotEmpty($content);

        $shouldContainReCaptcha
            ? self::assertStringContainsString('field-recaptcha', $content)
            : self::assertStringNotContainsString('field-recaptcha', $content);

        self::assertEmpty($this->getSessionMessages(MessageInterface::TYPE_ERROR));
    }

    /**
     * @param array $postValues
     * @return void
     */
    private function checkSuccessfulPostResponse(array $postValues = []): void
    {
        $this->makePostRequest($postValues);

        $this->assertSessionMessages(
            self::containsEqual(
                'The link to a friend was sent.'
            ),
            MessageInterface::TYPE_SUCCESS
        );
        self::assertEmpty($this->getSessionMessages(MessageInterface::TYPE_ERROR));

        $message = $this->transportMock->getSentMessage();
        self::assertNotEmpty($message);
        self::assertEquals((string)__('Welcome, Recipient'), $message->getSubject());
    }

    /**
     * @param array $postValues
     * @return void
     */
    private function checkFailedPostResponse(array $postValues = []): void
    {
        $this->makePostRequest($postValues);

        $this->assertSessionMessages(
            self::equalTo(['reCAPTCHA verification failed']),
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
        $expectedUrl = 'http://localhost/index.php/simple-product.html';

        $this->getRequest()
            ->setParam(RedirectInterface::PARAM_NAME_REFERER_URL, $expectedUrl)
            ->setMethod(Http::METHOD_POST)
            ->setPostValue(array_replace_recursive(
                [
                    'sender' => [
                        'name' => 'Sender',
                        'email' => 'sender@example.com',
                        'message' => 'Message',
                    ],
                    'recipients' => [
                        'name' => [
                            'Recipient',
                        ],
                        'email' => [
                            'recipient@example.com',
                        ]
                    ],
                    'form_key' => $this->formKey->getFormKey(),
                ],
                $postValues
            ));

        $this->dispatch('sendfriend/product/sendmail/id/1');
        $this->assertRedirect(self::equalTo($expectedUrl));
    }

    /**
     * @param bool $isEnabled
     * @param string|null $public
     * @param string|null $private
     */
    private function setConfig(bool $isEnabled, ?string $public, ?string $private): void
    {
        $this->mutableScopeConfig->setValue(
            'recaptcha_frontend/type_for/sendfriend',
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

    public function tearDown(): void
    {
        $this->mutableScopeConfig->setValue(
            'recaptcha_frontend/type_for/sendfriend',
            null,
            ScopeInterface::SCOPE_WEBSITE
        );
        $this->mutableScopeConfig->setValue(
            'recaptcha_frontend/type_invisible/public_key',
            null,
            ScopeInterface::SCOPE_WEBSITE
        );
        $this->mutableScopeConfig->setValue(
            'recaptcha_frontend/type_invisible/private_key',
            null,
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}
