<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Config\Backend\Duo;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\TwoFactorAuth\Api\TfaInterface;

/**
 * Represent api_hostname field
 */
class ApiHostname extends Value
{
    /**
     * @inheritDoc
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        if ($value && !preg_match('%^[^./:]+\.duosecurity\.com$%', $value)) {
            throw new ValidatorException(__('Invalid API hostname.'));
        }

        return parent::beforeSave();
    }
}
