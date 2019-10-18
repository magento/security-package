<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
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
    protected $_idFieldName = 'template_id';

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
                '(code = ' . $connection->quote($templateId) . ') AND ('
                    . 'adapter_code = ' . $connection->quote($adapterCode) . ' OR '
                    . 'adapter_code IS NULL'
                . ')'
            )
            ->order(new \Zend_Db_Expr('adapter_code IS NULL'));
    }
}
