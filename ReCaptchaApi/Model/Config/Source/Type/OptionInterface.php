<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaApi\Model\Config\Source\Type;

/**
 * SPI for reCAPTCHA type options.
 *
 * @api
 */
interface OptionInterface
{
    /**
     * Get option label.
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * Get option value.
     *
     * @return string
     */
    public function getValue(): string;
}
