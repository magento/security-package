<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierEvent\Model\ResourceModel\Rule;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use MSP\NotifierEvent\Model\ResourceModel\Rule;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = 'rule_id';

    protected function _construct()
    {
        $this->_init(
            \MSP\NotifierEvent\Model\Rule::class,
            Rule::class
        );
    }
}
