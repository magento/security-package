<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaPaypal\Test\Api;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\GraphQlAbstract;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteFactory;
use Magento\Paypal\Model\Payflow\Service\Request\SecureToken;

/**
 * Test change customer password
 */
class PayflowCaptchaGlaphQLTest extends GraphQlAbstract
{
    /**
     * @var QuoteFactory
     */
    private $quoteFactory;

    /**
     * @var SecureToken
     */
    private $secureTokenService;

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->quoteFactory = $objectManager->get(QuoteFactory::class);
        $this->secureTokenService = $objectManager->get(SecureToken::class);
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote.php
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/paypal_payflowpro invisible
     */
    public function testCreatePayflowProToken(): void
    {
        $this->expectExceptionMessage('ReCaptcha validation failed, please try again');

        /** @var Quote $quote */
        $quote = $this->quoteFactory->create();
        $quote->load('test_order_1', 'reserved_order_id');
        $cartId = $quote->getId();

        $query = <<<QUERY
mutation {
  createPayflowProToken(
    input: {
      cart_id:"{$cartId}",
      urls: {
        cancel_url: "paypal/transparent/cancel/"
        error_url: "paypal/transparent/error/"
        return_url: "paypal/transparent/response/"
      }
    }
  ) {
    response_message
    result
    result_code
    secure_token
    secure_token_id
  }
}
QUERY;

        $this->graphQlMutation($query);
    }

}
