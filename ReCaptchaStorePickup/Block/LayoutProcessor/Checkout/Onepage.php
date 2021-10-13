<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaStorePickup\Block\LayoutProcessor\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\ReCaptchaUi\Model\UiConfigResolverInterface;

/**
 * Provides reCaptcha component configuration.
 */
class Onepage implements LayoutProcessorInterface
{
    /**
     * @var UiConfigResolverInterface
     */
    private $captchaUiConfigResolver;

    /**
     * @var IsCaptchaEnabledInterface
     */
    private $isCaptchaEnabled;

    /**
     * @param UiConfigResolverInterface $captchaUiConfigResolver
     * @param IsCaptchaEnabledInterface $isCaptchaEnabled
     */
    public function __construct(
        UiConfigResolverInterface $captchaUiConfigResolver,
        IsCaptchaEnabledInterface $isCaptchaEnabled
    ) {
        $this->captchaUiConfigResolver = $captchaUiConfigResolver;
        $this->isCaptchaEnabled = $isCaptchaEnabled;
    }

    /**
     * @inheritdoc
     */
    public function process($jsLayout)
    {
        $key = 'customer_login';
        if ($this->isCaptchaEnabled->isCaptchaEnabledFor($key)) {
            $jsLayout['components']['checkout']['children']['steps']['children']['store-pickup']['children']
            ['store-selector']['children']['customer-email']['children']['additional-login-form-fields']['children']
            ['recaptcha']['settings'] = $this->captchaUiConfigResolver->get($key);
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['customer-email']['children']
            ['recaptcha']['settings'] = $this->captchaUiConfigResolver->get($key);
        } else {
            if (isset($jsLayout['components']['checkout']['children']['steps']['children']['store-pickup']['children']
                ['store-selector']['children']['customer-email']['children']['additional-login-form-fields']['children']
                ['recaptcha'])
            ) {
                unset($jsLayout['components']['checkout']['children']['steps']['children']['store-pickup']['children']
                    ['store-selector']['children']['customer-email']['children']['additional-login-form-fields']
                    ['children']['recaptcha']);
                unset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                    ['shippingAddress']['children']['customer-email']['children']['recaptcha']);
            }
        }

        return $jsLayout;
    }
}
