<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplate\Model\DatabaseTemplate\Command;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\NotifierTemplateApi\Model\DatabaseTemplate\Validator\ValidateDatabaseTemplateInterface;
use Magento\NotifierTemplate\Model\ResourceModel\DatabaseTemplate;
use Magento\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface;
use Psr\Log\LoggerInterface;

/**
 * @inheritdoc
 */
class Save implements SaveInterface
{
    /**
     * @var DatabaseTemplate
     */
    private $resource;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ValidateDatabaseTemplateInterface
     */
    private $validateDatabaseTemplate;

    /**
     * @param DatabaseTemplate $resource
     * @param ValidateDatabaseTemplateInterface $validateDatabaseTemplate
     * @SuppressWarnings(PHPMD.LongVariables)
     */
    public function __construct(
        DatabaseTemplate $resource,
        ValidateDatabaseTemplateInterface $validateDatabaseTemplate,
        LoggerInterface $logger
    ) {
        $this->resource = $resource;
        $this->logger = $logger;
        $this->validateDatabaseTemplate = $validateDatabaseTemplate;
    }

    /**
     * @inheritdoc
     */
    public function execute(DatabaseTemplateInterface $databaseTemplate): int
    {
        $this->validateDatabaseTemplate->execute($databaseTemplate);

        try {
            $this->resource->save($databaseTemplate);
            return (int) $databaseTemplate->getId();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CouldNotSaveException(__('Could not save DatabaseTemplate'), $e);
        }
    }
}
