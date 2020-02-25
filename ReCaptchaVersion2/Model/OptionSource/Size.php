<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaVersion2\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * reCAPTCHA sizes
 *
 * Extension point for adding reCAPTCHA sizes
 * Applicable only for visible captcha type ("reCAPTCHA v2")
 *
 * @api
 */
class Size implements OptionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'normal', 'label' => __('Normal')],
            ['value' => 'compact', 'label' => __('Compact')],
        ];
    }
}
