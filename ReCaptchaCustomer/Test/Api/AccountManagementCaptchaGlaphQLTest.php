<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Test\Api;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Integration\Api\CustomerTokenServiceInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\GraphQlAbstract;
use Magento\Customer\Model\AccountManagement;

/**
 * Test change customer password
 */
class AccountManagementCaptchaGlaphQLTest extends GraphQlAbstract
{
    /**
     * @var AccountManagementInterface
     */
    private $accountManagement;

    /**
     * @var CustomerTokenServiceInterface
     */
    private $customerTokenService;

    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    protected function setUp(): void
    {
        $this->customerTokenService = Bootstrap::getObjectManager()->get(CustomerTokenServiceInterface::class);
        $this->accountManagement = Bootstrap::getObjectManager()->get(AccountManagementInterface::class);
        $this->customerRegistry = Bootstrap::getObjectManager()->get(CustomerRegistry::class);
    }

    /**
     * @magentoApiDataFixture Magento/Customer/_files/customer.php
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/customer_edit invisible
     */
    public function testChangePassword()
    {
        $this->expectExceptionMessage('ReCaptcha validation failed, please try again');
        $customerEmail = 'customer@example.com';
        $currentPassword = 'password';
        $newPassword = 'anotherPassword1';

        $query = $this->getQuery($currentPassword, $newPassword);
        $headerMap = $this->getCustomerAuthHeaders($customerEmail, $currentPassword);

        $this->graphQlMutation($query, [], '', $headerMap);
    }

    /**
     * @magentoApiDataFixture Magento/Customer/_files/customer.php
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/customer_forgot_password invisible
     *
     */
    public function testResetPassword(): void
    {
        $this->expectExceptionMessage('ReCaptcha validation failed, please try again');

        $query = <<<QUERY
mutation {
    resetPassword (
        email: "customer@example.com"
        resetPasswordToken: "{$this->getResetPasswordToken()}"
        newPassword: "newPass123"
    )
}
QUERY;
        $this->graphQlMutation($query);
    }

    /**
     * @param $currentPassword
     * @param $newPassword
     *
     * @return string
     */
    private function getQuery($currentPassword, $newPassword)
    {
        $query = <<<QUERY
mutation {
  changeCustomerPassword(
    currentPassword: "$currentPassword",
    newPassword: "$newPassword"
  ) {
    id
    email
    firstname
    lastname
  }
}
QUERY;

        return $query;
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return array
     * @throws AuthenticationException
     */
    private function getCustomerAuthHeaders(string $email, string $password): array
    {
        $customerToken = $this->customerTokenService->createCustomerAccessToken($email, $password);
        return ['Authorization' => 'Bearer ' . $customerToken];
    }

    /**
     * Get reset password token
     *
     * @return string
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function getResetPasswordToken()
    {
        $this->accountManagement->initiatePasswordReset(
            'customer@example.com',
            AccountManagement::EMAIL_RESET,
            1
        );

        $customerSecure = $this->customerRegistry->retrieveSecureData(1);
        return $customerSecure->getRpToken();
    }
}
