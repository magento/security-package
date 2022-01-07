<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCheckoutSalesRule\Test\Api;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\GraphQlAbstract;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteFactory;

/**
 * Test graphql for couponApply
 */
class CouponApplyGraphQLTest extends GraphQlAbstract
{
    /**
     * @var QuoteFactory
     */
    private $quoteFactory;

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->quoteFactory = $objectManager->get(QuoteFactory::class);
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote.php
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/coupon_code invisible
     */
    public function testCreateCouponApply(): void
    {
        $this->expectExceptionMessage('ReCaptcha validation failed, please try again');

        /** @var Quote $quote */
        $quote = $this->quoteFactory->create();
        $quote->load('test_order_1', 'reserved_order_id');
        $cartId = $quote->getId();

        $query = <<<QUERY
mutation {
  applyCouponToCart(
    input: {
      cart_id:"{$cartId}",
      coupon_code: "{testCoupon}"
    }
  ) {
     cart{
        applied_coupons {
         code
        }
        prices {
           grand_total{
             value
             currency
           }
        }
     }
  }
}
QUERY;

        $this->graphQlMutation($query);
    }
}
