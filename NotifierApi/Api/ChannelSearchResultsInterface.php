<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierApi\Api;

/**
 * Channel results interface
 * @api
 */
interface ChannelSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get an array of objects
     * @return \MSP\NotifierApi\Api\Data\ChannelInterface[]
     */
    public function getItems();

    /**
     * Set objects list
     * @param \MSP\NotifierApi\Api\Data\ChannelInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
