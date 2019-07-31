<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierTemplate\Model;

use Magento\Framework\Api\SearchResults;
use MSP\NotifierTemplateApi\Api\DatabaseTemplateSearchResultsInterface;

class DatabaseTemplateSearchResults extends SearchResults implements
    DatabaseTemplateSearchResultsInterface
{
}
