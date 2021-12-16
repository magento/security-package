<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCheckoutSalesRule\Test\Integration;

use Magento\Customer\Model\Session;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractController;
use Magento\ReCaptchaUi\Model\CaptchaResponseResolverInterface;

/**
 * Tests for Coupon Post form
 *
 * @magentoDataFixture Magento/Customer/_files/customer.php
 * @magentoDbIsolation enabled
 * @magentoAppArea frontend
 */
class CouponApplyPostTest extends AbstractController
{
    /**
     * Customer ID
     */
    private const CUSTOMER_ID = 1;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->customerSession = $this->_objectManager->get(Session::class);
        $this->customerSession->setCustomerId(self::CUSTOMER_ID);
        $this->checkoutSession = $this->_objectManager->create(CheckoutSession::class);
    }

    /**
     * Verifying that recaptcha is present on the CouponPost form/page and keys are configured
     *
     * @magentoDataFixture Magento/Checkout/_files/quote_with_virtual_product_and_address.php
     * @magentoDataFixture Magento/Usps/Fixtures/cart_rule_coupon_free_shipping.php
     *
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/coupon_code invisible
     * @magentoConfigFixture default_store recaptcha_frontend/type_for/coupon_code invisible
     */
    public function testGetRequestIfReCaptchaIsEnabled(): void
    {
        $quote = $this->checkoutSession->getQuote();
        $quote->setData('trigger_recollect', 1)->setTotalsCollectedFlag(true);
        $this->checkSuccessfulGetResponse(true);
    }

    /**
     * Checks the coupon post with ReCaptcha validation when `g-recaptcha-response` missed
     *
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/coupon_code invisible
     *
     * It's  needed for proper work of "ifconfig" in layout during tests running
     * @magentoConfigFixture default_store recaptcha_frontend/type_for/coupon_code invisible
     * @magentoDataFixture Magento/Checkout/_files/quote_with_virtual_product_and_address.php
     */
    public function testPostRequestIfReCaptchaParameterIsMissed(): void
    {
        $this->checkFailedPostRequest();
        $this->assertSessionMessages(
            $this->equalTo(['The coupon code &quot;test&quot; is not valid.']),
            MessageInterface::TYPE_ERROR
        );
    }

    /**
     * Checks the failed coupon post with ReCaptcha validation
     *
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/coupon_code invisible
     *
     * @magentoConfigFixture default_store recaptcha_frontend/type_for/coupon_code invisible
     * @magentoDataFixture Magento/Checkout/_files/quote_with_virtual_product_and_address.php
     */
    public function testPostRequestWithFailedReCaptchaValidation(): void
    {
        $this->checkFailedPostRequest(true);
        $this->assertSessionMessages(
            $this->equalTo(['The coupon code &quot;test&quot; is not valid.']),
            MessageInterface::TYPE_ERROR
        );
    }

    /**
     * Checks GET response
     *
     * @param bool $shouldContainReCaptcha
     * @return void
     */
    private function checkSuccessfulGetResponse(bool $shouldContainReCaptcha = false): void
    {
        $this->getRequest()->setMethod(Http::METHOD_GET);
        $this->dispatch('checkout/cart/');
        $response = $this->getResponse();
        $content = $response->getContent();

        $this->assertNotEmpty($content);
        $shouldContainReCaptcha
            ? $this->assertStringContainsString('field-recaptcha', $content)
            : $this->assertStringNotContainsString('field-recaptcha', $content);

        $this->assertEmpty($this->getSessionMessages(\Magento\Framework\Message\MessageInterface::TYPE_ERROR));
    }

    /**
     * Checks failed sharing process
     *
     * @param bool $withParamReCaptcha
     */
    private function checkFailedPostRequest(bool $withParamReCaptcha = false): void
    {
        $this->makePostRequest($withParamReCaptcha);
    }

    /**
     * Makes post request
     *
     * @param bool $withParamReCaptcha
     * @return void
     */
    private function makePostRequest(bool $withParamReCaptcha = false): void
    {
        $postValue = [
            'remove' => 0,
            'coupon_code' => 'test'
        ];

        if ($withParamReCaptcha) {
            $postValue[CaptchaResponseResolverInterface::PARAM_RECAPTCHA] = 'test';
        }

        $this->getRequest()
            ->setMethod(Http::METHOD_POST)
            ->setPostValue($postValue);
        $this->dispatch('checkout/cart/couponPost/');
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        $this->customerSession->setCustomerId(null);
        parent::tearDown();
    }
}
