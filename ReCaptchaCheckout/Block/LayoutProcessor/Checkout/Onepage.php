<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCheckout\Block\LayoutProcessor\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\ReCaptcha\Model\ConfigInterface;
use Magento\ReCaptcha\Model\LayoutSettings;

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
     * @var ConfigInterface
     */
    private $config;

    /**
     * @param LayoutSettings $layoutSettings
     * @param ConfigInterface $config
     */
    public function __construct(
        LayoutSettings $layoutSettings,
        ConfigInterface $config
    ) {
        $this->layoutSettings = $layoutSettings;
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function process($jsLayout)
    {
        if ($this->config->isEnabledFrontend()) {
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['shippingAddress']['children']['customer-email']['children']
                ['recaptcha']['settings'] = $this->layoutSettings->getCaptchaSettings();

            $jsLayout['components']['checkout']['children']['authentication']['children']
                ['recaptcha']['settings'] = $this->layoutSettings->getCaptchaSettings();
        }

        if (!$this->config->isEnabledFrontend()) {
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
