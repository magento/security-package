<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Test\Api;

use Magento\Framework\Webapi\Rest\Request;
use Magento\Quote\Model\QuoteFactory;
use Magento\TestFramework\Bootstrap as TestBootstrap;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Test that login API is covered with ReCaptcha
 */
class LoginTest extends WebapiAbstract
{
    private const API_ROUTE = '/V1/integration/customer/token';

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
        $objectManager = Bootstrap::getObjectManager();
        $this->quoteFactory = $objectManager->get(QuoteFactory::class);
    }

    /**
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/customer_login invisible
     */
    public function testRequired(): void
    {
        $this->expectException(\Throwable::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('ReCaptcha validation failed, please try again');

        $serviceInfo = [
            'rest' => [
                'token' => null,
                'resourcePath' => self::API_ROUTE,
                'httpMethod' => Request::HTTP_METHOD_POST
            ],
        ];
        $requestData = [
            'username' => 'customRoleUser',
            'password' => TestBootstrap::ADMIN_PASSWORD
        ];

        $this->_webApiCall($serviceInfo, $requestData);
    }
}
