<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Config\Backend;

use Magento\Framework\App\Config\Value;
use Magento\Framework\App\Config\Data\ProcessorInterface;
use Magento\Framework\Exception\ValidatorException;
use OTPHP\TOTPInterface;

class OtpWindow extends Value implements ProcessorInterface
{
    /**
     * @return int
     */
    private function getDefaultPeriod(): int
    {
        return TOTPInterface::DEFAULT_PERIOD;
    }

    /**
     * Process the value before saving.
     *
     * @param mixed $value The configuration value.
     * @return mixed The processed value.
     * @throws ValidatorException If the value is invalid.
     */
    public function processValue($value)
    {
        if (!is_numeric($value)) {
            throw new ValidatorException(__('The OTP window must be a numeric value.'));
        }
        $numericValue = (int) $value;
        return $numericValue;
    }

    /**
     * Validates the value before saving.
     *
     * @throws ValidatorException If the value is invalid.
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $period = $this->getDefaultPeriod();
        if (!is_numeric($value) || $value < 1 || $value >= $period) {
            throw new ValidatorException(__('Invalid OTP window value. It must be less than the OTP period value '.$period));
        }

        return parent::beforeSave();
    }
}
