<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\Notifier\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use MSP\Notifier\Model\Channel\Command\DeleteInterface;
use MSP\Notifier\Model\Channel\Command\GetByCodeInterface;
use MSP\Notifier\Model\Channel\Command\GetInterface;
use MSP\Notifier\Model\Channel\Command\GetListInterface;
use MSP\Notifier\Model\Channel\Command\SaveInterface;
use MSP\NotifierApi\Api\ChannelRepositoryInterface;
use MSP\NotifierApi\Api\ChannelSearchResultsInterface;
use MSP\NotifierApi\Api\Data\ChannelInterface;

/**
 * @SuppressWarnings(PHPMD.ShortVariable)
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ChannelRepository implements ChannelRepositoryInterface
{
    /**
     * @var SaveInterface
     */
    private $commandSave;

    /**
     * @var GetInterface
     */
    private $commandGet;

    /**
     * @var DeleteInterface
     */
    private $commandDeleteById;

    /**
     * @var GetListInterface
     */
    private $commandGetList;

    /**
     * @var GetByCodeInterface
     */
    private $commandGetByCode;

    /**
     * @param SaveInterface $commandSave
     * @param GetInterface $commandGet
     * @param GetByCodeInterface $commandGetByCode
     * @param DeleteInterface $commandDeleteById
     * @param GetListInterface $commandGetList
     */
    public function __construct(
        SaveInterface $commandSave,
        GetInterface $commandGet,
        GetByCodeInterface $commandGetByCode,
        DeleteInterface $commandDeleteById,
        GetListInterface $commandGetList
    ) {
        $this->commandSave = $commandSave;
        $this->commandGet = $commandGet;
        $this->commandDeleteById = $commandDeleteById;
        $this->commandGetList = $commandGetList;
        $this->commandGetByCode = $commandGetByCode;
    }

    /**
     * @inheritdoc
     */
    public function save(ChannelInterface $channel): int
    {
        return $this->commandSave->execute($channel);
    }

    /**
     * @inheritdoc
     */
    public function get(int $channelId): ChannelInterface
    {
        return $this->commandGet->execute($channelId);
    }

    /**
     * @inheritdoc
     */
    public function getByCode(string $code): ChannelInterface
    {
        return $this->commandGetByCode->execute($code);
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $channelId): void
    {
        $this->commandDeleteById->execute($channelId);
    }

    /**
     * @inheritdoc
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria = null
    ): ChannelSearchResultsInterface {
        return $this->commandGetList->execute($searchCriteria);
    }
}
