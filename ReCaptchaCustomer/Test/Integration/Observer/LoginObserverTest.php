<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Test\Integration\Observer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Exception\LocalizedException;
use Magento\ReCaptcha\Model\CaptchaValidator;
use Magento\ReCaptchaApi\Api\CaptchaValidatorInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\TestFramework\App\MutableScopeConfig;
use Magento\TestFramework\TestCase\AbstractController;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Test for \Magento\ReCaptchaCustomer\Observer\LoginObserver class.
 *
 * @magentoDataFixture Magento/Customer/_files/customer.php
 * @magentoAppArea frontend
 * @magentoAppIsolation enabled
 * @magentoDbIsolation enabled
 */
class LoginObserverTest extends AbstractController
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
     * @var Session
     */
    private $session;

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
        $this->session = $this->_objectManager->get(Session::class);
        $this->captchaValidatorMock = $this->createMock(CaptchaValidatorInterface::class);
        $this->_objectManager->addSharedInstance($this->captchaValidatorMock, CaptchaValidator::class);
    }

    public function testReCaptchaNotConfigured()
    {
        $this->initConfig(1, null, null);
        $this->sendLoginRequest(true);
    }

    public function testReCaptchaDisabled()
    {
        $this->initConfig(0, 'test_public_key', 'test_private_key');
        $this->sendLoginRequest(true);
    }

    public function testCorrectRecaptcha()
    {
        $this->initConfig(1, 'test_public_key', 'test_private_key');
        $this->captchaValidatorMock->expects($this->once())->method('validate')->willReturn(true);
        $this->sendLoginRequest(true);
    }

    public function testIncorrectRecaptcha()
    {
        $this->initConfig(1, 'test_public_key', 'test_private_key');
        $this->captchaValidatorMock->expects($this->once())->method('validate')->willReturn(false);
        $this->sendLoginRequest(false);
    }

    public function testErrorValidatingRecaptcha()
    {
        $this->initConfig(1, 'test_public_key', 'test_private_key');
        $exception = new LocalizedException(__('error_message'));
        $this->captchaValidatorMock->expects($this->once())->method('validate')->willThrowException($exception);
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('error_message');
        $this->sendLoginRequest(false);
    }

    /**
     * @param bool $result
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function sendLoginRequest(bool $result)
    {
        $username = 'customer@example.com';
        $password = 'password';

        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPostValue(
            [
                'form_key' => $this->formKey->getFormKey(),
                'g-recaptcha-response' => 'test_response',
                'login' => [
                    'username' => $username,
                    'password' => $password,
                ]
            ]
        );

        $this->dispatch('customer/account/loginpost');

        $code = $this->response->getHttpResponseCode();
        $this->assertEquals(
            302,
            $code,
            'Incorrect response code'
        );

        $customerId = $this->customerRepository->get($username)->getId();
        $sessionCustomerId = $this->session->getCustomerId();
        $this->assertEquals(
            $result,
            $customerId === $sessionCustomerId);
    }

    /**
     * @param int|null $enabled
     * @param string|null $public
     * @param string|null $private
     */
    private function initConfig(?int $enabled, ?string $public, ?string $private): void
    {
        $this->mutableScopeConfig->setValue('recaptcha/frontend/enabled_for_customer_login', $enabled, ScopeInterface::SCOPE_WEBSITE);
        $this->mutableScopeConfig->setValue('recaptcha/frontend/public_key', $public, ScopeInterface::SCOPE_WEBSITE);
        $this->mutableScopeConfig->setValue('recaptcha/frontend/private_key', $private, ScopeInterface::SCOPE_WEBSITE);
    }
}
