<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplate\Model;

use Magento\Framework\Api\SearchResults;
use Magento\NotifierTemplateApi\Api\DatabaseTemplateSearchResultsInterface;

class DatabaseTemplateSearchResults extends SearchResults implements
    DatabaseTemplateSearchResultsInterface
{
}
