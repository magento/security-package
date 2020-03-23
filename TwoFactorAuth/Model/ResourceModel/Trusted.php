<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);


namespace Magento\TwoFactorAuth\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * @inheritdoc
 */
class Trusted extends AbstractDb
{
    /**
     * Trusted constructor.
     */
    protected function _construct()
    {
        $this->_init('tfa_trusted', 'trusted_id');
    }
}
