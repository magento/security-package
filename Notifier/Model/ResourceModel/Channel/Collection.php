<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Notifier\Model\ResourceModel\Channel;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Notifier\Model\ResourceModel\Channel;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Collection extends AbstractCollection
{
    /**
     * ID field name
     * @var string
     */
    protected $_idFieldName = 'channel_id';

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
