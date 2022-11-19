<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaPaypal\Plugin;

use Magento\Framework\Webapi\Rest\Request;
use Magento\Paypal\Model\Config;
use Magento\ReCaptchaCheckout\Model\WebapiConfigProvider;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\ReCaptchaValidationApi\Api\Data\ValidationConfigInterface;
use Magento\ReCaptchaWebapiApi\Api\Data\EndpointInterface;

class SkipPlaceOrderRecaptchaValidation
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
     * @param IsCaptchaEnabledInterface $isCaptchaEnabled
     * @param Request $request
     */
    public function __construct(
        IsCaptchaEnabledInterface $isCaptchaEnabled,
        Request $request
    ) {
        $this->isCaptchaEnabled = $isCaptchaEnabled;
        $this->request = $request;
    }

    /**
     * Skip captcha validation for "place order" button if captcha is enabled for payflowpro
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
            if (isset($paymentMethod['method']) && $paymentMethod['method'] === Config::METHOD_PAYFLOWPRO) {
                return null;
            }
        }

        return $result;
    }
}
