<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ReCaptchaCheckoutSalesRule\Plugin;

use Magento\Checkout\Block\Cart\Coupon;
use Magento\ReCaptchaUi\Block\ReCaptcha;

/**
 * Reset attempts for frontend checkout
 */
class CouponSetLayoutPlugin
{
    /**
     * Add Child captcha afterLayout in Coupon form
     *
     * @param Coupon $subject
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
