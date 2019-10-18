<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Notifier\Model\ResourceModel\Channel;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Notifier\Model\ResourceModel\Channel;
use Magento\NotifierApi\Api\Data\ChannelInterface;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Collection extends AbstractCollection
{
    /**
     * @var ID field name
     */
    protected $_idFieldName = ChannelInterface::ID;

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(
            \Magento\Notifier\Model\Channel::class,
            Channel::class
        );
    }
}
