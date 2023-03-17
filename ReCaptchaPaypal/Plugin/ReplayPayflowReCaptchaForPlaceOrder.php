<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaPaypal\Plugin;

use Magento\Framework\Webapi\Rest\Request;
use Magento\Paypal\Model\Config;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\ReCaptchaCheckout\Model\WebapiConfigProvider;
use Magento\ReCaptchaPaypal\Model\ReCaptchaSession;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\ReCaptchaValidationApi\Api\Data\ValidationConfigInterface;
use Magento\ReCaptchaWebapiApi\Api\Data\EndpointInterface;

class ReplayPayflowReCaptchaForPlaceOrder
{
    private const PAYPAL_PAYFLOWPRO_CAPTCHA_ID = 'paypal_payflowpro';

    /**
     * @var IsCaptchaEnabledInterface
     */
    private IsCaptchaEnabledInterface $isCaptchaEnabled;

    /**
     * @var Request
     */
    private Request $request;

    /**
     * @var ReCaptchaSession
     */
    private ReCaptchaSession $reCaptchaSession;

    /**
     * @var QuoteIdMaskFactory
     */
    private QuoteIdMaskFactory $quoteIdMaskFactory;

    /**
     * @param IsCaptchaEnabledInterface $isCaptchaEnabled
     * @param Request $request
     * @param ReCaptchaSession $reCaptchaSession
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     */
    public function __construct(
        IsCaptchaEnabledInterface $isCaptchaEnabled,
        Request $request,
        ReCaptchaSession $reCaptchaSession,
        QuoteIdMaskFactory $quoteIdMaskFactory
    ) {
        $this->isCaptchaEnabled = $isCaptchaEnabled;
        $this->request = $request;
        $this->reCaptchaSession = $reCaptchaSession;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
    }

    /**
     * Skip reCaptcha validation for "place order" button if captcha is enabled for payflowpro
     *
     * @param WebapiConfigProvider $subject
     * @param ValidationConfigInterface $result
     * @param EndpointInterface $endpoint
     * @return ValidationConfigInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetConfigFor(
        WebapiConfigProvider $subject,
        ?ValidationConfigInterface $result,
        EndpointInterface $endpoint
    ): ?ValidationConfigInterface {

        if ($result && $this->isCaptchaEnabled->isCaptchaEnabledFor(self::PAYPAL_PAYFLOWPRO_CAPTCHA_ID)) {
            $bodyParams = $this->request->getBodyParams();
            $paymentMethod = $bodyParams['paymentMethod'] ?? $bodyParams['payment_method'] ?? [];
            $cartId = $bodyParams['cartId'] ?? $bodyParams['cart_id'] ?? null;
            if (isset($paymentMethod['method'])
                && $paymentMethod['method'] === Config::METHOD_PAYFLOWPRO
                && $cartId
            ) {
                // check if it is guest cart, then resolve cart id by mask ID
                if (!is_numeric($cartId)) {
                    $cartId = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id')->getQuoteId();
                }
                if ($this->reCaptchaSession->isValid((int) $cartId)) {
                    return null;
                }
            }
        }

        return $result;
    }
}
