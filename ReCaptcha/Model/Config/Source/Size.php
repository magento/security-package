<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Recaptcha size options
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
