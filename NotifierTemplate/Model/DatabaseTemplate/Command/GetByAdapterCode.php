<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplate\Model\DatabaseTemplate\Command;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\NotifierTemplate\Model\ResourceModel\DatabaseTemplate;
use Magento\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface;
use Magento\NotifierTemplateApi\Api\Data\DatabaseTemplateInterfaceFactory;

/**
 * @inheritdoc
 */
class GetByAdapterCode implements GetByAdapterCodeInterface
{
    /**
     * @var DatabaseTemplate
     */
    private $resource;

    /**
     * @var DatabaseTemplateInterfaceFactory
     */
    private $factory;

    /**
     * @param DatabaseTemplate $resource
     * @param DatabaseTemplateInterfaceFactory $factory
     */
    public function __construct(
        DatabaseTemplate $resource,
        DatabaseTemplateInterfaceFactory $factory
    ) {
        $this->resource = $resource;
        $this->factory = $factory;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $adapterCode): DatabaseTemplateInterface
    {
        /** @var DatabaseTemplateInterface $databaseTemplate */
        $databaseTemplate = $this->factory->create();
        $this->resource->load(
            $databaseTemplate,
            $adapterCode,
            DatabaseTemplateInterface::ADAPTER_CODE
        );

        if (null === $databaseTemplate->getId()) {
            throw new NoSuchEntityException(__('DatabaseTemplate with adapterCode "%value" does not exist.', [
                'value' => $adapterCode
            ]));
        }

        return $databaseTemplate;
    }
}
