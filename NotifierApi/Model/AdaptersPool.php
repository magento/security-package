<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierApi\Model;

use InvalidArgumentException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\NotifierApi\Api\AdapterInterface;
use Magento\NotifierApi\Api\AdaptersPoolInterface;

class AdaptersPool implements AdaptersPoolInterface
{
    /**
     * @var AdapterInterface[]
     */
    private $adapters;

    /**
     * AdapterRepository constructor.
     * @param AdapterInterface[] $adapters
     * @throws InvalidArgumentException
     */
    public function __construct(array $adapters)
    {
        $this->adapters = $adapters;

        foreach ($this->adapters as $k => $adapter) {
            if (!($adapter instanceof AdapterInterface)) {
                throw new InvalidArgumentException('Adapter ' . $k . ' must implement AdapterInterface');
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getAdapters(): array
    {
        return $this->adapters;
    }

    /**
     * @inheritdoc
     */
    public function getAdapterByCode(string $code): AdapterInterface
    {
        if (!isset($this->adapters[$code])) {
            throw new NoSuchEntityException(__('Adapter %1 not found', $code));
        }

        return $this->adapters[$code];
    }
}
