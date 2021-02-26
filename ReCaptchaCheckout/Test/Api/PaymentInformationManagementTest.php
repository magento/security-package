<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCheckout\Test\Api;

use Magento\Framework\Webapi\Rest\Request;
use Magento\Integration\Api\CustomerTokenServiceInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Test that checkout APIs are covered with ReCaptcha
 */
class PaymentInformationManagementTest extends WebapiAbstract
{
    private const API_ROUTE = '/V1/carts/mine/payment-information';

    /**
     * @var QuoteFactory
     */
    private $quoteFactory;

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
        $this->quoteFactory = $objectManager->get(QuoteFactory::class);
        $this->tokenService = $objectManager->get(CustomerTokenServiceInterface::class);
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/customer_quote_ready_for_order.php
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/place_order invisible
     */
    public function testRequired(): void
    {
        $this->expectException(\Throwable::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('{"message":"ReCaptcha validation failed, please try again"}');

        /** @var Quote $quote */
        $quote = $this->quoteFactory->create();
        $quote->load('55555555', 'reserved_order_id');
        $cartId = $quote->getId();
        $payment = $quote->getPayment();
        $address = $quote->getBillingAddress();
        $addressData = [];
        $addressProperties = [
            'city', 'company', 'countryId', 'firstname', 'lastname', 'postcode',
            'region', 'regionCode', 'regionId', 'saveInAddressBook', 'street', 'telephone', 'email'
        ];
        foreach ($addressProperties as $property) {
            $method = 'get' . $property;
            $addressData[$property] = $address->$method();
        }
        $token = $this->tokenService->createCustomerAccessToken('customer@example.com', 'password');

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::API_ROUTE,
                'httpMethod' => Request::HTTP_METHOD_POST,
                'token' => $token
            ],
        ];
        $requestData = [
            'cart_id' => $cartId,
            'billingAddress' => $addressData,
            'email' => $quote->getCustomerEmail(),
            'paymentMethod' => [
                'additional_data' => $payment->getAdditionalData(),
                'method' => $payment->getMethod(),
                'po_number' => $payment->getPoNumber()
            ]
        ];

        $this->_webApiCall($serviceInfo, $requestData);
    }
}
