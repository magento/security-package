<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierApi\Api;

use Magento\NotifierApi\Api\Data\AdapterInterface;

/**
 * TODO
 *
 * @api
 */
interface GetAdapterByChannelCodeInterface
{
    /**
     * TODO
     *
     * @param string $channelCode
     * @return AdapterInterface
     */
    public function execute(string $channelCode): AdapterInterface;
}
