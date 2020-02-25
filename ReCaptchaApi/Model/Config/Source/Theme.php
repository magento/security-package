<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaApi\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * reCAPTCHA themes
 *
 * Extension point for adding reCAPTCHA themes
 * Applicable only for visible captcha type (for example "reCAPTCHA v2")
 *
 * @api
 */
class Theme implements OptionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'light', 'label' => __('Light Theme')],
            ['value' => 'dark', 'label' => __('Dark Theme')],
        ];
    }
}
