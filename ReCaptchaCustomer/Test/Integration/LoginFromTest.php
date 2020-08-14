<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Test\Integration;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\Validation\ValidationResult;
use Magento\ReCaptchaUi\Model\CaptchaResponseResolverInterface;
use Magento\ReCaptchaValidation\Model\Validator;
use Magento\Store\Model\ScopeInterface;
use Magento\TestFramework\App\MutableScopeConfig;
use Magento\TestFramework\TestCase\AbstractController;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @magentoDataFixture Magento/Customer/_files/customer.php
 * @magentoAppArea frontend
 * @magentoAppIsolation enabled
 */
class LoginFromTest extends AbstractController
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
     * @var Session
     */
    private $session;

    /**
     * @var UrlInterface
     */
    private $url;

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
        $this->session = $this->_objectManager->get(Session::class);
        $this->url = $this->_objectManager->get(UrlInterface::class);

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
    public function testGetRequestIfReCaptchaIsDisabled(): void
    {
        $this->setConfig(false, 'test_public_key', 'test_private_key');

        $this->checkSuccessfulGetResponse();
    }

    /**
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/customer_login invisible
     *
     * It's  needed for proper work of "ifconfig" in layout during tests running
     * @magentoConfigFixture default_store recaptcha_frontend/type_for/customer_login invisible
     */
    public function testGetRequestIfReCaptchaKeysAreNotConfigured(): void
    {
        $this->setConfig(true, null, null);

        $this->checkSuccessfulGetResponse();
    }

    /**
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/customer_login invisible
     *
     * It's  needed for proper work of "ifconfig" in layout during tests running
     * @magentoConfigFixture default_store recaptcha_frontend/type_for/customer_login invisible
     */
    public function testGetRequestIfReCaptchaIsEnabled(): void
    {
        $this->setConfig(true, 'test_public_key', 'test_private_key');

        $this->checkSuccessfulGetResponse(true);
    }

    /**
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     */
    public function testPostRequestIfReCaptchaIsDisabled(): void
    {
        $this->setConfig(false, 'test_public_key', 'test_private_key');

        $this->checkSuccessfulPostResponse();
    }

    /**
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/customer_login invisible
     */
    public function testPostRequestIfReCaptchaKeysAreNotConfigured(): void
    {
        $this->setConfig(true, null, null);

        $this->checkSuccessfulPostResponse();
    }

    /**
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/customer_login invisible
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
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/customer_login invisible
     */
    public function testPostRequestIfReCaptchaParameterIsMissed(): void
    {
        $this->setConfig(true, 'test_public_key', 'test_private_key');

        $this->checkFailedPostResponse();
    }

    /**
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/customer_login invisible
     */
    public function testPostRequestWithFailedReCaptchaValidation(): void
    {
        $this->setConfig(true, 'test_public_key', 'test_private_key');
        $this->captchaValidationResultMock->expects($this->once())->method('isValid')->willReturn(false);

        $this->session->setBeforeAuthUrl($this->url->getRouteUrl('customer/account/login'));

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
        $this->dispatch('customer/account/login');
        $content = $this->getResponse()->getBody();

        self::assertNotEmpty($content);

        $shouldContainReCaptcha
            ? $this->assertStringContainsString('field-recaptcha', $content)
            : $this->assertStringNotContainsString('field-recaptcha', $content);

        self::assertEmpty($this->getSessionMessages(MessageInterface::TYPE_ERROR));
    }

    /**
     * @param array $postValues
     * @return void
     */
    private function checkSuccessfulPostResponse(array $postValues = []): void
    {
        $this->makePostRequest($postValues);

        $this->assertRedirect(self::equalTo($this->url->getRouteUrl('customer/account')));
        self::assertEmpty($this->getSessionMessages(MessageInterface::TYPE_ERROR));

        $sessionCustomerId = $this->session->getCustomerId();
        $fixtureCustomerId = 1;
        self::assertEquals($fixtureCustomerId, $sessionCustomerId);
    }

    /**
     * @param array $postValues
     * @return void
     */
    private function checkFailedPostResponse(array $postValues = []): void
    {
        $this->makePostRequest($postValues);

        $this->assertRedirect(self::stringStartsWith($this->url->getRouteUrl('customer/account/login')));
        $this->assertSessionMessages(
            self::equalTo(['Something went wrong with reCAPTCHA. Please contact the store owner.']),
            MessageInterface::TYPE_ERROR
        );

        self::assertNull($this->session->getCustomerId());
    }

    /**
     * @param array $postValues
     * @return void
     */
    private function makePostRequest(array $postValues = []): void
    {
        $this->getRequest()
            ->setMethod(Http::METHOD_POST)
            ->setPostValue(
                array_replace_recursive(
                    [
                        'form_key' => $this->formKey->getFormKey(),
                        'login' => [
                            'username' =>  'customer@example.com',
                            'password' => 'password',
                        ]
                    ],
                    $postValues
                )
            );

        $this->dispatch('customer/account/loginpost');
    }

    /**
     * @param bool $isEnabled
     * @param string|null $public
     * @param string|null $private
     * @return void
     */
    private function setConfig(bool $isEnabled, ?string $public, ?string $private): void
    {
        $this->mutableScopeConfig->setValue(
            'recaptcha_frontend/type_for/customer_login',
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

        $this->mutableScopeConfig->setValue(
            'recaptcha_frontend/type_for/customer_login',
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
