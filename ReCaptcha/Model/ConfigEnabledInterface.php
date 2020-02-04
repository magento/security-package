<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model;

/**
 * Extension point of the ReCaptcha configuration
 *
 * @api
 */
interface ConfigEnabledInterface
{
    /**
     * Return true if functionality of corresponding point is enabled in configuration
     * @return bool
     */
    public function isEnabled(): bool;
}
