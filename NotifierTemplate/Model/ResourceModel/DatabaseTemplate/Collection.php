<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplate\Model\ResourceModel\DatabaseTemplate;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\NotifierTemplate\Model\ResourceModel\DatabaseTemplate;
use Magento\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = DatabaseTemplateInterface::ID;

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(
            \Magento\NotifierTemplate\Model\DatabaseTemplate::class,
            DatabaseTemplate::class
        );
    }

    /**
     * Filter adapter candidates
     * @param string $adapterCode
     * @param string $templateId
     */
    public function filterAdapterCandidates(string $adapterCode, string $templateId): void
    {
        $connection = $this->getConnection();

        $this->getSelect()
            ->where(
                '(' . DatabaseTemplateInterface::CODE . ' = ' . $connection->quote($templateId) . ') AND ('
                    . DatabaseTemplateInterface::ADAPTER_CODE . ' = ' . $connection->quote($adapterCode) . ' OR '
                    . DatabaseTemplateInterface::ADAPTER_CODE . ' IS NULL'
                . ')'
            )
            ->order(new \Zend_Db_Expr(DatabaseTemplateInterface::ADAPTER_CODE . ' IS NULL'));
    }
}
