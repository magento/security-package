<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\NotifierSecurity\Model;

interface NotifierInterface
{
    /**
     * @param string $eventName
     * @param array $eventData
     * @return void
     */
    public function execute(string $eventName, array $eventData): void;
}
