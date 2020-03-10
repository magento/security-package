<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUi\Model;

/**
 * Extension point for adding UI config for concrete reCAPTCHA type
 *
 * @api
 * @see \Magento\ReCaptchaUi\Model\UiConfigResolver
 */
interface UiConfigProviderInterface
{
    /**
     * Return UI config for concrete reCAPTCHA type
     *
     * @return array
     */
    public function get(): array;
}
