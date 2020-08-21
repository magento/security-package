<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUi\Model;

/**
 * Get reCAPTCHA Failure Messages from configuration.
 *
 * @api
 */
interface ErrorMessageConfigInterface
{
    /**
     * Get reCAPTCHA Technical Failure Message.
     *
     * @return string
     */
    public function getTechnicalFailureMessage(): string;

    /**
     * Get reCAPTCHA Validation Failure Message.
     *
     * @return string
     */
    public function getValidationFailureMessage(): string;
}
