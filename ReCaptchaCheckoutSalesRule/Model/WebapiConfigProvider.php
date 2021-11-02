<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCheckoutSalesRule\Model;

use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\ReCaptchaUi\Model\ValidationConfigResolverInterface;
use Magento\ReCaptchaValidationApi\Api\Data\ValidationConfigInterface;
use Magento\ReCaptchaWebapiApi\Api\Data\EndpointInterface;
use Magento\ReCaptchaWebapiApi\Api\WebapiValidationConfigProviderInterface;

/**
 * Provide checkout related endpoint configuration.
 */
class WebapiConfigProvider implements WebapiValidationConfigProviderInterface
{
    private const CAPTCHA_ID = 'coupon_code';

    /**
     * @var IsCaptchaEnabledInterface
     */
    private $isEnabled;

    /**
     * @var ValidationConfigResolverInterface
     */
    private $configResolver;

    /**
     * @param IsCaptchaEnabledInterface $isEnabled
     * @param ValidationConfigResolverInterface $configResolver
     */
    public function __construct(IsCaptchaEnabledInterface $isEnabled, ValidationConfigResolverInterface $configResolver)
    {
        $this->isEnabled = $isEnabled;
        $this->configResolver = $configResolver;
    }

    /**
     * @inheritDoc
     */
    public function getConfigFor(EndpointInterface $endpoint): ?ValidationConfigInterface
    {
        //phpcs:disable Magento2.PHP.LiteralNamespaces
        if ((($endpoint->getServiceClass() === 'Magento\Quote\Api\CouponManagementInterface' ||
                    $endpoint->getServiceClass() === 'Magento\Quote\Api\GuestCouponManagementInterface') &&
                $endpoint->getServiceMethod() === 'set')
            || ($endpoint->getServiceClass() === 'Magento\QuoteGraphQl\Model\Resolver\ApplyCouponToCart'
                || $endpoint->getServiceMethod() === 'ApplyCouponToCart')
        ) {
            if ($this->isEnabled->isCaptchaEnabledFor(self::CAPTCHA_ID)) {
                return $this->configResolver->get(self::CAPTCHA_ID);
            }
        }
        //phpcs:enable Magento2.PHP.LiteralNamespaces

        return null;
    }
}
