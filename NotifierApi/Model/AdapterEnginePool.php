<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\NotifierApi\Model;

use InvalidArgumentException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\NotifierApi\Api\Data\AdapterInterface;
use Magento\NotifierApi\Api\AdapterEnginePoolInterface;
use Magento\NotifierApi\Model\AdapterEngine\AdapterEngineInterface;

class AdapterEnginePool implements AdapterEnginePoolInterface
{
    /**
     * @var AdapterEngineInterface[]
     */
    private $adapterEngines;

    /**
     * @param AdapterEngineInterface[] $adapterEngines
     */
    public function __construct(array $adapterEngines)
    {
        $this->adapterEngines = $adapterEngines;
    }

    /**
     * @inheritdoc
     */
    public function getAdapterEngines(): array
    {
        foreach ($this->adapterEngines as $k => $adapterEngine) {
            if (!($adapterEngine instanceof AdapterInterface)) {
                throw new InvalidArgumentException('Adapter engine' . $k . ' must implement AdapterEngineInterface');
            }
        }

        return $this->adapterEngines;
    }

    /**
     * @inheritdoc
     */
    public function getAdapterEngineByCode(string $code): AdapterEngineInterface
    {
        if (!isset($this->adapterEngines[$code])) {
            throw new NoSuchEntityException(__('Adapter engine %1 not found', $code));
        }

        $adapterEngine = $this->adapterEngines[$code];
        if (!($adapterEngine instanceof AdapterEngineInterface)) {
            throw new InvalidArgumentException('Adapter engine ' . $code . ' must implement AdapterEngineInterface');
        }

        return $adapterEngine;
    }
}
