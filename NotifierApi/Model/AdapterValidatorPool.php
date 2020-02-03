<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\NotifierApi\Model;

use InvalidArgumentException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\NotifierApi\Api\AdapterValidatorPoolInterface;
use Magento\NotifierApi\Model\AdapterEngine\AdapterValidatorInterface;

class AdapterValidatorPool implements AdapterValidatorPoolInterface
{
    /**
     * @var AdapterValidatorInterface[]
     */
    private $adapterValidators;

    /**
     * @param AdapterValidatorInterface[] $adapterValidators
     */
    public function __construct(array $adapterValidators)
    {
        $this->adapterValidators = $adapterValidators;
    }

    /**
     * @inheritdoc
     */
    public function getAdapterValidators(): array
    {
        foreach ($this->adapterValidators as $k => $adapterValidator) {
            if (!($adapterValidator instanceof AdapterValidatorInterface)) {
                throw new InvalidArgumentException('Adapter validator ' . $k . ' must implement AdapterValidatorInterface');
            }
        }

        return $this->adapterValidators;
    }

    /**
     * @inheritdoc
     */
    public function getAdapterValidatorByCode(string $code): AdapterValidatorInterface
    {
        if (!isset($this->adapterValidators[$code])) {
            throw new NoSuchEntityException(__('Adapter validator %1 not found', $code));
        }

        $adapterValidator = $this->adapterValidators[$code];
        if (!($adapterValidator instanceof AdapterValidatorInterface)) {
            throw new InvalidArgumentException('Adapter validator ' . $code . ' must implement AdapterValidatorInterface');
        }


        return $adapterValidator;
    }
}
