<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierApi\Api;

use Magento\NotifierApi\Api\Data\MessageInterface;

/**
 * TODO
 *
 * @api
 */
interface BuildMessageInterface
{
    /**
     * TODO
     *
     * @param string $messageText
     * @param array $params
     * @return MessageInterface
     */
    public function execute(string $messageText, array $params): MessageInterface;
}
