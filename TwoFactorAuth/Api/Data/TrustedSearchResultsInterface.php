<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Trusted devices search results interface
 *
 * @deprecated Trusted Devices functionality was removed.
 */
interface TrustedSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get an array of objects
     * @return TrustedInterface[]
     */
    public function getItems(): array;

    /**
     * Set objects list
     * @param TrustedInterface[] $items
     * @return TrustedSearchResultsInterface
     */
    public function setItems(array $items): TrustedSearchResultsInterface;
}
