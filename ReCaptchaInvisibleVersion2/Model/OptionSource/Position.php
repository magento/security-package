<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaInvisibleVersion2\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * reCAPTCHA positions
 *
 * Extension point for adding reCAPTCHA positions
 * Applicable only to Invisible reCAPTCHA type (Invisible reCAPTCHA v2)
 *
 * @api
 */
class Position implements OptionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'inline', 'label' => __('Inline')],
            ['value' => 'bottomright', 'label' => __('Bottom Right')],
            ['value' => 'bottomleft', 'label' => __('Bottom Left')],
        ];
    }
}
