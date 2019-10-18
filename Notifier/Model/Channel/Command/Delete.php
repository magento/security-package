<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Notifier\Model\Channel\Command;

use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Notifier\Model\ResourceModel\Channel;
use Psr\Log\LoggerInterface;

/**
 * @inheritdoc
 */
class Delete implements DeleteInterface
{
    /**
     * @var Channel
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
     * @param Channel $resource
     * @param GetInterface $commandGet
     * @param LoggerInterface $logger
     */
    public function __construct(
        Channel $resource,
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
    public function execute(int $channelId): void
    {
        try {
            $channel = $this->commandGet->execute($channelId);
        } catch (NoSuchEntityException $e) {
            return;
        }

        try {
            $this->resource->delete($channel);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CouldNotDeleteException(__('Could not delete Channel'), $e);
        }
    }
}
