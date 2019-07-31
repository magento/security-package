<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\Notifier\Test\Integration\Mock;

use MSP\NotifierApi\Model\AdapterEngine\AdapterEngineInterface;

class FakeAdapterEngine implements AdapterEngineInterface
{
    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(string $message, array $params = []): bool
    {
        return true;
    }
}
