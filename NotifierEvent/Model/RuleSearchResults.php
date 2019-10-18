<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEvent\Model;

use Magento\Framework\Api\SearchResults;

class RuleSearchResults extends SearchResults implements
    \Magento\NotifierEventApi\Api\RuleSearchResultsInterface
{
}
