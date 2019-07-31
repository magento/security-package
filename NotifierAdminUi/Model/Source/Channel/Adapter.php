<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierAdminUi\Model\Source\Channel;

use Magento\Framework\Data\OptionSourceInterface;
use MSP\NotifierApi\Api\AdaptersPoolInterface;

class Adapter implements OptionSourceInterface
{
    /**
     * @var AdaptersPoolInterface
     */
    private $adapterRepository;

    /**
     * Adapter constructor.
     * @param AdaptersPoolInterface $adapterRepository
     */
    public function __construct(
        AdaptersPoolInterface $adapterRepository
    ) {
        $this->adapterRepository = $adapterRepository;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray(): array
    {
        $adapters = $this->adapterRepository->getAdapters();

        $res = [];
        foreach ($adapters as $adapter) {
            $res[] = [
                'value' => $adapter->getCode(),
                'label' => $adapter->getDescription(),
            ];
        }

        return $res;
    }
}
