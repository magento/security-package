<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCheckoutSalesRule\Test\Api;

use Magento\Framework\Webapi\Rest\Request;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteFactory;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Test that Coupon APIs are covered with ReCaptcha
 */
class CouponApplyFormRecaptchaTest extends WebapiAbstract
{
    private const API_ROUTE   = '/V1/carts/mine/coupons/%s';
    private const COUPON_CODE = 'testCoupon';

    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var QuoteFactory
     */
    private $quoteFactory;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->_markTestAsRestOnly();
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->quoteFactory = $this->objectManager->get(QuoteFactory::class);
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote.php
     * @magentoApiDataFixture Magento/Customer/_files/customer.php
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/coupon_code invisible
     */
    public function testRequired(): void
    {
        $this->expectException(\Throwable::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('{"message":"ReCaptcha validation failed, please try again"}');

        // get customer ID token
        /** @var \Magento\Integration\Api\CustomerTokenServiceInterface $customerTokenService */
        $customerTokenService = $this->objectManager->create(
            \Magento\Integration\Api\CustomerTokenServiceInterface::class
        );
        $token = $customerTokenService->createCustomerAccessToken('customer@example.com', 'password');

        /** @var Quote $quote */
        $quote = $this->quoteFactory->create();
        $quote->load('test_order_1', 'reserved_order_id');
        $cartId = $quote->getId();

        $api_url = sprintf(self::API_ROUTE, self::COUPON_CODE);
        $serviceInfo = [
            'rest' => [
                'resourcePath' => $api_url,
                'httpMethod' => Request::HTTP_METHOD_PUT,
                'token' => $token,
            ],
        ];
        $requestData = [
            'cart_id' => $cartId
        ];

        $this->_webApiCall($serviceInfo, $requestData);
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote.php
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/coupon_code invisible
     */
    public function testGuestCartTest(): void
    {
        $this->expectException(\Throwable::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('{"message":"ReCaptcha validation failed, please try again"}');

        /** @var Quote $quote */
        $quote = $this->quoteFactory->create();
        $quote->load('test_order_1', 'reserved_order_id');
        $cartId = $quote->getId();
        $api_url = "/V1/guest-carts/$cartId/coupons/".self::COUPON_CODE;

        $serviceInfo = [
            'rest' => [
                'resourcePath' => $api_url,
                'httpMethod' => Request::HTTP_METHOD_PUT,
                'token' => null
            ],
        ];
        $requestData = [];
        $this->_webApiCall($serviceInfo, $requestData);
    }
}
