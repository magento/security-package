<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model\Provider\Failure;

/**
 * Redirection URL provider in case of failure
 */
interface RedirectUrlProviderInterface
{
    /**
     * Get redirection URL
     * @return string
     */
    public function execute(): string;
}
