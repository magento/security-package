<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Test\Integration\Observer;

use Magento\Customer\Api\CustomerRepositoryInterface;
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
 * Test for \Magento\ReCaptchaCustomer\Observer\CreateCustomerObserver class.
 *
 * @magentoAppArea frontend
 * @magentoAppIsolation enabled
 * @magentoDbIsolation enabled
 */
class CreateCustomerObserverTest extends AbstractController
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
        $this->customerRepository = $this->_objectManager->get(CustomerRepositoryInterface::class);
        $this->captchaValidatorMock = $this->createMock(CaptchaValidatorInterface::class);
        $this->_objectManager->addSharedInstance($this->captchaValidatorMock, CaptchaValidator::class);
    }

    public function testReCaptchaNotConfigured()
    {
        $this->initConfig(1, null, null);
        $this->sendAccountCreatePostRequest(true);
    }

    public function testReCaptchaDisabled()
    {
        $this->initConfig(0, 'test_public_key', 'test_private_key');
        $this->sendAccountCreatePostRequest(true);
    }

    public function testCorrectRecaptcha()
    {
        $this->initConfig(1, 'test_public_key', 'test_private_key');
        $this->captchaValidatorMock->expects($this->once())->method('validate')->willReturn(true);
        $this->sendAccountCreatePostRequest(true);
    }

    public function testIncorrectRecaptcha()
    {
        $this->initConfig(1, 'test_public_key', 'test_private_key');
        $this->captchaValidatorMock->expects($this->once())->method('validate')->willReturn(false);
        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessageRegExp('/No such entity with email = \S+@\S+, websiteId = \d/');
        $this->sendAccountCreatePostRequest(false);
    }

    public function testErrorValidatingRecaptcha()
    {
        $this->initConfig(1, 'test_public_key', 'test_private_key');
        $exception = new LocalizedException(__('error_message'));
        $this->captchaValidatorMock->expects($this->once())->method('validate')->willThrowException($exception);
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('error_message');
        $this->sendAccountCreatePostRequest(false);
    }

    /**
     * @param bool $customerCreated
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function sendAccountCreatePostRequest(bool $customerCreated)
    {
        $email = 'dummy@dummy.com';
        $password = 'Password1';

        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPostValue(
            [
                'firstname' => 'first_name',
                'lastname' => 'last_name',
                'email' => $email,
                'password' => $password,
                'password_confirmation' => $password,
                'form_key' => $this->formKey->getFormKey(),
                'g-recaptcha-response' => 'test_response'
            ]
        );

        $this->dispatch('customer/account/createpost');

        $code = $this->response->getHttpResponseCode();
        $this->assertEquals(
            302,
            $code,
            'Incorrect response code'
        );

        $customer = $this->customerRepository->get($email);

        $this->assertEquals(
            $customerCreated,
            null !== $customer->getId()
        );
    }

    /**
     * @param int|null $enabled
     * @param string|null $public
     * @param string|null $private
     */
    private function initConfig(?int $enabled, ?string $public, ?string $private): void
    {
        $this->mutableScopeConfig->setValue('recaptcha/frontend/enabled_for_customer_create', $enabled, ScopeInterface::SCOPE_WEBSITE);
        $this->mutableScopeConfig->setValue('recaptcha/frontend/public_key', $public, ScopeInterface::SCOPE_WEBSITE);
        $this->mutableScopeConfig->setValue('recaptcha/frontend/private_key', $private, ScopeInterface::SCOPE_WEBSITE);
    }
}
