<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Country model
 */
class Country extends AbstractDb
{
    /**
     * Country constructor.
     */
    protected function _construct()
    {
        $this->_init('tfa_country_codes', 'country_id');
    }
}
