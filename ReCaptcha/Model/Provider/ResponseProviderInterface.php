<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model\Provider;

/**
 * ReCaptcha response provider
 */
interface ResponseProviderInterface
{
    /**
     * @return string
     */
    public function execute(): string;
}
