<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEventApi\Api;

interface RuleSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get an array of objects
     * @return \Magento\NotifierEventApi\Api\Data\RuleInterface[]
     */
    public function getItems();

    /**
     * Set objects list
     * @param \Magento\NotifierEventApi\Api\Data\RuleInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
