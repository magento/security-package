<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCheckout\Block\LayoutProcessor\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\ReCaptchaApi\Api\CaptchaConfigInterface;
use Magento\ReCaptchaUi\Model\CaptchaUiSettingsProviderInterface;

/**
 * Checkout layout processor
 */
class Onepage implements LayoutProcessorInterface
{
    /**
     * @var CaptchaUiSettingsProviderInterface
     */
    private $captchaUiSettingsProvider;

    /**
     * @var CaptchaConfigInterface
     */
    private $captchaConfig;

    /**
     * @param CaptchaUiSettingsProviderInterface $captchaUiSettingsProvider
     * @param CaptchaConfigInterface $captchaConfig
     */
    public function __construct(
        CaptchaUiSettingsProviderInterface $captchaUiSettingsProvider,
        CaptchaConfigInterface $captchaConfig
    ) {
        $this->captchaUiSettingsProvider = $captchaUiSettingsProvider;
        $this->captchaConfig = $captchaConfig;
    }

    /**
     * @inheritDoc
     */
    public function process($jsLayout)
    {
        if ($this->captchaConfig->areKeysConfigured()) {
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['shippingAddress']['children']['customer-email']['children']
                ['recaptcha']['settings'] = $this->captchaUiSettingsProvider->get();

            $jsLayout['components']['checkout']['children']['authentication']['children']
                ['recaptcha']['settings'] = $this->captchaUiSettingsProvider->get();
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
