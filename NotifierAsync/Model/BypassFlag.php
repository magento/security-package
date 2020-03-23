<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierAsync\Model;

/**
 * Class for Bypass Flag
 */
class BypassFlag
{
    /**
     * @var bool
     */
    private $status = false;

    /**
     * Set Status
     *
     * @param bool $status
     */
    public function setStatus(bool $status): void
    {
        $this->status = $status;
    }

    /**
     * Get Status
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getStatus(): bool
    {
        return $this->status;
    }
}
