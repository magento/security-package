<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplateApi\Model;

interface SendMessageInterface
{
    /**
     * Send a template formatted message
     * @param string $channelCode
     * @param string $template
     * @param array $params
     * @return bool
     */
    public function execute(string $channelCode, string $template, array $params = []): bool;
}
