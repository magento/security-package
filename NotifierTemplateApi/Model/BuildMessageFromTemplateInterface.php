<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplateApi\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\NotifierApi\Api\Data\MessageInterface;

/**
 * Create notifier message from template.
 *
 * @api
 */
interface BuildMessageFromTemplateInterface
{
    /**
     * Create notifier message from template.
     *
     * @param string $channelCode
     * @param string $template
     * @param array $params
     * @return MessageInterface
     * @throws NoSuchEntityException
     */
    public function execute(string $channelCode, string $template, array $params = []): MessageInterface;
}
