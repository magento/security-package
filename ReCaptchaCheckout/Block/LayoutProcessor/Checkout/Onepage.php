<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCheckout\Block\LayoutProcessor\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\ReCaptcha\Model\CaptchaConfigInterface;
use Magento\ReCaptchaFrontendUi\Model\LayoutSettings;

/**
 * Checkout layout processor
 */
class Onepage implements LayoutProcessorInterface
{
    /**
     * @var LayoutSettings
     */
    private $layoutSettings;

    /**
     * @var CaptchaConfigInterface
     */
    private $captchaConfig;

    /**
     * @param LayoutSettings $layoutSettings
     * @param CaptchaConfigInterface $captchaConfig
     */
    public function __construct(
        LayoutSettings $layoutSettings,
        CaptchaConfigInterface $captchaConfig
    ) {
        $this->layoutSettings = $layoutSettings;
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
                ['recaptcha']['settings'] = $this->layoutSettings->getCaptchaSettings();

            $jsLayout['components']['checkout']['children']['authentication']['children']
                ['recaptcha']['settings'] = $this->layoutSettings->getCaptchaSettings();
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
