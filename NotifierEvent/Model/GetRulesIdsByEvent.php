<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEvent\Model;

use Magento\NotifierEventApi\Model\GetRulesIdsByEventInterface;

/**
 * @inheritdoc
 */
class GetRulesIdsByEvent implements GetRulesIdsByEventInterface
{
    /**
     * @var GetRulesIdsByEventRegistry
     */
    private $getRulesIdsByEventRegistry;

    /**
     * @param GetRulesIdsByEventRegistry $getRulesIdsByEventRegistry
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        GetRulesIdsByEventRegistry $getRulesIdsByEventRegistry
    ) {
        $this->getRulesIdsByEventRegistry = $getRulesIdsByEventRegistry;
    }

    /**
     * @param string $eventName
     * @return array
     */
    public function execute(string $eventName): array
    {
        return $this->getRulesIdsByEventRegistry->get($eventName);
    }
}
