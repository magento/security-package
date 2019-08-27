<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\Notifier\Model\ResourceModel\Channel;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use MSP\Notifier\Model\ResourceModel\Channel;

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
            \MSP\Notifier\Model\Channel::class,
            Channel::class
        );
    }
}
