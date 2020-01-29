<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model\Provider;

use Magento\Framework\App\ResponseInterface;

/**
 * Response provider in case of failure
 */
interface FailureProviderInterface
{
    /**
     * Handle reCaptcha failure
     * @param ResponseInterface $response
     * @return void
     */
    public function execute(ResponseInterface $response = null): void;
}
