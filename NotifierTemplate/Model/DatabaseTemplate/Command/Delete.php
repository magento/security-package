<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplate\Model\DatabaseTemplate\Command;

use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\NotifierTemplate\Model\ResourceModel\DatabaseTemplate;
use Magento\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface;
use Psr\Log\LoggerInterface;

/**
 * @inheritdoc
 */
class Delete implements DeleteInterface
{
    /**
     * @var DatabaseTemplate
     */
    private $resource;

    /**
     * @var GetInterface
     */
    private $commandGet;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param DatabaseTemplate $resource
     * @param GetInterface $commandGet
     * @param LoggerInterface $logger
     */
    public function __construct(
        DatabaseTemplate $resource,
        GetInterface $commandGet,
        LoggerInterface $logger
    ) {
        $this->resource = $resource;
        $this->commandGet = $commandGet;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function execute(int $databaseTemplateId): void
    {
        /** @var DatabaseTemplateInterface $databaseTemplate */
        try {
            $databaseTemplate = $this->commandGet->execute($databaseTemplateId);
        } catch (NoSuchEntityException $e) {
            return;
        }

        try {
            $this->resource->delete($databaseTemplate);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CouldNotDeleteException(__('Could not delete DatabaseTemplate'), $e);
        }
    }
}
