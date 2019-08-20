<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierTemplate\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use MSP\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class DatabaseTemplate extends AbstractDb
{
    /**
     * Template notifier base table
     */
    private const TABLE_NAME = 'msp_notifier_template';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(
            self::TABLE_NAME,
            DatabaseTemplateInterface::ID
        );
    }
}
