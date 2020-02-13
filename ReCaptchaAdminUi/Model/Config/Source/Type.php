<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaAdminUi\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Recaptcha type options
 */
class Type implements OptionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'recaptcha_v3', 'label' => __('Invisible reCAPTCHA v3')],
            ['value' => 'invisible', 'label' => __('Invisible reCAPTCHA v2')],
            ['value' => 'recaptcha', 'label' => __('reCAPTCHA v2')],
        ];
    }
}
