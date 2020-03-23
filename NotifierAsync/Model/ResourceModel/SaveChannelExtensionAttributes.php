<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierAsync\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;

/**
 * Class for Save Channel Extension attributes
 */
class SaveChannelExtensionAttributes
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Save Channel Extension attributes
     *
     * @param int $channelId
     * @param array $data
     */
    public function execute(int $channelId, array $data): void
    {
        $tableName = $this->resourceConnection->getTableName('notifier_async_channel');
        $connection = $this->resourceConnection->getConnection();

        $connection->insertOnDuplicate(
            $tableName,
            [
                'send_async' => $data['send_async'] ?? false,
                'channel_id' => $channelId
            ],
            [
                'send_async'
            ]
        );
    }
}
