<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\Notifier\Model;

use Magento\Framework\Api\SearchResults;

class ChannelSearchResults extends SearchResults implements
    \MSP\NotifierApi\Api\ChannelSearchResultsInterface
{
}
