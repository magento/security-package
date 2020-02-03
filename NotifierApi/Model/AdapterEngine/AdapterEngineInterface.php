<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierApi\Model\AdapterEngine;

use Magento\NotifierApi\Api\Data\MessageInterface;

/**
 * Notifier adapter interface - SPI
 *
 * @api
 */
interface AdapterEngineInterface
{
    /**
     * TODO
     *
     * Throws exception on failure.
     *
     * @param MessageInterface $message
     */
    public function execute(MessageInterface $message): void;
}
