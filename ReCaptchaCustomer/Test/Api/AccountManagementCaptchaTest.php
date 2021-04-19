<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Test\Api;

use Magento\Customer\Model\AccountManagement;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Integration\Api\CustomerTokenServiceInterface;


/**
 * Test class for Magento\Customer\Api\AccountManagementInterface
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AccountManagementCaptchaTest extends WebapiAbstract
{

    private const API_ROUTE = '/V1/customers/';

    /**
     * @var CustomerTokenServiceInterface
     */
    private $tokenService;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->_markTestAsRestOnly();
        $objectManager = Bootstrap::getObjectManager();
        $this->tokenService = $objectManager->get(CustomerTokenServiceInterface::class);
    }

    /**
     * @magentoApiDataFixture Magento/Customer/_files/customer.php
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/customer_edit invisible
     */
    public function testChangePassword(): void
    {
        $this->expectException(\Throwable::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('{"message":"ReCaptcha validation failed, please try again"}');

        $token = $this->tokenService->createCustomerAccessToken('customer@example.com', 'password');

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::API_ROUTE . 'me/password',
                'httpMethod' => Request::HTTP_METHOD_PUT,
                'token' => $token
            ],
        ];
        $requestData = [
            'currentPassword' => 'password',
            'newPassword' => 'Newpassword123qQ',
        ];

        $this->_webApiCall($serviceInfo, $requestData);
    }

    /**
     * @magentoApiDataFixture Magento/Customer/_files/customer.php
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/customer_forgot_password invisible
     */
    public function testInitiatePasswordReset(): void
    {
        $this->expectException(\Throwable::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('{"message":"ReCaptcha validation failed, please try again"}');

        $token = $this->tokenService->createCustomerAccessToken('customer@example.com', 'password');

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::API_ROUTE . 'password',
                'httpMethod' => Request::HTTP_METHOD_PUT,
                'token' => $token
            ],
        ];
        $requestData = [
            'email' => 'customer@example.com',
            'template' => AccountManagement::EMAIL_RESET,
            'websiteId' => 0,
        ];

        $this->_webApiCall($serviceInfo, $requestData);
    }

    /**
     * @magentoApiDataFixture Magento/Customer/_files/customer.php
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/customer_forgot_password invisible
     */
    public function testPasswordReset(): void
    {
        $this->expectException(\Throwable::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('{"message":"ReCaptcha validation failed, please try again"}');

        $token = $this->tokenService->createCustomerAccessToken('customer@example.com', 'password');

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::API_ROUTE . 'resetPassword',
                'httpMethod' => Request::HTTP_METHOD_POST,
                'token' => $token
            ],
        ];
        $requestData = [
            'email' => 'customer@example.com',
            'resetToken' =>  $token,
            'newPassword' => 'Newpassword123qQ'
        ];

        $this->_webApiCall($serviceInfo, $requestData);
    }

}
