<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\ResourceModel\Trusted;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\TwoFactorAuth\Model\ResourceModel\Trusted;

/**
 * Trusted devices collection
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = 'trusted_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Magento\TwoFactorAuth\Model\Trusted::class,
            Trusted::class
        );
    }
}
