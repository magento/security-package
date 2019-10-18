<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEvent\Model\ResourceModel\Rule;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = \Magento\NotifierEventApi\Api\Data\RuleInterface::ID;

    protected function _construct()
    {
        $this->_init(
            \Magento\NotifierEvent\Model\Rule::class,
            \Magento\NotifierEvent\Model\ResourceModel\Rule::class
        );
    }
}
