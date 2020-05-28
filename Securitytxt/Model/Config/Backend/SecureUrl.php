<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Securitytxt\Model\Config\Backend;

use Magento\Framework\Validator\Exception as ValidatorException;
use Magento\Framework\App\Config\Value;

/**
 * Security.txt secure URL validator.
 */
class SecureUrl extends Value
{
    /**
     * Validate security.txt URL field before saving it.
     *
     * @return $this
     * @throws ValidatorException
     */
    public function beforeSave()
    {
        $url = $this->getValue();
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        $isValid = parse_url($url, PHP_URL_SCHEME) === 'https';
        if (!$isValid && $url !== '') {
            throw new ValidatorException(
                __('URL should be in correct format and must start with HTTPS.')
            );
        }
        return $this;
    }
}
