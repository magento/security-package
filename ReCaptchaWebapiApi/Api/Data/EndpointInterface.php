<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaWebapiApi\Api\Data;

/**
 * Requested endpoint info.
 *
 * @api
 */
interface EndpointInterface
{
    /**
     * Service class responsible for processing requests to the endpoint.
     *
     * @return string
     */
    public function getServiceClass(): string;

    /**
     * Service class method responsible for processing requests to the endpoint.
     *
     * @return string
     */
    public function getServiceMethod(): string;

    /**
     * Endpoint name.
     *
     * @return string
     */
    public function getName(): string;
}
