<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEvent\Model;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\NotifierEvent\Model\ResourceModel\Rule\CollectionFactory;
use Magento\NotifierEventApi\Api\Data\RuleInterface;

class GetRulesIdsByEventRegistry
{
    /**
     * @var array
     */
    private $rulesRegistryMap;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param CollectionFactory $collectionFactory
     * @param SerializerInterface $serializer
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        SerializerInterface $serializer
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->serializer = $serializer;
    }

    /**
     * Builds a rule registry map
     */
    private function buildRulesRegistryMap(): void
    {
        $this->rulesRegistryMap = [];

        $collection = $this->collectionFactory->create();
        $collection
            ->addFieldToFilter('enabled', true);

        foreach ($collection as $rule) {
            if (!$rule->getEvents()) {
                continue;
            }

            /** @var RuleInterface $rule */
            $events = $this->serializer->unserialize($rule->getEvents());
            $events = array_unique(array_map('strtolower', $events));

            foreach ($events as $event) {
                if (!isset($this->rulesRegistryMap[$event])) {
                    $this->rulesRegistryMap[$event] = [];
                }

                $this->rulesRegistryMap[$event][] = $rule->getId();
            }
        }
    }

    /**
     * Clear internal registry
     */
    public function clearRegistry(): void
    {
        $this->rulesRegistryMap = null;
    }

    /**
     * @param string $eventName
     * @return array
     */
    public function get(string $eventName): array
    {
        if ($this->rulesRegistryMap === null) {
            $this->buildRulesRegistryMap();
        }

        return $this->rulesRegistryMap[$eventName] ?? [];
    }
}
