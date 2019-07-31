<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierAsync\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;

class GetChannelExtensionAttributes
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
     * @param int $channelId
     * @return array
     */
    public function execute(int $channelId): array
    {
        $tableName = $this->resourceConnection->getTableName('msp_notifier_async_channel');
        $connection = $this->resourceConnection->getConnection();

        $qry = $connection->select()
            ->from($tableName, 'send_async')
            ->where('channel_id = ?', $channelId)
            ->limit(1);

        $res = $connection->fetchOne($qry);
        return [
            'send_async' => (string) ($res ? 1 : 0) // Must be a string to be correctly handled by data provider
        ];
    }
}
