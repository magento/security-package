<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEvent\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\NotifierEventApi\Api\Data\RuleInterface;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Rule extends AbstractDb
{
    /**
     * Event notifier table name
     */
    private const TABLE_NAME = 'notifier_event_rule';

    protected function _construct()
    {
        $this->_init(
            self::TABLE_NAME,
            'rule_id'
        );
    }
}
