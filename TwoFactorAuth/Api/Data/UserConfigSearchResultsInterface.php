<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * User config search result
 *
 * @api
 */
interface UserConfigSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get an array of objects
     *
     * @return UserConfigInterface[]
     */
    public function getItems(): array;

    /**
     * Set objects list
     *
     * @param UserConfigInterface[] $items
     * @return UserConfigSearchResultsInterface
     */
    public function setItems(array $items): UserConfigSearchResultsInterface;
}
