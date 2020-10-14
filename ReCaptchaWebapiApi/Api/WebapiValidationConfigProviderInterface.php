<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaWebapiApi\Api;

use Magento\ReCaptchaValidationApi\Api\Data\ValidationConfigInterface;
use Magento\ReCaptchaWebapiApi\Api\Data\EndpointInterface;

/**
 * Provides ReCaptcha validation config for an endpoint.
 *
 * Implement to control which web API endpoint need ReCaptcha validation.
 *
 * @api
 */
interface WebapiValidationConfigProviderInterface
{
    /**
     * Provides a validation config for an endpoint if it exists and validation is required.
     *
     * @param EndpointInterface $endpoint
     * @return ValidationConfigInterface|null
     */
    public function getConfigFor(
        EndpointInterface $endpoint
    ): ?ValidationConfigInterface;
}
