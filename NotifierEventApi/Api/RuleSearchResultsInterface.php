<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierEventApi\Api;

interface RuleSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get an array of objects
     * @return \MSP\NotifierEventApi\Api\Data\RuleInterface[]
     */
    public function getItems();

    /**
     * Set objects list
     * @param \MSP\NotifierEventApi\Api\Data\RuleInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
