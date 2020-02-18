<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Test\Integration\Observer;

use Magento\Captcha\Helper\Data as CaptchaHelper;
use Magento\Captcha\Model\DefaultModel;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\ReCaptcha\Model\CaptchaValidator;
use Magento\ReCaptchaApi\Api\CaptchaValidatorInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\TestFramework\App\MutableScopeConfig;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\TestFramework\TestCase\AbstractController;

/**
 * Test for \Magento\ReCaptchaCustomer\Observer\ForgotPasswordObserver class.
 *
 * @magentoDataFixture Magento/Customer/_files/customer.php
 * @magentoAppArea frontend
 * @magentoAppIsolation enabled
 * @magentoDbIsolation enabled
 */
class ForgotPasswordObserverTest extends AbstractController
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
        $this->mutableScopeConfig = $this->_objectManager->get(MutableScopeConfig::class);
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->response = $this->_objectManager->get(ResponseInterface::class);
        $this->captchaHelper = $this->_objectManager->create(CaptchaHelper::class);
        $this->customerRegistry = $this->_objectManager->get(CustomerRegistry::class);
        $this->customerRepository = $this->_objectManager->get(CustomerRepositoryInterface::class);
        $this->captchaValidatorMock = $this->createMock(CaptchaValidatorInterface::class);
        $this->_objectManager->addSharedInstance($this->captchaValidatorMock, CaptchaValidator::class);
    }

    public function testReCaptchaNotConfigured()
    {
        $this->initConfig(1, null, null);
        $this->sendForgotPasswordPostRequest(true);
    }

    public function testReCaptchaDisabled()
    {
        $this->initConfig(0, 'test_public_key', 'test_private_key');
        $this->sendForgotPasswordPostRequest(true);
    }

    public function testCorrectRecaptcha()
    {
        $this->initConfig(1, 'test_public_key', 'test_private_key');
        $this->captchaValidatorMock->expects($this->once())->method('validate')->willReturn(true);
        $this->sendForgotPasswordPostRequest(true);
    }

    public function testIncorrectRecaptcha()
    {
        $this->initConfig(1, 'test_public_key', 'test_private_key');
        $this->captchaValidatorMock->expects($this->once())->method('validate')->willReturn(false);
        $this->sendForgotPasswordPostRequest(false);
    }

    public function testErrorValidatingRecaptcha()
    {
        $this->initConfig(1, 'test_public_key', 'test_private_key');
        $exception = new LocalizedException(__('error_message'));
        $this->captchaValidatorMock->expects($this->once())->method('validate')->willThrowException($exception);
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('error_message');
        $this->sendForgotPasswordPostRequest(false);
    }

    /**
     * @param bool $result
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function sendForgotPasswordPostRequest(bool $result)
    {
        $email = 'customer@example.com';
        $formId = 'user_forgotpassword';
        $customerInitialToken = $this->getCustomerRpToken($email);

        /** @var DefaultModel $captchaModel */
        $captchaModel = $this->captchaHelper->getCaptcha($formId);
        $captchaModel->generate();

        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPostValue(
            [
                'email' => $email,
                'form_key' => $this->formKey->getFormKey(),
                'g-recaptcha-response' => 'test_response',
                'captcha' => [
                    $formId => $captchaModel->getWord()
                ]
            ]
        );

        $this->dispatch('customer/account/forgotpasswordpost');

        $code = $this->response->getHttpResponseCode();
        $this->assertEquals(
            302,
            $code,
            'Incorrect response code'
        );

        $customerNewToken = $this->getCustomerRpToken($email);
        $this->assertEquals(
            $result,
            $customerNewToken !== $customerInitialToken);
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
        $this->mutableScopeConfig->setValue('recaptcha/frontend/enabled_for_customer_forgot_password', $enabled, ScopeInterface::SCOPE_WEBSITE);
        $this->mutableScopeConfig->setValue('recaptcha/frontend/public_key', $public, ScopeInterface::SCOPE_WEBSITE);
        $this->mutableScopeConfig->setValue('recaptcha/frontend/private_key', $private, ScopeInterface::SCOPE_WEBSITE);
    }
}
