<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCheckout\Block\LayoutProcessor\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Framework\Exception\InputException;
use Magento\ReCaptchaApi\Api\CaptchaConfigInterface;
use Magento\ReCaptchaUi\Model\UiConfigResolverInterface;

/**
 * Checkout layout processor
 */
class Onepage implements LayoutProcessorInterface
{
    /**
     * @var UiConfigResolverInterface
     */
    private $captchaUiConfigResolver;

    /**
     * @var CaptchaConfigInterface
     */
    private $captchaConfig;

    /**
     * @param UiConfigResolverInterface $captchaUiConfigResolver
     * @param CaptchaConfigInterface $captchaConfig
     */
    public function __construct(
        UiConfigResolverInterface $captchaUiConfigResolver,
        CaptchaConfigInterface $captchaConfig
    ) {
        $this->captchaUiConfigResolver = $captchaUiConfigResolver;
        $this->captchaConfig = $captchaConfig;
    }

    /**
     * {@inheritdoc}
     *
     * @param array $jsLayout
     * @return array
     * @throws InputException
     */
    public function process($jsLayout)
    {
        $key = 'customer_login';
        if ($this->captchaConfig->isCaptchaEnabledFor($key)) {
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['shippingAddress']['children']['customer-email']['children']
                ['recaptcha']['settings'] = $this->captchaUiConfigResolver->get($key);

            $jsLayout['components']['checkout']['children']['authentication']['children']
                ['recaptcha']['settings'] = $this->captchaUiConfigResolver->get($key);
        } else {
            if (isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['shippingAddress']['children']['customer-email']['children']['recaptcha'])) {
                unset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                    ['shippingAddress']['children']['customer-email']['children']['recaptcha']);
            }

            if (isset($jsLayout['components']['checkout']['children']['authentication']['children']['recaptcha'])) {
                unset($jsLayout['components']['checkout']['children']['authentication']['children']['recaptcha']);
            }
        }
        return $jsLayout;
    }
}
