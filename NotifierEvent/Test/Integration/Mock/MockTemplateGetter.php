<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEvent\Test\Integration\Mock;

use Magento\Framework\Exception\FileSystemException;
use Magento\NotifierTemplateApi\Model\TemplateGetter\TemplateGetterInterface;

class MockTemplateGetter implements TemplateGetterInterface
{
    /**
     * @inheritDoc
     * @throws FileSystemException
     */
    public function getTemplate(string $channelCode, string $templateId): ?string
    {
        if ($templateId === 'event:unknown_event') {
            throw new FileSystemException(__('Template not found'));
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    public function getList(): array
    {
        return [];
    }
}
