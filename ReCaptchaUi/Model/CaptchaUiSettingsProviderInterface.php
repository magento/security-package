<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUi\Model;

/**
 * Extension point of the UI setting for reCAPTCHA rendering
 *
 * @api
 */
interface CaptchaUiSettingsProviderInterface
{
    /**
     * Return layout UI setting for reCAPTCHA rendering
     *
     * @return array
     */
    public function get(): array;
}
