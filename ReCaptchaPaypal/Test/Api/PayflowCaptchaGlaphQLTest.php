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

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote.php
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/paypal_payflowpro invisible
     */
    public function testHandlePayflowProResponse(): void
    {
        $this->expectExceptionMessage('ReCaptcha validation failed, please try again');

        /** @var Quote $quote */
        $quote = $this->quoteFactory->create();
        $quote->load('test_order_1', 'reserved_order_id');
        $cartId = $quote->getId();

        $payload = 'BILLTOCITY=CityM&AMT=0.00&BILLTOSTREET=Green+str,+67&VISACARDLEVEL=12&SHIPTOCITY=CityM'
            . '&NAMETOSHIP=John+Smith&ZIP=75477&BILLTOLASTNAME=Smith&BILLTOFIRSTNAME=John'
            . '&RESPMSG=Verified&PROCCVV2=M&STATETOSHIP=AL&NAME=John+Smith&BILLTOZIP=75477&CVV2MATCH=Y'
            . '&PNREF=B70CCC236815&ZIPTOSHIP=75477&SHIPTOCOUNTRY=US&SHIPTOSTREET=Green+str,+67&CITY=CityM'
            . '&HOSTCODE=A&LASTNAME=Smith&STATE=AL&SECURETOKEN=MYSECURETOKEN&CITYTOSHIP=CityM&COUNTRYTOSHIP=US'
            . '&AVSDATA=YNY&ACCT=1111&AUTHCODE=111PNI&FIRSTNAME=John&RESULT=0&IAVS=N&POSTFPSMSG=No+Rules+Triggered&'
            . 'BILLTOSTATE=AL&BILLTOCOUNTRY=US&EXPDATE=0222&CARDTYPE=0&PREFPSMSG=No+Rules+Triggered&SHIPTOZIP=75477&'
            . 'PROCAVS=A&COUNTRY=US&AVSZIP=N&ADDRESS=Green+str,+67&BILLTONAME=John+Smith&'
            . 'ADDRESSTOSHIP=Green+str,+67&'
            . 'AVSADDR=Y&SECURETOKENID=MYSECURETOKENID&SHIPTOSTATE=AL&TRANSTIME=2019-06-24+07%3A53%3A10';

        $query = <<<QUERY
mutation {
  handlePayflowProResponse(input: {
          paypal_payload: "$payload",
          cart_id: "{$cartId}"
        })
      {
        cart {
          selected_payment_method {
            code
          }
        }
      }
      placeOrder(input: {cart_id: "{$cartId}"}) {
        order {
          order_number
        }
      }
}
QUERY;

        $this->graphQlMutation($query);
    }

}
