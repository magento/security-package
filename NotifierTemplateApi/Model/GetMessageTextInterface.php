<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplateApi\Model;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Get message text from template - SPI
 *
 * @api
 */
interface GetMessageTextInterface
{
    /**
     * Get a template formatted message
     * @param string $channelCode
     * @param string $templateId
     * @param array $params
     * @return string
     * @throws NoSuchEntityException
     */
    public function execute(string $channelCode, string $templateId, array $params = []): string;
}
