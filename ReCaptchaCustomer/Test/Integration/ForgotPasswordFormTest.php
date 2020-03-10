<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Test\Integration;

use Magento\Captcha\Helper\Data as CaptchaHelper;
use Magento\Captcha\Model\DefaultModel;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\Validation\ValidationResult;
use Magento\ReCaptchaUi\Model\CaptchaResponseResolverInterface;
use Magento\ReCaptchaValidation\Model\Validator;
use Magento\Store\Model\ScopeInterface;
use Magento\TestFramework\App\MutableScopeConfig;
use Magento\TestFramework\Mail\Template\TransportBuilderMock;
use Magento\TestFramework\TestCase\AbstractController;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Test for \Magento\ReCaptchaCustomer\Observer\ForgotPasswordObserver class.
 *
 * @magentoDataFixture Magento/Customer/_files/customer.php
 * @magentoAppArea frontend
 * @magentoAppIsolation enabled
 * @magentoDbIsolation enabled
 */
class ForgotPasswordFormTest extends AbstractController
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
     * @var CaptchaHelper
     */
    private $captchaHelper;

    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var UrlInterface
     */
    private $url;

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
    protected function setUp()
    {
        parent::setUp();
        $this->mutableScopeConfig = $this->_objectManager->get(MutableScopeConfig::class);
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->captchaHelper = $this->_objectManager->create(CaptchaHelper::class);
        $this->customerRegistry = $this->_objectManager->get(CustomerRegistry::class);
        $this->customerRepository = $this->_objectManager->get(CustomerRepositoryInterface::class);
        $this->url = $this->_objectManager->get(UrlInterface::class);
        $this->transportMock = $this->_objectManager->get(TransportBuilderMock::class);
        $this->captchaValidationResultMock = $this->createMock(ValidationResult::class);
        $captchaValidationResultMock = $this->createMock(Validator::class);
        $captchaValidationResultMock->expects($this->any())
            ->method('isValid')
            ->willReturn($this->captchaValidationResultMock);
        $this->_objectManager->addSharedInstance($captchaValidationResultMock, Validator::class);
    }

    public function testGetRequestIfReCaptchaIsDisabled()
    {
        $this->initConfig(0, 'test_public_key', 'test_private_key');

        $this->checkSuccessfulGetResponse();
    }

    /**
     * @magentoConfigFixture default_store recaptcha_frontend/type_for/customer_forgot_password invisible
     */
    public function testGetRequestIfReCaptchaKeysAreNotConfigured()
    {
        $this->initConfig(1, null, null);

        $this->checkSuccessfulGetResponse();
    }

    /**
     * @magentoConfigFixture default_store recaptcha_frontend/type_for/customer_forgot_password invisible
     */
    public function testGetRequestIfReCaptchaIsEnabled()
    {
        $this->initConfig(1, 'test_public_key', 'test_private_key');

        $this->checkSuccessfulGetResponse(true);
    }

    public function testPostRequestIfReCaptchaKeysAreNotConfigured()
    {
        $this->initConfig(1, null, null);

        $this->checkSuccessfulPostResponse(true);
    }

    public function testPostRequestIfReCaptchaIsDisabled()
    {
        $this->initConfig(0, 'test_public_key', 'test_private_key');

        $this->checkSuccessfulPostResponse(true);
    }

    public function testPostRequestWithSuccessfulReCaptchaValidation()
    {
        $this->initConfig(1, 'test_public_key', 'test_private_key');
        $this->captchaValidationResultMock->expects($this->once())->method('isValid')->willReturn(true);

        $this->checkSuccessfulPostResponse(
            true,
            [CaptchaResponseResolverInterface::PARAM_RECAPTCHA => 'test_response']
        );
    }

    public function testPostRequestWithFailedReCaptchaValidation()
    {
        $this->initConfig(1, 'test_public_key', 'test_private_key');
        $this->captchaValidationResultMock->expects($this->once())->method('isValid')->willReturn(false);

        $this->checkSuccessfulPostResponse(
            false,
            [CaptchaResponseResolverInterface::PARAM_RECAPTCHA => 'test_response']
        );
    }

    public function testPostRequestIfReCaptchaParameterIsMissed()
    {
        $this->initConfig(1, 'test_public_key', 'test_private_key');
        $this->captchaValidationResultMock->expects($this->never())->method('isValid');
        $this->expectException(InputException::class);
        $this->expectExceptionMessage('Can not resolve reCAPTCHA parameter.');

        $this->checkSuccessfulPostResponse(
            false
        );
    }

    /**
     * @param bool $result
     * @param array $postValues
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function checkSuccessfulPostResponse(bool $result, array $postValues = [])
    {
        $email = 'customer@example.com';
        $formId = 'user_forgotpassword';
        $customerInitialToken = $this->getCustomerRpToken($email);

        /** @var DefaultModel $captchaModel */
        $captchaModel = $this->captchaHelper->getCaptcha($formId);
        $captchaModel->generate();

        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPostValue(
            array_merge_recursive(
                [
                    'email' => $email,
                    'form_key' => $this->formKey->getFormKey(),
                    'captcha' => [
                        $formId => $captchaModel->getWord()
                    ]
                ],
                $postValues
            )
        );

        $this->dispatch('customer/account/forgotpasswordpost');

        $customerNewToken = $this->getCustomerRpToken($email);
        $message = $this->transportMock->getSentMessage();

        if ($result) {
            $this->assertRedirect(self::equalTo($this->url->getRouteUrl('customer/account')));
            self::assertEmpty($this->getSessionMessages(MessageInterface::TYPE_ERROR));
            $this->assertNotEquals(
                $customerNewToken,
                $customerInitialToken
            );
            self::assertNotEmpty($message);
            self::assertEquals((string)__('Reset your Main Website Store password'), $message->getSubject());
        } else {
            $this->assertRedirect(self::equalTo($this->url->getRouteUrl('customer/account/forgotpassword')));
            $this->assertSessionMessages(
                self::equalTo(['reCAPTCHA verification failed']),
                MessageInterface::TYPE_ERROR
            );
            $this->assertEquals(
                $customerNewToken,
                $customerInitialToken
            );
            self::assertEmpty($message);
        }


        $this->assertEquals(
            $result,
            $customerNewToken !== $customerInitialToken);
    }

    /**
     * @param bool $shouldContainReCaptcha
     */
    private function checkSuccessfulGetResponse($shouldContainReCaptcha = false)
    {
        $this->dispatch('customer/account/forgotpassword');
        $content = $this->getResponse()->getBody();

        self::assertNotEmpty($content);

        $shouldContainReCaptcha
            ? $this->assertContains('field-recaptcha', $content)
            : $this->assertNotContains('field-recaptcha', $content);

        self::assertEmpty($this->getSessionMessages(MessageInterface::TYPE_ERROR));
    }

    /**
     * @param string $customerEmail
     * @return string|null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function getCustomerRpToken(string $customerEmail): ?string
    {
        $customer = $this->customerRepository->get($customerEmail);
        $customerSecure = $this->customerRegistry->retrieveSecureData($customer->getId());

        return $customerSecure->getRpToken();
    }

    /**
     * @param int|null $enabled
     * @param string|null $public
     * @param string|null $private
     */
    private function initConfig(?int $enabled, ?string $public, ?string $private): void
    {
        $this->mutableScopeConfig->setValue('recaptcha_frontend/type_for/newsletter', null, ScopeInterface::SCOPE_WEBSITE);
        $this->mutableScopeConfig->setValue('recaptcha_frontend/type_for/customer_forgot_password', $enabled ? 'invisible' : null, ScopeInterface::SCOPE_WEBSITE);
        $this->mutableScopeConfig->setValue('recaptcha_frontend/type_invisible/public_key', $public, ScopeInterface::SCOPE_WEBSITE);
        $this->mutableScopeConfig->setValue('recaptcha_frontend/type_invisible/private_key', $private, ScopeInterface::SCOPE_WEBSITE);
    }
}
