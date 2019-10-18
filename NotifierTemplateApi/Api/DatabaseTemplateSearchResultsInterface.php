<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplateApi\Api;

interface DatabaseTemplateSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get an array of objects
     * @return \Magento\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface[]
     */
    public function getItems();

    /**
     * Set objects list
     * @param \Magento\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
