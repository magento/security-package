<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model;

/**
 * SPI - Interface for recaptcha validation
 */
interface ValidateInterface
{
    /**
     * Parameter name for recaptcha response
     */
    public const PARAM_RECAPTCHA_RESPONSE = 'g-recaptcha-response';

    /**
     * Return true if reCaptcha validation has passed
     * @param string $reCaptchaResponse
     * @param string $remoteIp
     * @return bool
     */
    public function validate(string $reCaptchaResponse, string $remoteIp): bool;
}
