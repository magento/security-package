<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierTemplate\Model\DatabaseTemplate\Command;

use Magento\Framework\Exception\NoSuchEntityException;
use MSP\NotifierTemplate\Model\ResourceModel\DatabaseTemplate;
use MSP\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface;
use MSP\NotifierTemplateApi\Api\Data\DatabaseTemplateInterfaceFactory;

/**
 * @inheritdoc
 */
class GetByCode implements GetByCodeInterface
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
    public function execute(string $code): DatabaseTemplateInterface
    {
        /** @var DatabaseTemplateInterface $databaseTemplate */
        $databaseTemplate = $this->factory->create();
        $this->resource->load(
            $databaseTemplate,
            $code,
            'code'
        );

        if (null === $databaseTemplate->getId()) {
            throw new NoSuchEntityException(__('DatabaseTemplate with code "%value" does not exist.', [
                'value' => $code
            ]));
        }

        return $databaseTemplate;
    }
}
