<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Notifier\Test\Integration\Mock;

use Magento\NotifierApi\Api\Data\MessageInterface;
use Magento\NotifierApi\Model\AdapterEngine\AdapterEngineInterface;

class FakeAdapterEngine implements AdapterEngineInterface
{
    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(MessageInterface $message): void {}
}
