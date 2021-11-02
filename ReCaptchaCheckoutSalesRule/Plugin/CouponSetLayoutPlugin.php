<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ReCaptchaCheckoutSalesRule\Plugin;

use Magento\Checkout\Block\Cart\Coupon;
use Magento\ReCaptchaUi\Block\ReCaptcha;

/**
 * Plugin for adding recaptcha in coupon form
 */
class CouponSetLayoutPlugin
{
    /**
     * Add Child ReCaptcha in Coupon form
     *
     * @param Coupon $subject
     * @return Coupon
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSetLayout(Coupon $subject): Coupon
    {
        if (!$subject->getChildBlock('captcha')) {
            $subject->addChild(
                'captcha',
                ReCaptcha::class,
                [
                    'jsLayout' => [
                        'components' => [
                            'captcha' => ['component' => 'Magento_ReCaptchaFrontendUi/js/reCaptcha']
                        ]
                    ]
                ]
            );
        }
        return $subject;
    }
}
